module('Mouse Navigation 2012', {
    setup: function(){
        /*
            Tests start with picker on March 31, 2012.  Fun facts:

            * February 1, 2012 was on a Wednesday
            * February 29, 2012 was on a Wednesday
            * March 1, 2012 was on a Thursday
            * March 31, 2012 was on a Saturday
        */
        this.input = $('<input type="text" value="31-03-2012">')
                        .appendTo('#qunit-fixture')
                        .datetimepicker({format: "dd-mm-yyyy", viewSelect: 2})
                        .focus(); // Activate for visibility checks
        this.dp = this.input.data('datetimepicker')
        this.picker = this.dp.picker;
    },
    teardown: function(){
        this.picker.remove();
    }
});

test('Selecting date resets viewDate and date', function(){
    var target;

    // Rendered correctly
    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days tbody td:nth(7)');
    equal(target.text(), '4'); // Should be Mar 4

    // Updated internally on click
    target.click();
    datesEqual(this.dp.viewDate, UTCDate(2012, 2, 4))
    datesEqual(this.dp.date, UTCDate(2012, 2, 4))

    // Re-rendered on click
    target = this.picker.find('.datetimepicker-days tbody td:first');
    equal(target.text(), '26'); // Should be Feb 29
});

test('Navigating next/prev by month', function(){
    var target;

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.prev');
    ok(target.is(':visible'), 'Month:prev nav is visible');

    // Updated internally on click
    target.click();
    // Should handle month-length changes gracefully
    datesEqual(this.dp.viewDate, UTCDate(2012, 1, 29));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Re-rendered on click
    target = this.picker.find('.datetimepicker-days tbody td:first');
    equal(target.text(), '29'); // Should be Jan 29

    target = this.picker.find('.datetimepicker-days thead th.next');
    ok(target.is(':visible'), 'Month:next nav is visible');

    // Updated internally on click
    target.click().click();
    // Graceful moonth-end handling carries over
    datesEqual(this.dp.viewDate, UTCDate(2012, 3, 29));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Re-rendered on click
    target = this.picker.find('.datetimepicker-days tbody td:first');
    equal(target.text(), '25'); // Should be Mar 25
    // (includes "old" days at start of month, even if that's all the first week-row consists of)
});

test('Navigating to/from year view', function(){
    var target;

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.switch');
    ok(target.is(':visible'), 'View switcher is visible');

    target.click();
    ok(this.picker.find('.datetimepicker-months').is(':visible'), 'Month picker is visible');
    equal(this.dp.viewMode, 3);
    // Not modified when switching modes
    datesEqual(this.dp.viewDate, UTCDate(2012, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Change months to test internal state
    target = this.picker.find('.datetimepicker-months tbody span:contains(Apr)');
    target.click();
    equal(this.dp.viewMode, 2);
    // Only viewDate modified
    datesEqual(this.dp.viewDate, UTCDate(2012, 3, 1)); // Apr 30
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));
});

test('Navigating to/from decade view', function(){
    var target;

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.switch');
    ok(target.is(':visible'), 'View switcher is visible');

    target.click();
    ok(this.picker.find('.datetimepicker-months').is(':visible'), 'Month picker is visible');
    equal(this.dp.viewMode, 3);
    // Not modified when switching modes
    datesEqual(this.dp.viewDate, UTCDate(2012, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    target = this.picker.find('.datetimepicker-months thead th.switch');
    ok(target.is(':visible'), 'View switcher is visible');

    target.click();
    ok(this.picker.find('.datetimepicker-years').is(':visible'), 'Year picker is visible');
    equal(this.dp.viewMode, 4);
    // Not modified when switching modes
    datesEqual(this.dp.viewDate, UTCDate(2012, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Change years to test internal state changes
    target = this.picker.find('.datetimepicker-years tbody span:contains(2011)');
    target.click();
    equal(this.dp.viewMode, 3);
    // Only viewDate modified
    datesEqual(this.dp.viewDate, UTCDate(2011, 2, 1));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    target = this.picker.find('.datetimepicker-months tbody span:contains(Apr)');
    target.click();
    equal(this.dp.viewMode, 2);
    // Only viewDate modified
    datesEqual(this.dp.viewDate, UTCDate(2011, 3, 1));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));
});

test('Navigating prev/next in year view', function(){
    var target;

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.switch');
    ok(target.is(':visible'), 'View switcher is visible');

    target.click();
    ok(this.picker.find('.datetimepicker-months').is(':visible'), 'Month picker is visible');
    equal(this.dp.viewMode, 3);
    equal(this.picker.find('.datetimepicker-months thead th.switch').text(), '2012');
    // Not modified when switching modes
    datesEqual(this.dp.viewDate, UTCDate(2012, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Go to next year (2013)
    target = this.picker.find('.datetimepicker-months thead th.next');
    target.click();
    equal(this.picker.find('.datetimepicker-months thead th.switch').text(), '2013');
    // Only viewDate modified
    datesEqual(this.dp.viewDate, UTCDate(2013, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Go to prev year (x2 == 2011)
    target = this.picker.find('.datetimepicker-months thead th.prev');
    target.click().click();
    equal(this.picker.find('.datetimepicker-months thead th.switch').text(), '2011');
    // Only viewDate modified
    datesEqual(this.dp.viewDate, UTCDate(2011, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));
});

test('Navigating prev/next in decade view', function(){
    var target;

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.switch');
    ok(target.is(':visible'), 'View switcher is visible');

    target.click();
    ok(this.picker.find('.datetimepicker-months').is(':visible'), 'Month picker is visible');
    equal(this.dp.viewMode, 3);
    // Not modified when switching modes
    datesEqual(this.dp.viewDate, UTCDate(2012, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    target = this.picker.find('.datetimepicker-months thead th.switch');
    ok(target.is(':visible'), 'View switcher is visible');

    target.click();
    ok(this.picker.find('.datetimepicker-years').is(':visible'), 'Year picker is visible');
    equal(this.dp.viewMode, 4);
    equal(this.picker.find('.datetimepicker-years thead th.switch').text(), '2010-2019');
    // Not modified when switching modes
    datesEqual(this.dp.viewDate, UTCDate(2012, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Go to next decade (2020-29)
    target = this.picker.find('.datetimepicker-years thead th.next');
    target.click();
    equal(this.picker.find('.datetimepicker-years thead th.switch').text(), '2020-2029');
    // Only viewDate modified
    datesEqual(this.dp.viewDate, UTCDate(2022, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));

    // Go to prev year (x2 == 2000-09)
    target = this.picker.find('.datetimepicker-years thead th.prev');
    target.click().click();
    equal(this.picker.find('.datetimepicker-years thead th.switch').text(), '2000-2009');
    // Only viewDate modified
    datesEqual(this.dp.viewDate, UTCDate(2002, 2, 31));
    datesEqual(this.dp.date, UTCDate(2012, 2, 31));
});

test('Selecting date from previous month resets viewDate and date, changing month displayed', function(){
    var target;

    // Rendered correctly
    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days tbody td:first');
    equal(target.text(), '26'); // Should be Feb 26
    equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'March 2012');

    // Updated internally on click
    target.click();
    equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'February 2012');
    datesEqual(this.dp.viewDate, UTCDate(2012, 1, 26))
    datesEqual(this.dp.date, UTCDate(2012, 1, 26))

    // Re-rendered on click
    target = this.picker.find('.datetimepicker-days tbody td:first');
    equal(target.text(), '29'); // Should be Jan 29
});

test('Selecting date from next month resets viewDate and date, changing month displayed', function(){
  var target;

  this.input.val('01-04-2012');
  this.dp.update();

  // Rendered correctly
  equal(this.dp.viewMode, 2);
  target = this.picker.find('.datetimepicker-days tbody td:last');
  equal(target.text(), '5'); // Should be May 5
  equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'April 2012');

  // Updated internally on click
  target.click();
  equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'May 2012');
  datesEqual(this.dp.viewDate, UTCDate(2012, 4, 5))
  datesEqual(this.dp.date, UTCDate(2012, 4, 5))

  // Re-rendered on click
  target = this.picker.find('.datetimepicker-days tbody td:first');
  equal(target.text(), '29'); // Should be Apr 29
});

test('Selecting date from next month when the current month has 31 days resets viewDate and date, changing month displayed to the following month', function(){
  var target;

  // use Date AND Time mode
  this.dp.viewSelect = 0;

  this.input.val('2012-01-31');
  this.dp.update();
  equal(this.picker.find('.datetimepicker-days tbody td.day.active').text(), '31');

  // Rendered correctly
  equal(this.dp.viewMode, 2);
  target = this.picker.find('.datetimepicker-days tbody td:last');
  equal(target.text(), '4');
  equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'January 2012');

  // Updated internally on click
  target.click();
  equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'February 2012');
  datesEqual(this.dp.viewDate, UTCDate(2012, 1, 4));
});

test('Selecting date from previous month when the current month has 31 days resets viewDate and date, changing month displayed to the preceding month', function(){
  var target;

  // use Date AND Time mode
  this.dp.viewSelect = 0;

  this.input.val('2012-03-31');
  this.dp.update();
  equal(this.picker.find('.datetimepicker-days tbody td.day.active').text(), '31');

  // Rendered correctly
  equal(this.dp.viewMode, 2);
  target = this.picker.find('.datetimepicker-days tbody td.old:last');
  equal(target.text(), '29');
  equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'March 2012');

  // Updated internally on click
  target.click();
  equal(this.picker.find('.datetimepicker-days thead th.switch').text(), 'February 2012');
  datesEqual(this.dp.viewDate, UTCDate(2012, 1, 29));
});
