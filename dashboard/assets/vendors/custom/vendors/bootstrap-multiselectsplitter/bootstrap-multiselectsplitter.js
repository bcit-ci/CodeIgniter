/**
 * Bootstrap multiselectsplitter plugin
 * Version: 1.0.1
 * License: MIT
 * Homepage: https://github.com/poolerMF/bootstrap-multiselectsplitter
 */
+function ($) {
    'use strict';

    // CLASS DEFINITION
    // ===============================

    var MultiSelectSplitter = function (element, options) {
        this.init('multiselectsplitter', element, options);
    };

    MultiSelectSplitter.DEFAULTS = {
        selectSize: null,
        maxSelectSize: null,
        clearOnFirstChange: false,
        onlySameGroup: false,   // only if multiselect
        groupCounter: false,    // only if multiselect
        maximumSelected: null,  // only if multiselect, integer or function
        afterInitialize: null,
        maximumAlert: function (maximumSelected) {
            alert("Only " + maximumSelected + " values can be selected");
        },
        createFirstSelect: function (label, $originalSelect) {
            return '<option>' + label + '</option>';
        },
        createSecondSelect: function (label, $firstSelect) {
            return '<option>' + label + '</option>';
        },
        template: '<div class="row" data-multiselectsplitter-wrapper-selector>' +
        '<div class="col-xs-6 col-sm-6">' +
        '<select class="form-control" data-multiselectsplitter-firstselect-selector></select>' +
        '</div>' +
        ' <!-- Add the extra clearfix for only the required viewport -->' +
        '<div class="col-xs-6 col-sm-6">' +
        '<select class="form-control" data-multiselectsplitter-secondselect-selector></select>' +
        '</div>' +
        '</div>'
    };

    MultiSelectSplitter.prototype.init = function (type, element, options) {

        var self = this;

        self.type = type;
        self.last$ElementSelected = [];
        self.initialized = false;

        self.$element = $(element);
        self.$element.hide();

        self.options = $.extend({}, MultiSelectSplitter.DEFAULTS, options);

        // Add template.
        self.$element.after(self.options.template);

        // Define selected elements.
        self.$wrapper = self.$element.next('div[data-multiselectsplitter-wrapper-selector]');
        self.$firstSelect = $('select[data-multiselectsplitter-firstselect-selector]', self.$wrapper);
        self.$secondSelect = $('select[data-multiselectsplitter-secondselect-selector]', self.$wrapper);

        var optgroupCount = 0;
        var longestOptionCount = 0;

        if (self.$element.find('optgroup').length == 0) {
            return;
        }

        self.$element.find('optgroup').each(function () {

            var label = $(this).attr('label');
            var $option = $(self.options.createFirstSelect(label, self.$element));

            $option.val(label);
            $option.attr('data-current-label', $option.text());
            self.$firstSelect.append($option);

            var currentOptionCount = $(this).find('option').length;

            if (currentOptionCount > longestOptionCount) {
                longestOptionCount = currentOptionCount;
            }
            optgroupCount++;
        });

        // Define $firstSelect and $secondSelect size attribute
        var selectSize = Math.max(optgroupCount, longestOptionCount);
        selectSize = Math.min(selectSize, 10);
        if (self.options.selectSize) {
            selectSize = self.options.selectSize;
        } else if (self.options.maxSelectSize) {
            selectSize = Math.min(selectSize, self.options.maxSelectSize);
        }
        self.$firstSelect.attr('size', selectSize);
        self.$secondSelect.attr('size', selectSize);

        // Set multiple
        if (self.$element.attr('multiple')) {
            self.$secondSelect.attr('multiple', 'multiple');
        }

        // Set disabled
        if (self.$element.is(":disabled")) {
            self.disable();
        }

        // Define events.
        self.$firstSelect.on('change', $.proxy(self.updateParentCategory, self));
        self.$secondSelect.on('click change', $.proxy(self.updateChildCategory, self));

        self.update = function () {
            if (self.$element.find('option').length < 1) {
                return;
            }
            var selectedOptions = self.$element.find('option:selected:first');
            var selectedGroup;

            if (selectedOptions.length) {
                selectedGroup = selectedOptions.parent().attr('label');
            } else {
                selectedGroup = self.$element.find('option:first').parent().attr('label');
            }

            self.$firstSelect.find('option[value="' + selectedGroup + '"]').prop('selected', true);
            self.$firstSelect.trigger('change');
        };

        self.update();
        self.initialized = true;

        if (self.options.afterInitialize) {
            self.options.afterInitialize(self.$firstSelect, self.$secondSelect);
        }
    };

    MultiSelectSplitter.prototype.disable = function () {
        this.$secondSelect.prop('disabled', true);
        this.$firstSelect.prop('disabled', true);
    };

    MultiSelectSplitter.prototype.enable = function () {
        this.$secondSelect.prop('disabled', false);
        this.$firstSelect.prop('disabled', false);
    };

    MultiSelectSplitter.prototype.createSecondSelect = function () {

        var self = this;

        self.$secondSelect.empty();

        $.each(self.$element.find('optgroup[label="' + self.$firstSelect.val() + '"] option'), function (index, element) {
            var value = $(this).val();
            var label = $(this).text();

            var $option = $(self.options.createSecondSelect(label, self.$firstSelect));
            $option.val(value);

            $.each(self.$element.find('option:selected'), function (index, element) {
                if ($(element).val() == value) {
                    $option.prop('selected', true);
                }
            });

            self.$secondSelect.append($option);
        });
    };

    MultiSelectSplitter.prototype.updateParentCategory = function () {

        var self = this;

        self.last$ElementSelected = self.$element.find('option:selected');

        if (self.options.clearOnFirstChange && self.initialized) {
            self.$element.find('option:selected').prop('selected', false);
        }

        self.createSecondSelect();
        self.checkSelected();
        self.updateCounter();
    };

    MultiSelectSplitter.prototype.updateCounter = function () {

        var self = this;

        if (!self.$element.attr('multiple') || !self.options.groupCounter) {
            return;
        }

        $.each(self.$firstSelect.find('option'), function (index, element) {
            var originalLabel = $(element).val();
            var text = $(element).data('currentLabel');
            var count = self.$element.find('optgroup[label="' + originalLabel + '"] option:selected').length;

            if (count > 0) {
                text += ' (' + count + ')';
            }

            $(element).html(text);
        });
    };

    MultiSelectSplitter.prototype.checkSelected = function () {

        var self = this;

        if (!self.$element.attr('multiple') || !self.options.maximumSelected) {
            return;
        }

        var maximumSelected = 0;

        if (typeof self.options.maximumSelected == 'function') {
            maximumSelected = self.options.maximumSelected(self.$firstSelect, self.$secondSelect);
        } else {
            maximumSelected = self.options.maximumSelected;
        }

        if (maximumSelected < 1) {
            return;
        }

        var $actualElementSelected = self.$element.find('option:selected');

        if ($actualElementSelected.length > maximumSelected) {
            self.$firstSelect.find('option:selected').prop('selected', false);
            self.$secondSelect.find('option:selected').prop('selected', false);

            if (self.initialized) {
                self.$element.find('option:selected').prop('selected', false);
                self.last$ElementSelected.prop('selected', true);
            } else {
                // after init, there is no last value
                $.each(self.$element.find('option:selected'), function (index, element) {
                    if (index > maximumSelected - 1) {
                        $(element).prop('selected', false);
                    }
                });
            }

            var firstSelectedOptGroupLabel = self.last$ElementSelected.first().parent().attr('label');
            self.$firstSelect.find('option[value="' + firstSelectedOptGroupLabel + '"]').prop('selected', true);

            self.createSecondSelect();
            self.options.maximumAlert(maximumSelected);
        }
    };

    MultiSelectSplitter.prototype.basicUpdateChildCategory = function (event, isCtrlKey) {

        var self = this;

        self.last$ElementSelected = self.$element.find('option:selected');
        var childValues = self.$secondSelect.val();

        if (!$.isArray(childValues)) {
            childValues = [childValues];
        }

        var parentLabel = self.$firstSelect.val();
        var removeActualSelected = false;

        if (!self.$element.attr('multiple')) {
            removeActualSelected = true;
        } else {
            if (self.options.onlySameGroup) {
                $.each(self.$element.find('option:selected'), function (index, element) {
                    if ($(element).parent().attr('label') != parentLabel) {
                        removeActualSelected = true;
                        return false;
                    }
                });
            } else {
                if (!isCtrlKey) {
                    removeActualSelected = true;
                }
            }
        }

        if (removeActualSelected) {
            self.$element.find('option:selected').prop('selected', false);
        } else {
            $.each(self.$element.find('option:selected'), function (index, element) {
                if (parentLabel == $(element).parent().attr('label') && $.inArray($(element).val(), childValues) == -1) {
                    $(element).prop('selected', false);
                }
            });
        }

        $.each(childValues, function (index, value) {
            self.$element.find('option[value="' + value + '"]').prop('selected', true);
        });

        self.checkSelected();
        self.updateCounter();
        self.$element.trigger('change'); // Required for external plugins.
    };

    MultiSelectSplitter.prototype.updateChildCategory = function (event) {
        // There is no event.ctrlKey in event "change", so change function is called with timeout
        if (event.type == "change") {
            this.timeOut = setTimeout($.proxy(function () {
                this.basicUpdateChildCategory(event, event.ctrlKey);
            }, this), 10);
        } else if (event.type == "click") {
            clearTimeout(this.timeOut);
            this.basicUpdateChildCategory(event, event.ctrlKey)
        }
    };

    MultiSelectSplitter.prototype.destroy = function () {
        this.$wrapper.remove();
        this.$element.removeData(this.type);
        this.$element.show();
    };

    // PLUGIN DEFINITION
    // =========================

    function Plugin(option) {
        return this.each(function () {
            var $this = $(this);
            var data = $this.data('multiselectsplitter');
            var options = typeof option === 'object' && option;

            if (!data && option == 'destroy') {
                return;
            }
            if (!data) {
                $this.data('multiselectsplitter', ( data = new MultiSelectSplitter(this, options) ));
            }
            if (typeof option == 'string') {
                data[option]();
            }
        });
    }

    $.fn.multiselectsplitter = Plugin;
    $.fn.multiselectsplitter.Constructor = MultiSelectSplitter;
    $.fn.multiselectsplitter.VERSION = '1.0.1';

}(jQuery);