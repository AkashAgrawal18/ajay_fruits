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
</body>

</html>