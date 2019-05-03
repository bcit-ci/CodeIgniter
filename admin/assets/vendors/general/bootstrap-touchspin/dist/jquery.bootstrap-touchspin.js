/*
 *  Bootstrap TouchSpin - v4.2.5
 *  A mobile and touch friendly input spinner component for Bootstrap 3 & 4.
 *  http://www.virtuosoft.eu/code/bootstrap-touchspin/
 *
 *  Made by István Ujj-Mészáros
 *  Under Apache License v2.0 License
 */
(function(factory) {
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if (typeof module === 'object' && module.exports) {
    module.exports = function(root, jQuery) {
      if (jQuery === undefined) {
        if (typeof window !== 'undefined') {
          jQuery = require('jquery');
        }
        else {
          jQuery = require('jquery')(root);
        }
      }
      factory(jQuery);
      return jQuery;
    };
  } else {
    factory(jQuery);
  }
}(function($) {
  'use strict';

  var _currentSpinnerId = 0;

  $.fn.TouchSpin = function(options) {

    var defaults = {
      min: 0, // If null, there is no minimum enforced
      max: 100, // If null, there is no maximum enforced
      initval: '',
      replacementval: '',
      step: 1,
      decimals: 0,
      stepinterval: 100,
      forcestepdivisibility: 'round', // none | floor | round | ceil
      stepintervaldelay: 500,
      verticalbuttons: false,
      verticalup: '+',
      verticaldown: '-',
      verticalupclass: '',
      verticaldownclass: '',
      prefix: '',
      postfix: '',
      prefix_extraclass: '',
      postfix_extraclass: '',
      booster: true,
      boostat: 10,
      maxboostedstep: false,
      mousewheel: true,
      buttondown_class: 'btn btn-primary',
      buttonup_class: 'btn btn-primary',
      buttondown_txt: '-',
      buttonup_txt: '+',
      callback_before_calculation: function(value) {
        return value;
      },
      callback_after_calculation: function(value) {
        return value;
      }
    };

    var attributeMap = {
      min: 'min',
      max: 'max',
      initval: 'init-val',
      replacementval: 'replacement-val',
      step: 'step',
      decimals: 'decimals',
      stepinterval: 'step-interval',
      verticalbuttons: 'vertical-buttons',
      verticalupclass: 'vertical-up-class',
      verticaldownclass: 'vertical-down-class',
      forcestepdivisibility: 'force-step-divisibility',
      stepintervaldelay: 'step-interval-delay',
      prefix: 'prefix',
      postfix: 'postfix',
      prefix_extraclass: 'prefix-extra-class',
      postfix_extraclass: 'postfix-extra-class',
      booster: 'booster',
      boostat: 'boostat',
      maxboostedstep: 'max-boosted-step',
      mousewheel: 'mouse-wheel',
      buttondown_class: 'button-down-class',
      buttonup_class: 'button-up-class',
      buttondown_txt: 'button-down-txt',
      buttonup_txt: 'button-up-txt'
    };

    return this.each(function() {

      var settings,
        originalinput = $(this),
        originalinput_data = originalinput.data(),
        _detached_prefix,
        _detached_postfix,
        container,
        elements,
        value,
        downSpinTimer,
        upSpinTimer,
        downDelayTimeout,
        upDelayTimeout,
        spincount = 0,
        spinning = false;

      init();

      function init() {
        if (originalinput.data('alreadyinitialized')) {
          return;
        }

        originalinput.data('alreadyinitialized', true);
        _currentSpinnerId += 1;
        originalinput.data('spinnerid', _currentSpinnerId);

        if (!originalinput.is('input')) {
          console.log('Must be an input.');
          return;
        }

        _initSettings();
        _setInitval();
        _checkValue();
        _buildHtml();
        _initElements();
        _hideEmptyPrefixPostfix();
        _bindEvents();
        _bindEventsInterface();
      }

      function _setInitval() {
        if (settings.initval !== '' && originalinput.val() === '') {
          originalinput.val(settings.initval);
        }
      }

      function changeSettings(newsettings) {
        _updateSettings(newsettings);
        _checkValue();

        var value = elements.input.val();

        if (value !== '') {
          value = Number(settings.callback_before_calculation(elements.input.val()));
          elements.input.val(settings.callback_after_calculation(Number(value).toFixed(settings.decimals)));
        }
      }

      function _initSettings() {
        settings = $.extend({}, defaults, originalinput_data, _parseAttributes(), options);
      }

      function _parseAttributes() {
        var data = {};
        $.each(attributeMap, function(key, value) {
          var attrName = 'bts-' + value + '';
          if (originalinput.is('[data-' + attrName + ']')) {
            data[key] = originalinput.data(attrName);
          }
        });
        return data;
      }

      function _destroy() {
        var $parent = originalinput.parent();

        stopSpin();

        originalinput.off('.touchspin');

        if ($parent.hasClass('bootstrap-touchspin-injected')) {
          originalinput.siblings().remove();
          originalinput.unwrap();
        }
        else {
          $('.bootstrap-touchspin-injected', $parent).remove();
          $parent.removeClass('bootstrap-touchspin');
        }

        originalinput.data('alreadyinitialized', false);
      }

      function _updateSettings(newsettings) {
        settings = $.extend({}, settings, newsettings);

        // Update postfix and prefix texts if those settings were changed.
        if (newsettings.postfix) {
          var $postfix = originalinput.parent().find('.bootstrap-touchspin-postfix');

          if ($postfix.length === 0) {
            _detached_postfix.insertAfter(originalinput);
          }

          originalinput.parent().find('.bootstrap-touchspin-postfix .input-group-text').text(newsettings.postfix);
        }

        if (newsettings.prefix) {
          var $prefix = originalinput.parent().find('.bootstrap-touchspin-prefix');

          if ($prefix.length === 0) {
            _detached_prefix.insertBefore(originalinput);
          }

          originalinput.parent().find('.bootstrap-touchspin-prefix .input-group-text').text(newsettings.prefix);
        }

        _hideEmptyPrefixPostfix();
      }

      function _buildHtml() {
        var initval = originalinput.val(),
          parentelement = originalinput.parent();

        if (initval !== '') {
          initval = settings.callback_after_calculation(Number(initval).toFixed(settings.decimals));
        }

        originalinput.data('initvalue', initval).val(initval);
        originalinput.addClass('form-control');

        if (parentelement.hasClass('input-group')) {
          _advanceInputGroup(parentelement);
        }
        else {
          _buildInputGroup();
        }
      }

      function _advanceInputGroup(parentelement) {
        parentelement.addClass('bootstrap-touchspin');

        var prev = originalinput.prev(),
          next = originalinput.next();

        var downhtml,
          uphtml,
          prefixhtml = '<span class="input-group-addon input-group-prepend bootstrap-touchspin-prefix input-group-prepend bootstrap-touchspin-injected"><span class="input-group-text">' + settings.prefix + '</span></span>',
          postfixhtml = '<span class="input-group-addon input-group-append bootstrap-touchspin-postfix input-group-append bootstrap-touchspin-injected"><span class="input-group-text">' + settings.postfix + '</span></span>';

        if (prev.hasClass('input-group-btn') || prev.hasClass('input-group-prepend')) {
          downhtml = '<button class="' + settings.buttondown_class + ' bootstrap-touchspin-down bootstrap-touchspin-injected" type="button">' + settings.buttondown_txt + '</button>';
          prev.append(downhtml);
        }
        else {
          downhtml = '<span class="input-group-btn input-group-prepend bootstrap-touchspin-injected"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button></span>';
          $(downhtml).insertBefore(originalinput);
        }

        if (next.hasClass('input-group-btn') || next.hasClass('input-group-append')) {
          uphtml = '<button class="' + settings.buttonup_class + ' bootstrap-touchspin-up bootstrap-touchspin-injected" type="button">' + settings.buttonup_txt + '</button>';
          next.prepend(uphtml);
        }
        else {
          uphtml = '<span class="input-group-btn input-group-append bootstrap-touchspin-injected"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button></span>';
          $(uphtml).insertAfter(originalinput);
        }

        $(prefixhtml).insertBefore(originalinput);
        $(postfixhtml).insertAfter(originalinput);

        container = parentelement;
      }

      function _buildInputGroup() {
        var html;

        var inputGroupSize = '';
        if (originalinput.hasClass('input-sm')) {
          inputGroupSize = 'input-group-sm';
        }

        if (originalinput.hasClass('input-lg')) {
          inputGroupSize = 'input-group-lg';
        }

        if (settings.verticalbuttons) {
          html = '<div class="input-group ' + inputGroupSize + ' bootstrap-touchspin bootstrap-touchspin-injected"><span class="input-group-addon input-group-prepend bootstrap-touchspin-prefix"><span class="input-group-text">' + settings.prefix + '</span></span><span class="input-group-addon bootstrap-touchspin-postfix input-group-append"><span class="input-group-text">' + settings.postfix + '</span></span><span class="input-group-btn-vertical"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-up ' + settings.verticalupclass + '" type="button">' + settings.verticalup + '</button><button class="' + settings.buttonup_class + ' bootstrap-touchspin-down ' + settings.verticaldownclass + '" type="button">' + settings.verticaldown + '</button></span></div>';
        }
        else {
          html = '<div class="input-group bootstrap-touchspin bootstrap-touchspin-injected"><span class="input-group-btn input-group-prepend"><button class="' + settings.buttondown_class + ' bootstrap-touchspin-down" type="button">' + settings.buttondown_txt + '</button></span><span class="input-group-addon bootstrap-touchspin-prefix input-group-prepend"><span class="input-group-text">' + settings.prefix + '</span></span><span class="input-group-addon bootstrap-touchspin-postfix input-group-append"><span class="input-group-text">' + settings.postfix + '</span></span><span class="input-group-btn input-group-append"><button class="' + settings.buttonup_class + ' bootstrap-touchspin-up" type="button">' + settings.buttonup_txt + '</button></span></div>';
        }

        container = $(html).insertBefore(originalinput);

        $('.bootstrap-touchspin-prefix', container).after(originalinput);

        if (originalinput.hasClass('input-sm')) {
          container.addClass('input-group-sm');
        }
        else if (originalinput.hasClass('input-lg')) {
          container.addClass('input-group-lg');
        }
      }

      function _initElements() {
        elements = {
          down: $('.bootstrap-touchspin-down', container),
          up: $('.bootstrap-touchspin-up', container),
          input: $('input', container),
          prefix: $('.bootstrap-touchspin-prefix', container).addClass(settings.prefix_extraclass),
          postfix: $('.bootstrap-touchspin-postfix', container).addClass(settings.postfix_extraclass)
        };
      }

      function _hideEmptyPrefixPostfix() {
        if (settings.prefix === '') {
          _detached_prefix = elements.prefix.detach();
        }

        if (settings.postfix === '') {
          _detached_postfix = elements.postfix.detach();
        }
      }

      function _bindEvents() {
        originalinput.on('keydown.touchspin', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
          else if (code === 40) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        originalinput.on('keyup.touchspin', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 38) {
            stopSpin();
          }
          else if (code === 40) {
            stopSpin();
          }
        });

        originalinput.on('blur.touchspin', function() {
          _checkValue();
          originalinput.val(settings.callback_after_calculation(originalinput.val()));
        });

        elements.down.on('keydown', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'down') {
              downOnce();
              startDownSpin();
            }
            ev.preventDefault();
          }
        });

        elements.down.on('keyup.touchspin', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.up.on('keydown.touchspin', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            if (spinning !== 'up') {
              upOnce();
              startUpSpin();
            }
            ev.preventDefault();
          }
        });

        elements.up.on('keyup.touchspin', function(ev) {
          var code = ev.keyCode || ev.which;

          if (code === 32 || code === 13) {
            stopSpin();
          }
        });

        elements.down.on('mousedown.touchspin', function(ev) {
          elements.down.off('touchstart.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          downOnce();
          startDownSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.down.on('touchstart.touchspin', function(ev) {
          elements.down.off('mousedown.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          downOnce();
          startDownSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mousedown.touchspin', function(ev) {
          elements.up.off('touchstart.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          upOnce();
          startUpSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('touchstart.touchspin', function(ev) {
          elements.up.off('mousedown.touchspin');  // android 4 workaround

          if (originalinput.is(':disabled')) {
            return;
          }

          upOnce();
          startUpSpin();

          ev.preventDefault();
          ev.stopPropagation();
        });

        elements.up.on('mouseup.touchspin mouseout.touchspin touchleave.touchspin touchend.touchspin touchcancel.touchspin', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mouseup.touchspin mouseout.touchspin touchleave.touchspin touchend.touchspin touchcancel.touchspin', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          stopSpin();
        });

        elements.down.on('mousemove.touchspin touchmove.touchspin', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        elements.up.on('mousemove.touchspin touchmove.touchspin', function(ev) {
          if (!spinning) {
            return;
          }

          ev.stopPropagation();
          ev.preventDefault();
        });

        originalinput.on('mousewheel.touchspin DOMMouseScroll.touchspin', function(ev) {
          if (!settings.mousewheel || !originalinput.is(':focus')) {
            return;
          }

          var delta = ev.originalEvent.wheelDelta || -ev.originalEvent.deltaY || -ev.originalEvent.detail;

          ev.stopPropagation();
          ev.preventDefault();

          if (delta < 0) {
            downOnce();
          }
          else {
            upOnce();
          }
        });
      }

      function _bindEventsInterface() {
        originalinput.on('touchspin.destroy', function() {
          _destroy();
        });

        originalinput.on('touchspin.uponce', function() {
          stopSpin();
          upOnce();
        });

        originalinput.on('touchspin.downonce', function() {
          stopSpin();
          downOnce();
        });

        originalinput.on('touchspin.startupspin', function() {
          startUpSpin();
        });

        originalinput.on('touchspin.startdownspin', function() {
          startDownSpin();
        });

        originalinput.on('touchspin.stopspin', function() {
          stopSpin();
        });

        originalinput.on('touchspin.updatesettings', function(e, newsettings) {
          changeSettings(newsettings);
        });
      }

      function _forcestepdivisibility(value) {
        switch (settings.forcestepdivisibility) {
          case 'round':
            return (Math.round(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'floor':
            return (Math.floor(value / settings.step) * settings.step).toFixed(settings.decimals);
          case 'ceil':
            return (Math.ceil(value / settings.step) * settings.step).toFixed(settings.decimals);
          default:
            return value;
        }
      }

      function _checkValue() {
        var val, parsedval, returnval;

        val = settings.callback_before_calculation(originalinput.val());

        if (val === '') {
          if (settings.replacementval !== '') {
            originalinput.val(settings.replacementval);
            originalinput.trigger('change');
          }
          return;
        }

        if (settings.decimals > 0 && val === '.') {
          return;
        }

        parsedval = parseFloat(val);

        if (isNaN(parsedval)) {
          if (settings.replacementval !== '') {
            parsedval = settings.replacementval;
          }
          else {
            parsedval = 0;
          }
        }

        returnval = parsedval;

        if (parsedval.toString() !== val) {
          returnval = parsedval;
        }

        if ((settings.min !== null) && (parsedval < settings.min)) {
          returnval = settings.min;
        }

        if ((settings.max !== null) && (parsedval > settings.max)) {
          returnval = settings.max;
        }

        returnval = _forcestepdivisibility(returnval);

        if (Number(val).toString() !== returnval.toString()) {
          originalinput.val(returnval);
          originalinput.trigger('change');
        }
      }

      function _getBoostedStep() {
        if (!settings.booster) {
          return settings.step;
        }
        else {
          var boosted = Math.pow(2, Math.floor(spincount / settings.boostat)) * settings.step;

          if (settings.maxboostedstep) {
            if (boosted > settings.maxboostedstep) {
              boosted = settings.maxboostedstep;
              value = Math.round((value / boosted)) * boosted;
            }
          }

          return Math.max(settings.step, boosted);
        }
      }

      function upOnce() {
        _checkValue();

        value = parseFloat(settings.callback_before_calculation(elements.input.val()));
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
          boostedstep = _getBoostedStep();

        value = value + boostedstep;

        if ((settings.max !== null) && (value > settings.max)) {
          value = settings.max;
          originalinput.trigger('touchspin.on.max');
          stopSpin();
        }

        elements.input.val(settings.callback_after_calculation(Number(value).toFixed(settings.decimals)));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function downOnce() {
        _checkValue();

        value = parseFloat(settings.callback_before_calculation(elements.input.val()));
        if (isNaN(value)) {
          value = 0;
        }

        var initvalue = value,
          boostedstep = _getBoostedStep();

        value = value - boostedstep;

        if ((settings.min !== null) && (value < settings.min)) {
          value = settings.min;
          originalinput.trigger('touchspin.on.min');
          stopSpin();
        }

        elements.input.val(settings.callback_after_calculation(Number(value).toFixed(settings.decimals)));

        if (initvalue !== value) {
          originalinput.trigger('change');
        }
      }

      function startDownSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'down';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startdownspin');

        downDelayTimeout = setTimeout(function() {
          downSpinTimer = setInterval(function() {
            spincount++;
            downOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function startUpSpin() {
        stopSpin();

        spincount = 0;
        spinning = 'up';

        originalinput.trigger('touchspin.on.startspin');
        originalinput.trigger('touchspin.on.startupspin');

        upDelayTimeout = setTimeout(function() {
          upSpinTimer = setInterval(function() {
            spincount++;
            upOnce();
          }, settings.stepinterval);
        }, settings.stepintervaldelay);
      }

      function stopSpin() {
        clearTimeout(downDelayTimeout);
        clearTimeout(upDelayTimeout);
        clearInterval(downSpinTimer);
        clearInterval(upSpinTimer);

        switch (spinning) {
          case 'up':
            originalinput.trigger('touchspin.on.stopupspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
          case 'down':
            originalinput.trigger('touchspin.on.stopdownspin');
            originalinput.trigger('touchspin.on.stopspin');
            break;
        }

        spincount = 0;
        spinning = false;
      }

    });

  };

}));
