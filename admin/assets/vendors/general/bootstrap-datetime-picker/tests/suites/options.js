module('Options', {
    setup: function(){},
    teardown: function(){
        return
        $('#qunit-fixture *').each(function(){
            var t = $(this);
            if ('datetimepicker' in t.data())
                t.data('datetimepicker').picker.remove();
        });
    }
});

test('Autoclose', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
					minView: 2,
                    viewSelect: 2
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;


    input.focus();
    ok(picker.is(':visible'), 'Picker is visible');
    target = picker.find('.datetimepicker-days tbody td:nth(7)');
    equal(target.text(), '4'); // Mar 4

    target.click();
    ok(picker.is(':not(:visible)'), 'Picker is hidden');
    datesEqual(dp.date, UTCDate(2012, 2, 4));
    datesEqual(dp.viewDate, UTCDate(2012, 2, 4));
});

test('Startview: year view (integer)', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 3,
                    viewSelect: 2
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':not(:visible)'), 'Days view hidden');
        ok(picker.find('.datetimepicker-months').is(':visible'), 'Months view visible');
        ok(picker.find('.datetimepicker-years').is(':not(:visible)'), 'Years view hidden');
});

test('Startview: year view (string)', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 'year',
                    viewSelect: 2
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':not(:visible)'), 'Days view hidden');
        ok(picker.find('.datetimepicker-months').is(':visible'), 'Months view visible');
        ok(picker.find('.datetimepicker-years').is(':not(:visible)'), 'Years view hidden');
});

test('Startview: decade view (integer)', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 4
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':not(:visible)'), 'Days view hidden');
        ok(picker.find('.datetimepicker-months').is(':not(:visible)'), 'Months view hidden');
        ok(picker.find('.datetimepicker-years').is(':visible'), 'Years view visible');
});

test('Startview: decade view (string)', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 'decade'
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':not(:visible)'), 'Days view hidden');
        ok(picker.find('.datetimepicker-months').is(':not(:visible)'), 'Months view hidden');
        ok(picker.find('.datetimepicker-years').is(':visible'), 'Years view visible');
});

test('Today Button: today button not default', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd'
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':visible'), 'Days view visible');
        ok(picker.find('.datetimepicker-days tfoot .today').is(':not(:visible)'), 'Today button not visible');
});

test('Today Button: today visibility when enabled', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    todayBtn: true
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':visible'), 'Days view visible');
        ok(picker.find('.datetimepicker-days tfoot .today').is(':visible'), 'Today button visible');

        picker.find('.datetimepicker-days thead th.switch').click();
        ok(picker.find('.datetimepicker-months').is(':visible'), 'Months view visible');
        ok(picker.find('.datetimepicker-months tfoot .today').is(':visible'), 'Today button visible');

        picker.find('.datetimepicker-months thead th.switch').click();
        ok(picker.find('.datetimepicker-years').is(':visible'), 'Years view visible');
        ok(picker.find('.datetimepicker-years tfoot .today').is(':visible'), 'Today button visible');
});

test('Today Button: data-api', function(){
    var input = $('<input data-date-today-btn="true" />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd'
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':visible'), 'Days view visible');
        ok(picker.find('.datetimepicker-days tfoot .today').is(':visible'), 'Today button visible');
});

test('Today Button: moves to today\'s date', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    todayBtn: true
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':visible'), 'Days view visible');
        ok(picker.find('.datetimepicker-days tfoot .today').is(':visible'), 'Today button visible');

        target = picker.find('.datetimepicker-days tfoot .today');
        target.click();

        var d = new Date(),
            today = UTCDate(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes(), d.getSeconds());
        datesEqual(dp.viewDate, today);
        //datesEqual(dp.date, UTCDate(2012, 2, 5));
});

test('Today Button: "linked" selects today\'s date', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    todayBtn: "linked"
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':visible'), 'Days view visible');
        ok(picker.find('.datetimepicker-days tfoot .today').is(':visible'), 'Today button visible');

        target = picker.find('.datetimepicker-days tfoot .today');
        target.click();

        var d = new Date(),
            today = UTCDate(d.getFullYear(), d.getMonth(), d.getDate(), d.getHours(), d.getMinutes(), d.getSeconds());
        datesEqual(dp.viewDate, today);
        datesEqual(dp.date, today);
});

test('Today Highlight: today\'s date is not highlighted by default', patch_date(function(Date){
    Date.now = UTCDate(2012, 2, 15);
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd'
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':visible'), 'Days view visible');
        equal(picker.find('.datetimepicker-days thead .switch').text(), 'March 2012', 'Title is "March 2012"');

        target = picker.find('.datetimepicker-days tbody td:contains(15)');
        ok(!target.hasClass('today'), 'Today is not marked with "today" class');
        target = picker.find('.datetimepicker-days tbody td:contains(14)');
        ok(!target.hasClass('today'), 'Yesterday is not marked with "today" class');
        target = picker.find('.datetimepicker-days tbody td:contains(16)');
        ok(!target.hasClass('today'), 'Tomorrow is not marked with "today" class');
}));

test('Today Highlight: today\'s date is highlighted when not active', patch_date(function(Date){
    Date.now = new Date(2012, 2, 15);
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-03-05')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    todayHighlight: true
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

        input.focus();
        ok(picker.find('.datetimepicker-days').is(':visible'), 'Days view visible');
        equal(picker.find('.datetimepicker-days thead .switch').text(), 'March 2012', 'Title is "March 2012"');

        target = picker.find('.datetimepicker-days tbody td:contains(15)');
        ok(target.hasClass('today'), 'Today is marked with "today" class');
        target = picker.find('.datetimepicker-days tbody td:contains(14)');
        ok(!target.hasClass('today'), 'Yesterday is not marked with "today" class');
        target = picker.find('.datetimepicker-days tbody td:contains(16)');
        ok(!target.hasClass('today'), 'Tomorrow is not marked with "today" class');
}));

test('DaysOfWeekDisabled', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2012-10-26')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    daysOfWeekDisabled: '1,5'
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;


    input.focus();
    target = picker.find('.datetimepicker-days tbody td:nth(22)');
    ok(target.hasClass('disabled'), 'Day of week is disabled');
    target = picker.find('.datetimepicker-days tbody td:nth(24)');
    ok(!target.hasClass('disabled'), 'Day of week is enabled');
    target = picker.find('.datetimepicker-days tbody td:nth(26)');
    ok(target.hasClass('disabled'), 'Day of week is disabled');
});

test('startDate: Custom value', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2013-01-25')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 2,
                    startDate: "2013-01-24 15:30",
                    viewSelect: 2
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

    input.focus();
    ok(picker.find('.datetimepicker-days tbody tr:nth(3) td:nth(2)').hasClass('disabled'), 'The previous day is disabled');
    target = picker.find('.datetimepicker-days tbody tr:nth(3) td:nth(4)')
    ok(!target.hasClass('disabled'), 'The starting day is enabled');

    target.click()
    ok(picker.find('.datetimepicker-hours tbody span:nth(14)').hasClass('disabled'), 'The previous hour is disabled');
    target = picker.find('.datetimepicker-hours tbody span:nth(15)')
    ok(!target.hasClass('disabled'), 'The starting hour is enabled');

    target.click()
    ok(picker.find('.datetimepicker-minutes tbody span:nth(5)').hasClass('disabled'), 'The previous minute is disabled');
    ok(!picker.find('.datetimepicker-minutes tbody span:nth(6)').hasClass('disabled'), 'The starting minute is enabled');
});

test('startDate: Custom value', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2013-01-25')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 2,
                    startDate: "2013-01-24 15:30",
                    viewSelect: 2
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

    input.focus();
    ok(picker.find('.datetimepicker-days tbody tr:nth(3) td:nth(3)').hasClass('disabled'), 'The previous day is disabled');
    target = picker.find('.datetimepicker-days tbody tr:nth(3) td:nth(4)')
    ok(!target.hasClass('disabled'), 'The starting day is enabled');

    target.click()
    ok(picker.find('.datetimepicker-hours tbody span:nth(14)').hasClass('disabled'), 'The previous hour is disabled');
    target = picker.find('.datetimepicker-hours tbody span:nth(15)')
    ok(!target.hasClass('disabled'), 'The starting hour is enabled');

    target.click()
    ok(picker.find('.datetimepicker-minutes tbody span:nth(5)').hasClass('disabled'), 'The previous minute is disabled');
    ok(!picker.find('.datetimepicker-minutes tbody span:nth(6)').hasClass('disabled'), 'The starting minute is enabled');
});

test('endDate: Custom value', function(){
    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2013-01-25')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 2,
                    endDate: "2013-01-24 15:30",
                    viewSelect: 2
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker,
        target;

    input.focus();
    ok(picker.find('.datetimepicker-days tbody tr:nth(3) td:nth(5)').hasClass('disabled'), 'The next day is disabled');
    target = picker.find('.datetimepicker-days tbody tr:nth(3) td:nth(4)')
    ok(!target.hasClass('disabled'), 'The last day is enabled');

    target.click()
    ok(picker.find('.datetimepicker-hours tbody span:nth(16)').hasClass('disabled'), 'The next hour is disabled');
    target = picker.find('.datetimepicker-hours tbody span:nth(15)')
    ok(!target.hasClass('disabled'), 'The last hour is enabled');

    target.click()
    ok(picker.find('.datetimepicker-minutes tbody span:nth(7)').hasClass('disabled'), 'The next minute is disabled');
    ok(!picker.find('.datetimepicker-minutes tbody span:nth(6)').hasClass('disabled'), 'The last minute is enabled');
});

test('zIndex: set in options', function(){
    var zIndex = 77;

    var input = $('<input />')
                .appendTo('#qunit-fixture')
                .val('2013-01-25')
                .datetimepicker({
                    format: 'yyyy-mm-dd',
                    startView: 2,
                    endDate: "2013-01-24 15:30",
                    viewSelect: 2,
                    zIndex: zIndex
                }),
        dp = input.data('datetimepicker'),
        picker = dp.picker;

    ok(parseInt(picker.css('z-index'), 10) == zIndex, 'has a value defined in the options');

});
