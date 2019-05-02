module('Keyboard Navigation 2011', {
    setup: function(){
        /*
            Tests start with picker on March 31, 2011.  Fun facts:

            * March 1, 2011 was on a Tuesday
            * March 31, 2011 was on a Thursday
        */
        this.input = $('<input type="text" value="31-03-2011">')
                        .appendTo('#qunit-fixture')
                        .datetimepicker({format: "dd-mm-yyyy"})
                        .focus(); // Activate for visibility checks
        this.dp = this.input.data('datetimepicker')
        this.picker = this.dp.picker;
    },
    teardown: function(){
        this.picker.remove();
    }
});

test('Regression: by week (up/down arrows); up from Mar 6, 2011 should go to Feb 27, 2011', function(){
    var target;

    this.input.val('06-03-2011').datetimepicker('update');

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.switch');
    equal(target.text(), 'March 2011', 'Title is "March 2011"');
    datesEqual(this.dp.viewDate, UTCDate(2011, 2, 6));
    datesEqual(this.dp.date, UTCDate(2011, 2, 6));

    // Navigation: -1 week, up arrow key
    this.input.trigger({
        type: 'keydown',
        keyCode: 38
    });
    datesEqual(this.dp.viewDate, UTCDate(2011, 1, 27));
    datesEqual(this.dp.date, UTCDate(2011, 1, 27));
    target = this.picker.find('.datetimepicker-days thead th.switch');
    equal(target.text(), 'February 2011', 'Title is "February 2011"');
});

test('Regression: by day (left/right arrows); left from Mar 1, 2011 should go to Feb 28, 2011', function(){
    var target;

    this.input.val('01-03-2011').datetimepicker('update');

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.switch');
    equal(target.text(), 'March 2011', 'Title is "March 2011"');
    datesEqual(this.dp.viewDate, UTCDate(2011, 2, 1));
    datesEqual(this.dp.date, UTCDate(2011, 2, 1));

    // Navigation: -1 day left arrow key
    this.input.trigger({
        type: 'keydown',
        keyCode: 37
    });
    datesEqual(this.dp.viewDate, UTCDate(2011, 1, 28));
    datesEqual(this.dp.date, UTCDate(2011, 1, 28));
    target = this.picker.find('.datetimepicker-days thead th.switch');
    equal(target.text(), 'February 2011', 'Title is "February 2011"');
});

test('Regression: by month (shift + left/right arrows); left from Mar 15, 2011 should go to Feb 15, 2011', function(){
    var target;

    this.input.val('15-03-2011').datetimepicker('update');

    equal(this.dp.viewMode, 2);
    target = this.picker.find('.datetimepicker-days thead th.switch');
    equal(target.text(), 'March 2011', 'Title is "March 2011"');
    datesEqual(this.dp.viewDate, UTCDate(2011, 2, 15));
    datesEqual(this.dp.date, UTCDate(2011, 2, 15));

    // Navigation: -1 month, shift + left arrow key
    this.input.trigger({
        type: 'keydown',
        keyCode: 37,
        shiftKey: true
    });
    datesEqual(this.dp.viewDate, UTCDate(2011, 1, 15));
    datesEqual(this.dp.date, UTCDate(2011, 1, 15));
    target = this.picker.find('.datetimepicker-days thead th.switch');
    equal(target.text(), 'February 2011', 'Title is "February 2011"');
});
