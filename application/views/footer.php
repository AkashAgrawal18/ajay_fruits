<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous">
</script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js" integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="<?= base_url('assets/lib/sweet-alerts/js/sweetalert.min.js') ?>"></script>
<script src="<?= base_url('assets/lib/sweet-alerts/js/custom-sweetalerts.js') ?>"></script>
<script src="<?= base_url('assets/lib/select2/dist/js/select2.min.js'); ?>"></script>
<script src="<?= base_url('assets/js/tablesearch.js') ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $('.select2').select2({
            // theme: "bootstrap-5",
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
        });

    });


    function selectRefresh() {
        $('.select2').select2({
            // theme: "bootstrap-5",
            tags: true,
            placeholder: $(this).data('placeholder'),
            // allowClear: true,
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        });
    }
</script>

<script type="text/javascript">
    // Global CSRF token injection - keeps every classic form submit and every
    // jQuery $.ajax POST call carrying a valid token without having to touch
    // each individual form/view. The token is read from a server-rendered
    // <meta> tag (see head.php) rather than the cookie, since the CSRF cookie
    // is HttpOnly (not readable from JS) - a fresh copy is embedded in every
    // page load, which is enough because CI issues a new token only after an
    // accepted POST, and a full page reload always follows a classic submit.
    (function() {
        function getCsrfTokenName() {
            var el = document.querySelector('meta[name="csrf-token-name"]');
            return el ? el.getAttribute('content') : '';
        }

        function getCsrfTokenValue() {
            var el = document.querySelector('meta[name="csrf-token-value"]');
            return el ? el.getAttribute('content') : '';
        }

        // CI regenerates the CSRF hash on every accepted POST (csrf_regenerate
        // = TRUE) and sets it via an HttpOnly cookie this JS can't read back.
        // That's harmless for a classic form submit (a full page reload
        // always follows, re-rendering a fresh <meta> tag) but breaks any
        // flow that fires more than one POST from the same page load without
        // reloading in between: the 1st POST still carries the token the page
        // was rendered with and succeeds, but the token it carries is now
        // stale for every POST after it, which the server rejects with a 403
        // (BUG-027 - this is what "picking a 2nd branch stops refreshing the
        // dropdowns" turned out to be). Endpoints that can be called
        // repeatedly without a reload (see Master::branch_scoped_options,
        // Sales::get_vourcher_accounts/get_reciept_accounts) echo the fresh
        // token pair back in their JSON body; call this after reading such a
        // response so the *next* POST uses it instead of the stale one.
        window.refreshCsrfToken = function(tokenName, tokenValue) {
            if (!tokenName || !tokenValue) return;
            var nameEl = document.querySelector('meta[name="csrf-token-name"]');
            var valueEl = document.querySelector('meta[name="csrf-token-value"]');
            if (nameEl) nameEl.setAttribute('content', tokenName);
            if (valueEl) valueEl.setAttribute('content', tokenValue);
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

        document.addEventListener('DOMContentLoaded', injectIntoAllForms);

        // Top up the token right before submit too (covers modals/dynamically-added forms).
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (form.tagName !== 'FORM') return;
            if (form.method && form.method.toLowerCase() !== 'post') return;
            var tokenName = getCsrfTokenName();
            var token = getCsrfTokenValue();
            if (tokenName && token) ensureTokenField(form, tokenName, token);
        }, true);

        // Auto-inject the token into every jQuery $.ajax POST call.
        if (window.jQuery) {
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

            // ...and pick the rotated token back up from ANY JSON response
            // that carries it, so a page firing several POSTs without
            // reloading keeps working. Endpoints opt in simply by including
            // csrf_token_name / csrf_token_value in their JSON; no call site
            // has to remember to do anything.
            jQuery(document).ajaxSuccess(function(event, xhr) {
                var payload = xhr.responseJSON;
                if (!payload && xhr.responseText) {
                    try {
                        payload = JSON.parse(xhr.responseText);
                    } catch (e) {
                        return;
                    }
                }
                if (payload && payload.csrf_token_name && payload.csrf_token_value) {
                    window.refreshCsrfToken(payload.csrf_token_name, payload.csrf_token_value);
                }
            });
        }
    })();
</script>

<script type="text/javascript">
    // Shared "branch cascading" helper: when a form's Branch <select> changes,
    // re-fetch whichever sibling dropdown/datalist (customer, staff, supplier,
    // item, group...) needs to be re-scoped to that branch, via
    // Master/branch_scoped_options. One implementation reused by every
    // add/edit form instead of a copy per page.
    //
    // All of a form's dropdowns are fetched in ONE request. Doing one request
    // per dropdown broke every branch change after the first: CI rotates the
    // CSRF token on each accepted POST (csrf_regenerate=TRUE), so N parallel
    // requests minted N different tokens and the client had no way to know
    // which one the browser's cookie kept - the next change then 403'd and
    // silently updated nothing (BUG-027). One request is also one round trip
    // instead of N. The token itself is picked back up globally by the
    // ajaxSuccess hook in the CSRF script above.
    window.BranchCascade = (function($) {
        var inFlight = {};

        // The element to show busy state on. A <datalist> is invisible, so
        // fall back to the <input list="..."> that actually drives it.
        function loadingElFor(cfg) {
            if (cfg.loadingTarget) return $(cfg.loadingTarget);
            if (cfg.mode === 'datalist') {
                var sels = [];
                $(cfg.target).each(function() {
                    if (this.id) sels.push('input[list="' + this.id + '"]');
                });
                if (sels.length) return $(sels.join(','));
            }
            return $(cfg.target);
        }

        function setLoading($targets, isLoading) {
            $targets.each(function() {
                var $el = $(this);
                if (isLoading) {
                    $el.prop('disabled', true);
                    if (!$el.data('bcSpinner')) {
                        var $spinner = $('<span class="spinner-border spinner-border-sm text-primary ms-1" role="status" aria-hidden="true"></span>');
                        $el.data('bcSpinner', $spinner);
                        $el.after($spinner);
                    }
                } else {
                    $el.prop('disabled', false);
                    var existing = $el.data('bcSpinner');
                    if (existing) {
                        existing.remove();
                        $el.removeData('bcSpinner');
                    }
                }
            });
        }

        function rebuildSelect($sel, list, cfg) {
            // .val() returns a string for single selects, an array (or null)
            // for multi-selects - normalize to an array either way so this
            // works for both (e.g. add_user.php's Group picker is multiple).
            var cur = $sel.val();
            var curArr = $.isArray(cur) ? cur : (cur ? [cur] : []);

            $sel.empty().append('<option value="">' + (cfg.placeholder || '') + '</option>');
            $.each(list, function(i, item) {
                $sel.append($('<option>', {
                    value: item[cfg.idField]
                }).text(cfg.labelFn(item)));
            });

            var stillValid = curArr.filter(function(v) {
                return $sel.find('option[value="' + v + '"]').length > 0;
            });
            $sel.val(stillValid);
            $sel.trigger('change');
        }

        function rebuildDatalist($dl, list, cfg) {
            $dl.empty();
            $.each(list, function(i, item) {
                var $opt = $('<option>', {
                    value: cfg.valueFn(item)
                });
                var attrs = cfg.attrsFn(item);
                for (var key in attrs) {
                    $opt.attr('data-' + key, attrs[key]);
                }
                $dl.append($opt);
            });
        }

        // targets: array of {
        //   listType, groupType, target (selector), mode: 'select'|'datalist',
        //   idField, labelFn, placeholder,   // mode: 'select'
        //   valueFn, attrsFn,                // mode: 'datalist'
        //   loadingTarget                    // optional, defaults sensibly
        // }
        // options.then: optional callback(branch_id) run AFTER the response is
        //   applied. Use it to chain any further POST this page needs on a
        //   branch change - firing it in parallel instead would re-introduce
        //   the multi-rotation CSRF race described above.
        function bind(branchSelector, targets, url, options) {
            options = options || {};

            $(document).on('change', branchSelector, function() {
                var branch_id = $(this).val();

                // One in-flight request per branch picker. Aborting the
                // previous one keeps a slow response for the branch selected
                // a moment ago from landing after - and overwriting - the
                // newer one.
                if (inFlight[branchSelector]) {
                    inFlight[branchSelector].abort();
                }

                var listTypes = [];
                var groupType;
                var $busy = $();

                $.each(targets, function(i, cfg) {
                    if ($.inArray(cfg.listType, listTypes) === -1) {
                        listTypes.push(cfg.listType);
                    }
                    if (cfg.groupType) groupType = cfg.groupType;
                    $busy = $busy.add(loadingElFor(cfg));
                });

                setLoading($busy, true);

                inFlight[branchSelector] = $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        list_types: listTypes,
                        group_type: groupType,
                        branch_id: branch_id
                    },
                    dataType: 'JSON'
                }).done(function(response) {
                    var lists = (response && response.lists) || {};
                    $.each(targets, function(i, cfg) {
                        var list = lists[cfg.listType] || [];
                        var $target = $(cfg.target);
                        if (cfg.mode === 'datalist') {
                            rebuildDatalist($target, list, cfg);
                        } else {
                            rebuildSelect($target, list, cfg);
                        }
                    });
                    // Runs only once the CSRF token from this response has
                    // been picked up, so a chained POST uses the fresh one.
                    if (typeof options.then === 'function') {
                        options.then(branch_id);
                    }
                }).always(function() {
                    setLoading($busy, false);
                    inFlight[branchSelector] = null;
                });
            });
        }

        return {
            bind: bind
        };
    })(jQuery);
</script>
</body>

</html>