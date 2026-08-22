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

<?php $this->view('csrf'); ?>

<script type="text/javascript">
    // Busy overlay for ordinary (non-AJAX) form submits.
    //
    // Receipt / payment / voucher saves post to a controller that writes a row
    // and updates a balance per line, then redirects back. That round trip is
    // slow and, until it finishes, nothing on screen changes - so the save
    // looks like it did nothing, and a second impatient click posts the whole
    // form again.
    (function() {
        var overlay = null;

        function build() {
            if (overlay) return overlay;
            overlay = document.createElement('div');
            overlay.id = 'app-busy';
            overlay.setAttribute('role', 'status');
            overlay.setAttribute('aria-live', 'polite');
            overlay.style.cssText = [
                'position:fixed', 'inset:0', 'z-index:20000',
                'display:none', 'align-items:center', 'justify-content:center',
                'flex-direction:column', 'gap:.75rem',
                'background:rgba(255,255,255,.72)',
                '-webkit-backdrop-filter:blur(1px)', 'backdrop-filter:blur(1px)'
            ].join(';');
            overlay.innerHTML =
                '<div class="spinner-border text-primary" style="width:2.5rem;height:2.5rem" aria-hidden="true"></div>' +
                '<div id="app-busy-text" style="font-weight:600;color:#333"></div>';
            document.body.appendChild(overlay);
            return overlay;
        }

        function show(message) {
            var el = build();
            el.querySelector('#app-busy-text').textContent = message;
            el.style.display = 'flex';
        }

        function hide() {
            if (overlay) overlay.style.display = 'none';
            // Let the form be submitted again - the navigation it was waiting
            // on is over (or never happened).
            document.querySelectorAll('form[data-busy="1"]').forEach(function(f) {
                f.removeAttribute('data-busy');
            });
        }

        function labelFor(form) {
            var action = (form.getAttribute('action') || '').toLowerCase();
            return /insert|update|delete|import|save/.test(action) ? 'Saving, please wait...' : 'Loading, please wait...';
        }

        // Bubble phase, deliberately: every AJAX form in this app binds its
        // handler straight to the <form> with jQuery's .submit() and calls
        // preventDefault() there, so by the time the event reaches document
        // those are already flagged as cancelled. Skipping them matters - an
        // AJAX submit never navigates, so the overlay would stay up for good.
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if (form.method && form.method.toLowerCase() !== 'post') return;
            if (e.defaultPrevented) return;

            if (form.getAttribute('data-busy') === '1') {
                // Already on its way - swallow the repeat click rather than
                // posting the same receipt a second time.
                e.preventDefault();
                return;
            }

            form.setAttribute('data-busy', '1');
            show(labelFor(form));
        });

        // Coming back via the browser's Back button can restore this page from
        // the bfcache with the overlay still showing.
        window.addEventListener('pageshow', hide);

        // Belt and braces: if something did turn the submit into an AJAX call
        // after all, drop the overlay when that call settles.
        if (window.jQuery) {
            jQuery(document).ajaxStop(hide);
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
    // per dropdown broke every branch change after the first: CI rotated the
    // CSRF token on each accepted POST (csrf_regenerate was TRUE then), so N
    // parallel requests minted N different tokens and the client had no way to
    // know which one the browser's cookie kept - the next change then 403'd and
    // silently updated nothing (BUG-027). csrf_regenerate is FALSE now so that
    // race is gone, but one round trip still beats N.
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
<script type="text/javascript">
    // Shared reveal popup behind the superadmin "view password" buttons (staff
    // list, branch list, application settings). swal() puts `text` through
    // textContent with no pre-wrap, so newlines in it collapse into one run-on
    // line - the labelled rows have to be built as a node instead.
    //
    // Values go in via textContent, so nothing stored in a login id or
    // password can inject markup here.
    window.swalCredentials = function(title, pairs) {
        var box = document.createElement('div');
        box.style.textAlign = 'left';

        pairs.forEach(function(pair) {
            var row = document.createElement('div'),
                lbl = document.createElement('span'),
                val = document.createElement('strong');

            row.style.marginBottom = '6px';
            lbl.textContent = pair[0] + ': ';
            lbl.style.color = '#797979';
            val.textContent = pair[1] || '-';
            val.style.fontFamily = 'monospace';
            val.style.fontSize = '17px';

            row.appendChild(lbl);
            row.appendChild(val);
            box.appendChild(row);
        });

        swal({
            title: title,
            content: box,
            icon: "info"
        });
    };
</script>
</body>

</html>