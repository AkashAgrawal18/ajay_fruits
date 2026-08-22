<script type="text/javascript">
    // Global CSRF token handling - keeps every classic form submit and every
    // jQuery $.ajax POST carrying a valid token, without having to touch each
    // individual form/view.
    //
    // This used to live inline in footer.php, which meant the pages that don't
    // include footer.php got none of it. afc_account.php (the shell behind 15
    // report screens, several of them superadmin-only) and truck_report.php
    // are standalone documents with their own <head>, so they shipped no token
    // at all and every POST out of them was rejected - not "worked on retry",
    // but 403 every single time. Pulling it into its own partial lets those
    // pages include the exact same implementation.
    //
    // The initial token is rendered straight from PHP rather than read out of
    // head.php's <meta> tags, so this works on a page that never included
    // head.php; the tags are still created/kept in sync for any other code
    // that reads them. Whatever the server considers current is echoed back on
    // every response in X-CSRF-Token-* headers (see
    // application/core/MY_Security.php) and absorbed below, so the page's copy
    // can never drift out of date - which is what produced the "403 the first
    // time, works after going back and retrying" behaviour across the app.
    (function() {
        // footer.php includes this, and a standalone page may include it again
        // after its own late-loaded jQuery. Installing twice would double up
        // the prefilter and append the token to each POST twice.
        if (window.__csrfGuardInstalled) return;
        window.__csrfGuardInstalled = true;

        var seedName = <?= json_encode($this->security->get_csrf_token_name()) ?>;
        var seedValue = <?= json_encode($this->security->get_csrf_hash()) ?>;

        function metaEl(name) {
            var el = document.querySelector('meta[name="' + name + '"]');
            if (!el) {
                el = document.createElement('meta');
                el.setAttribute('name', name);
                el.setAttribute('content', name === 'csrf-token-name' ? seedName : seedValue);
                (document.head || document.documentElement).appendChild(el);
            }
            return el;
        }

        function getCsrfTokenName() {
            return metaEl('csrf-token-name').getAttribute('content') || seedName;
        }

        function getCsrfTokenValue() {
            return metaEl('csrf-token-value').getAttribute('content') || seedValue;
        }

        // Points the page's token at a newer value.
        //
        // The app used to depend on this being called by hand, from the three
        // endpoints that echoed a rotated token in their JSON. Everywhere else
        // the page kept using the token it was rendered with, which CI had
        // already rotated away under it on the first POST - so the 2nd POST of
        // any page load 403'd, and only a reload ("go back and retry") cleared
        // it. csrf_regenerate is now FALSE so the token no longer rotates at
        // all, and the hooks below keep this in sync automatically from every
        // response. No call site has to remember to invoke it any more.
        window.refreshCsrfToken = function(tokenName, tokenValue) {
            if (!tokenName || !tokenValue) return;
            metaEl('csrf-token-name').setAttribute('content', tokenName);
            metaEl('csrf-token-value').setAttribute('content', tokenValue);
        };

        function ensureTokenField(form, tokenName, token) {
            var existing = form.querySelector('input[name="' + tokenName + '"]');
            if (existing) {
                existing.value = token;
            } else {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = tokenName;
                input.value = token;
                form.appendChild(input);
            }
        }

        function injectIntoAllForms() {
            var tokenName = getCsrfTokenName();
            var token = getCsrfTokenValue();
            if (!tokenName || !token) return;
            document.querySelectorAll('form').forEach(function(form) {
                if (form.method && form.method.toLowerCase() !== 'post') return;
                ensureTokenField(form, tokenName, token);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', injectIntoAllForms);
        } else {
            injectIntoAllForms();
        }

        // Top up the token right before submit too (covers modals/dynamically-added forms).
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form.tagName !== 'FORM') return;
            if (form.method && form.method.toLowerCase() !== 'post') return;
            var tokenName = getCsrfTokenName();
            var token = getCsrfTokenValue();
            if (tokenName && token) ensureTokenField(form, tokenName, token);
        }, true);

        function attachJQueryHooks() {
            if (!window.jQuery || attachJQueryHooks.done) return;
            attachJQueryHooks.done = true;

            // Auto-inject the token into every jQuery $.ajax POST call.
            jQuery.ajaxPrefilter(function(options) {
                if (!options.type || options.type.toUpperCase() !== 'POST') return;
                var tokenName = getCsrfTokenName();
                var token = getCsrfTokenValue();
                if (!tokenName || !token) return;

                if (window.FormData && options.data instanceof FormData) {
                    options.data.append(tokenName, token);
                } else if (typeof options.data === 'string') {
                    options.data += (options.data ? '&' : '') + tokenName + '=' + encodeURIComponent(token);
                } else if (typeof options.data === 'object' && options.data !== null) {
                    options.data[tokenName] = token;
                } else {
                    options.data = {};
                    options.data[tokenName] = token;
                }
            });

            // Pick the current token back up from EVERY response - success or
            // error. MY_Security::csrf_set_cookie() stamps X-CSRF-Token-Name /
            // X-CSRF-Token-Value onto every response the app sends, so this
            // needs no cooperation from the ~120 JSON endpoints; the
            // JSON-body form is still honoured for the few that send it.
            //
            // ajaxComplete (not ajaxSuccess) so a REJECTED request refreshes
            // the token too. That is what makes a stale token self-correcting
            // instead of something the user has to clear by going back and
            // retrying.
            function absorbToken(xhr) {
                var name = '',
                    value = '';
                try {
                    name = xhr.getResponseHeader('X-CSRF-Token-Name') || '';
                    value = xhr.getResponseHeader('X-CSRF-Token-Value') || '';
                } catch (e) {}

                if (!name || !value) {
                    var payload = xhr.responseJSON;
                    if (!payload && xhr.responseText) {
                        try {
                            payload = JSON.parse(xhr.responseText);
                        } catch (e) {
                            payload = null;
                        }
                    }
                    if (payload && payload.csrf_token_name && payload.csrf_token_value) {
                        name = payload.csrf_token_name;
                        value = payload.csrf_token_value;
                    }
                }

                if (!name || !value) return false;

                window.refreshCsrfToken(name, value);
                // Classic (non-AJAX) forms already rendered on the page carry
                // the token in a hidden input, so re-stamp those as well.
                injectIntoAllForms();
                return true;
            }

            jQuery(document).ajaxComplete(function(event, xhr) {
                absorbToken(xhr);
            });

            // A CSRF rejection happens during CI's bootstrap, before any
            // controller runs - nothing was read, written or sent. So the
            // request is safe to replay verbatim once we hold the good token,
            // and replaying it beats surfacing a 403 the user can only clear
            // by reloading. _csrfRetried caps it at one attempt, so a token
            // that is genuinely broken still fails loudly instead of looping.
            jQuery(document).ajaxError(function(event, xhr, settings) {
                if (xhr.status !== 403 || settings._csrfRetried) return;

                var payload = xhr.responseJSON;
                if (!payload && xhr.responseText) {
                    try {
                        payload = JSON.parse(xhr.responseText);
                    } catch (e) {
                        return;
                    }
                }
                if (!payload || payload.csrf_error !== true) return;

                // ajaxComplete already ran absorbToken for this response;
                // repeating it is harmless and keeps this correct regardless
                // of handler ordering.
                absorbToken(xhr);

                var retry = jQuery.extend({}, settings, {
                    _csrfRetried: true
                });

                // The prefilter appended the dead token to settings.data on
                // the way out - strip it so the fresh one isn't a duplicate.
                var tokenName = getCsrfTokenName();
                if (tokenName) {
                    if (window.FormData && retry.data instanceof FormData) {
                        if (typeof retry.data.delete === 'function') {
                            retry.data.delete(tokenName);
                        }
                    } else if (typeof retry.data === 'string') {
                        retry.data = retry.data.split('&').filter(function(pair) {
                            return pair.indexOf(tokenName + '=') !== 0;
                        }).join('&');
                    } else if (typeof retry.data === 'object' && retry.data !== null) {
                        delete retry.data[tokenName];
                    }
                }

                jQuery.ajax(retry);
            });
        }

        // jQuery is loaded before this partial on most pages, but the
        // standalone reports pull it in at the very bottom of the document -
        // so try now, and again once the rest of the page has parsed.
        attachJQueryHooks();
        if (!attachJQueryHooks.done) {
            document.addEventListener('DOMContentLoaded', attachJQueryHooks);
            window.addEventListener('load', attachJQueryHooks);
        }
    })();
</script>
