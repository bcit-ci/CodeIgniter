module('Keyboard Navigation (All)', {
    setup: function(){
        this.input = $('<input type="text">')
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

test('TAB hides picker', function(){
    var target;

    ok(this.picker.is(':visible'), 'Picker is visible');

    this.input.trigger({
        type: 'keydown',
        keyCode: 9
    });

    ok(this.picker.is(':not(:visible)'), 'Picker is hidden');
});
