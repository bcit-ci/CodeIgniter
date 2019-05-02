$(function () {
  'use strict';

  var maxlengthInput;

  module('maxlength', {
    setup: function () {
      maxlengthInput = $('<input type="text" maxlength="10" />')
        .appendTo('#qunit-fixture');

      maxlengthInput.maxlength();
    },
    teardown: function () {
      $('.bootstrap-maxlength').remove();
      $('#qunit-fixture').empty();
    }
  });

  test('Maxlength is displayed correctly', function () {
    maxlengthInput.focus();
    ok($('.bootstrap-maxlength').length, 'maxlength was inserted');
  });

  test('Maxlength is visible on focus', function () {
    maxlengthInput.focus();
    ok($('.bootstrap-maxlength').is(':visible'), 'Maxlength is visible');
  });

  test('Maxlength is removed on blur', function () {
    maxlengthInput.maxlength().focus().blur();
    ok(!$('.bootstrap-maxlength').length, 'Maxlength is removed on blur');
  });

  test('Maxlength updates the maxlength', function () {
    maxlengthInput.focus();

    // Change the maxlength attribute
    maxlengthInput.blur().attr('maxlength', '142').focus();

    ok($('.bootstrap-maxlength').html() === '0 / 142', 'Maxlength updated the field');

  });

  test('Removing an element with the maxlength removes the maxlength if any.', function () {
    maxlengthInput.maxlength().focus();
    maxlengthInput.remove();
    ok($('.bootstrap-maxlength').length === 0, 'Maxlength field removed with the input');

  });

  test('The focus event is triggered multiple times without a blur', function () {
    maxlengthInput.focus().focus().focus().focus();
    ok($('.bootstrap-maxlength').length === 1, 'Maxlength visualized only once after multiple focuses');
  });

  module('textarea', {
    setup: function () {
      maxlengthInput = $('<textarea maxlength="10"></textarea>')
        .appendTo('#qunit-fixture');

      maxlengthInput.maxlength();
    },
    teardown: function () {
      $('.bootstrap-maxlength').remove();
      $('#qunit-fixture').empty();
    }
  });

  test('Newlines are counted twice', function () {
    maxlengthInput.val('t\r\nt');

    maxlengthInput.maxlength();
    maxlengthInput.focus();

    ok($('.bootstrap-maxlength').html() === '4 / 10', 'Current length is: ' + $('.bootstrap-maxlength').html() + '. Expected 4 / 10.');
  });

  test('Message can be a customizable function', function () {
    $('.bootstrap-maxlength').remove();
    $('#qunit-fixture').empty();
    maxlengthInput = $('<input type="text" maxlength="10" />').appendTo('#qunit-fixture');
    maxlengthInput.maxlength({
      message: function (currentText, maxLength) {
        return '' + (currentText.length * 8) + ' Bytes / ' + (maxLength * 8) + ' Bytes';
      }
    });
    maxlengthInput.val('Test!');
    maxlengthInput.focus();

    ok($('.bootstrap-maxlength').html() === '40 Bytes / 80 Bytes', 'Message override is not functioning properly');
  });

  test('Message can be a customizable string', function () {
    $('.bootstrap-maxlength').remove();
    $('#qunit-fixture').empty();
    maxlengthInput = $('<input type="text" maxlength="10" />').appendTo('#qunit-fixture');
    maxlengthInput.maxlength({
      message: 'You have typed %charsTyped% chars, %charsRemaining% of %charsTotal% remaining'
    });
    maxlengthInput.val('Testing');
    maxlengthInput.focus();

    ok($('.bootstrap-maxlength').html() === 'You have typed 7 chars, 3 of 10 remaining', 'Message override is not functioning properly');
  });

  module('textarea', {
    setup: function () {
      maxlengthInput = $('<textarea maxlength="10"></textarea>')
        .appendTo('#qunit-fixture');

      maxlengthInput.maxlength({ twoCharLinebreak: false });
    },
    teardown: function () {
      $('.bootstrap-maxlength').remove();
      $('#qunit-fixture').empty();
    }
  });

  test('Newlines are not counted twice', function () {
    maxlengthInput.val('t\r\nt');

    maxlengthInput.maxlength({ twoCharLinebreak: false });
    maxlengthInput.focus();

    ok($('.bootstrap-maxlength').html() === '3 / 10', 'Current length is: ' + $('.bootstrap-maxlength').html() + '. Expected 3 / 10.');

  });

  module('overmax', {
    setup: function () {
      maxlengthInput = $('<input type="text" maxlength="10" />')
        .appendTo('#qunit-fixture');

      maxlengthInput.maxlength({ allowOverMax: true });
    },
    teardown: function () {
      $('.bootstrap-maxlength').remove();
      $('#qunit-fixture').empty();
    }
  });

  test('Allows over maxlength', function () {
    maxlengthInput.val('this is over the maxlength');
    maxlengthInput.focus();

    ok($('.bootstrap-maxlength').html() === '26 / 10', 'Current length is: ' + $('.bootstrap-maxlength').html() + '. Expected 26 / 10.');
  });

  test('Adds overmax class to element', function () {
    maxlengthInput.val('this is over the maxlength');
    maxlengthInput.focus();

    ok(maxlengthInput.hasClass('overmax'), '"overmax" class added to element');
  });

  test('Maxlength attribute removed', function () {
    maxlengthInput.val('this is over the maxlength');
    maxlengthInput.focus();

    ok(!maxlengthInput.is('[maxlength]'), 'Maxlength attribute is removed and does not exist.');
  });

  test('New data-bs-mxl attribute created', function () {
    maxlengthInput.val('this is over the maxlength');
    maxlengthInput.focus();

    ok(maxlengthInput.attr('data-bs-mxl') === '10', 'data-bs-mxl attribute value is ' + maxlengthInput.attr('data-bs-mxl') + '. Expected value of 10.');
  });


  module('placement object option', {
    setup: function () {
      maxlengthInput = $('<input type="text" maxlength="10" />')
          .appendTo('#qunit-fixture');

      maxlengthInput.maxlength({
        placement : {
          top: '5px',
          left: '6px',
          bottom: '7px',
          right: '10px'
        }
      });
    },
    teardown: function () {
      $('.bootstrap-maxlength').remove();
      $('#qunit-fixture').empty();
    }
  });

  test('css top placement from object placement option', function () {
    maxlengthInput.focus();
    var hasTop = $('.bootstrap-maxlength').attr('style').match(/top\:\s?5px/).length === 1;
    ok(hasTop, 'maxlength has expected top style');
  });

  test('css left placement from object placement option', function () {
    maxlengthInput.focus();
    var hasLeft = $('.bootstrap-maxlength').attr('style').match(/left\:\s?6px/).length === 1;
    ok(hasLeft, 'maxlength has expected left style');
  });

  test('css right placement from object placement option', function () {
    maxlengthInput.focus();
    var hasRight = $('.bootstrap-maxlength').attr('style').match(/right\:\s?10px/).length === 1;
    ok(hasRight, 'maxlength has expected right style');
  });

  test('css bottom placement from object placement option', function () {
    maxlengthInput.focus();
    var hasBottom = $('.bootstrap-maxlength').attr('style').match(/bottom\:\s?7px/).length === 1;
    ok(hasBottom, 'maxlength has expected bottom style');
  });

  var wasCalled,
      argsLength;

  module('placement function option', {
    setup: function () {
      wasCalled = false;
      maxlengthInput = $('<input type="text" maxlength="10" />')
          .appendTo('#qunit-fixture');

      maxlengthInput.maxlength({
        placement : function () {
          wasCalled = true;
          argsLength = arguments.length;
        }
      });
    },
    teardown: function () {
      $('.bootstrap-maxlength').remove();
      $('#qunit-fixture').empty();
    }
  });

  test('Was called', function () {
    maxlengthInput.focus();
    ok(wasCalled, 'Custom placement function was called');
  });
  test('Was called with expected number of arguments', function () {
    maxlengthInput.focus();
    ok(argsLength === 3, 'placement function option was called with expected number of arguments');
  });

});
