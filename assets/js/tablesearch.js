/**
 * jQuery Table Search Plugin
 * Usage: $('#myTable').tableSearch(options)
 *
 * Options:
 *   inputSelector   - CSS selector ya jQuery object of search input (required)
 *   columns         - Array of column indexes to search in (default: all columns)
 *   minChars        - Minimum characters before search triggers (default: 0)
 *   noResultMsg     - Message shown when no results (default: 'No records found')
 *   onResult        - Callback function(visibleCount, totalCount) after each search
 *   highlightClass  - CSS class to apply on matched text (default: 'ts-highlight')
 *   highlight       - Enable text highlighting (default: false)
 *   caseSensitive   - Case sensitive search (default: false)
 *   debounce        - Debounce delay in ms (default: 200)
 */
 
;(function ($) {
    'use strict';
 
    var defaults = {
        inputSelector  : null,
        columns        : null,        // null = all columns
        minChars       : 0,
        noResultMsg    : 'No records found',
        onResult       : null,
        highlightClass : 'ts-highlight',
        highlight      : false,
        caseSensitive  : false,
        debounce       : 200,
    };
 
    // ---- helpers ----
 
    function debounce(fn, delay) {
        var timer;
        return function () {
            clearTimeout(timer);
            timer = setTimeout(fn.bind(this, arguments), delay);
        };
    }
 
    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
 
    function getCellText($td) {
        return $td.text().trim();
    }
 
    // ---- plugin ----
 
    $.fn.tableSearch = function (options) {
        var opts = $.extend({}, defaults, options);
 
        return this.each(function () {
            var $table  = $(this);
            var $tbody  = $table.find('tbody');
            var $input  = $(opts.inputSelector);
 
            if (!$input.length) {
                console.warn('[tableSearch] inputSelector not found:', opts.inputSelector);
                return;
            }
 
            // "No result" row — inject once
            var $noRow = $('<tr class="ts-no-result"><td colspan="99" class="text-center py-3 text-muted">' + opts.noResultMsg + '</td></tr>').hide();
            $tbody.append($noRow);
 
            // Remove previous highlight spans (clean re-run)
            function clearHighlights() {
                $tbody.find('.ts-hl-wrap').each(function () {
                    $(this).replaceWith($(this).text());
                });
            }
 
            function applyHighlight($row, term) {
                if (!term || !opts.highlight) return;
                var regex = new RegExp('(' + escapeRegex(term) + ')', opts.caseSensitive ? 'g' : 'gi');
                $row.find('td').each(function () {
                    var $td = $(this);
                    var html = $td.text().replace(regex, '<span class="ts-hl-wrap ' + opts.highlightClass + '">$1</span>');
                    $td.html(html);
                });
            }
 
            function doSearch(term) {
                clearHighlights();
 
                if (term.length < opts.minChars) {
                    $tbody.find('tr').not('.ts-no-result').show();
                    $noRow.hide();
                    if (typeof opts.onResult === 'function') {
                        var total = $tbody.find('tr').not('.ts-no-result').length;
                        opts.onResult(total, total);
                    }
                    return;
                }
 
                var compareStr = opts.caseSensitive ? term : term.toLowerCase();
                var $rows      = $tbody.find('tr').not('.ts-no-result');
                var visible    = 0;
 
                $rows.each(function () {
                    var $row  = $(this);
                    var $cells = opts.columns
                        ? $row.find('td').filter(function (i) { return opts.columns.indexOf(i) !== -1; })
                        : $row.find('td');
 
                    var matched = false;
                    $cells.each(function () {
                        var text = opts.caseSensitive ? getCellText($(this)) : getCellText($(this)).toLowerCase();
                        if (text.indexOf(compareStr) !== -1) {
                            matched = true;
                            return false; // break
                        }
                    });
 
                    if (matched) {
                        $row.show();
                        applyHighlight($row, term);
                        visible++;
                    } else {
                        $row.hide();
                    }
                });
 
                $noRow.toggle(visible === 0);
 
                if (typeof opts.onResult === 'function') {
                    opts.onResult(visible, $rows.length);
                }
            }
 
            // Attach event with debounce
            $input.on('input keyup', debounce(function () {
                var term = $input.val().trim();
                doSearch(term);
            }, opts.debounce));
 
            // Clear button support: trigger search on clear
            $input.on('search', function () {
                doSearch($input.val().trim());
            });
        });
    };
 
}(jQuery));