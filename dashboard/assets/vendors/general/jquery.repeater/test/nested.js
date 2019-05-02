QUnit.module('nested-repeater', {
    setup: function () {
        this.$fixture = $('#qunit-fixture');
        this.$fixture.html($('#template').html());
        this.$outerRepeater = this.$fixture.find('.outer-repeater');
        this.$innerRepeater = this.$fixture.find('.inner-repeater');
        this.$outerAddButton = this.$fixture.find('.outer-repeater > [data-repeater-create]');
        this.$innerAddButton = this.$fixture.find('.inner-repeater > [data-repeater-create]');
    }
});


QUnit.test('isFirstItemUndeletable configuration option', function (assert) {
    this.$outerRepeater.repeater({
        isFirstItemUndeletable: true,
        repeaters: [{
            selector: '.inner-repeater',
            isFirstItemUndeletable: true
        }]
    });

    this.$outerAddButton.click();
    this.$innerAddButton.click();

    var $outerItems = this.$outerRepeater.find('[data-repeater-list="outer-group"] > [data-repeater-item]');
    var $innerItems = this.$innerRepeater.find('[data-repeater-item]');

    assert.strictEqual($outerItems.length, 2, 'adds a second item to outer list');
    assert.strictEqual($innerItems.length, 2, 'adds a second item to inner list');
    assert.strictEqual(
        this.$outerRepeater.find('[data-repeater-item].outer')
        .first().find('[data-repeater-delete].outer').length,
        0,
        'No delete button on first outer item'
    );

    assert.strictEqual(
        this.$outerRepeater.find('[data-repeater-item].outer')
        .last().find('[data-repeater-delete].outer').length,
        1,
        'Delete button on second outer item'
    );

    assert.strictEqual(
        this.$innerRepeater.find('[data-repeater-item]')
        .first().find('[data-repeater-delete]').length,
        0,
        'No delete button on first inner item of first outer item'
    );

    assert.strictEqual(
        this.$innerRepeater.find('[data-repeater-item]')
        .last().find('[data-repeater-delete]').length,
        1,
        'Delete button on second inner item of first outer item'
    );

    assert.strictEqual(
        this.$outerRepeater.find('[data-repeater-list="inner-group"]').last()
        .find('[data-repeater-item]').first().find('[data-repeater-delete]').length,
        0,
        'No delete button on first inner item of second outer item'
    );
});

QUnit.test('setList', function (assert) {
    var repeater = this.$outerRepeater.repeater({
        repeaters: [{ selector: '.inner-repeater' }]
    });
    repeater.setList([
        {
            'text-input': 'set-a',
            'inner-group': [{ 'inner-text-input': 'set-b' }]
        },
        {
            'text-input': 'set-foo',
            'inner-group': []
        }
    ]);

    var $items = this.$outerRepeater.find('[data-repeater-list="outer-group"] > [data-repeater-item]');

    assert.deepEqual(
        getNamedInputValues($items.first()),
        {
            "outer-group[0][text-input]": "set-a",
            "outer-group[0][inner-group][0][inner-text-input]": "set-b"
        },
        'set first item'
    );

    assert.deepEqual(
        getNamedInputValues($items.last()),
        {
            "outer-group[1][text-input]": "set-foo"
        },
        'set second item'
    );
});

QUnit.test('add item nested outer', function (assert) {
    this.$outerRepeater.repeater({ repeaters: [{ selector: '.inner-repeater' }] });
    this.$outerAddButton.click();
    var $items = this.$outerRepeater.find('[data-repeater-list="outer-group"] > [data-repeater-item]');

    assert.strictEqual($items.length, 2, 'adds a second item to list');

    assert.strictEqual(
        $items.first().find('[data-repeater-list="inner-group"] > [data-repeater-item]').length,
        1, 'does not duplicate first inner repeater'
    );

    assert.strictEqual(
        $items.last().find('[data-repeater-list="inner-group"] > [data-repeater-item]').length,
        1, 'does not duplicate last inner repeater'
    );

    assert.deepEqual(
        getNamedInputValues($items.first()),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B"
        },
        'renamed first item'
    );

    assert.deepEqual(
        getNamedInputValues($items.last()),
        {
            "outer-group[1][text-input]": "",
            "outer-group[1][inner-group][0][inner-text-input]": ""
        },
        'renamed last item, values cleared'
    );
});

QUnit.test('delete added item outer from repeated outer', function (assert) {
    this.$outerRepeater.repeater({ repeaters: [{ selector: '.inner-repeater' }] });
    this.$outerAddButton.click();

    var $lastOuterItem = this.$outerRepeater
        .find('[data-repeater-list="outer-group"] > [data-repeater-item]')
        .last();

    $lastOuterItem.find('[data-repeater-delete]').first().click();

    assert.deepEqual(
        getNamedInputValues(this.$outerRepeater),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B"
        }
    );
});

QUnit.test('delete added item outer from first outer', function (assert) {
    this.$outerRepeater.repeater({ repeaters: [{ selector: '.inner-repeater' }] });
    this.$outerAddButton.click();

    var $firstOuterItem = this.$outerRepeater
        .find('[data-repeater-list="outer-group"] > [data-repeater-item]')
        .first();

    $firstOuterItem.find('[data-repeater-delete]').first().click();

    assert.deepEqual(
        getNamedInputValues(this.$outerRepeater),
        {
            "outer-group[0][text-input]": "",
            "outer-group[0][inner-group][0][inner-text-input]": ""
        }
    );
});

QUnit.test('add item nested inner', function (assert) {
    this.$outerRepeater.repeater({ repeaters: [{ selector: '.inner-repeater' }] });
    this.$innerAddButton.click();

    assert.strictEqual(
        this.$innerRepeater.find('[data-repeater-item]').length,
        2, 'adds item to inner repeater'
    );

    var $items = this.$outerRepeater.find('[data-repeater-list="outer-group"] > [data-repeater-item]');

    assert.strictEqual($items.length, 1, 'does not add item to outer list');

    assert.deepEqual(
        getNamedInputValues($items.first()),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B",
            "outer-group[0][inner-group][1][inner-text-input]": "",
        },
        'renamed items'
    );
});

QUnit.test('add item nested inner from repeated outer', function (assert) {
    this.$outerRepeater.repeater({ repeaters: [{ selector: '.inner-repeater' }] });
    this.$outerAddButton.click();

    var $lastItem =  this.$outerRepeater.find('[data-repeater-list="outer-group"] > [data-repeater-item]').last();

    $lastItem.find('[data-repeater-create]').click();

    assert.strictEqual(
        this.$innerRepeater.find('[data-repeater-item]').length,
        1, 'does not add item to first inner repeater'
    );

    assert.strictEqual(
        $lastItem.find('[data-repeater-item]').length,
        2, 'adds item to second inner repeater'
    );

    var $items = this.$outerRepeater.find('[data-repeater-list="outer-group"] > [data-repeater-item]');

    assert.strictEqual($items.length, 2, 'correct number of terms in outer list');

    assert.deepEqual(
        getNamedInputValues(this.$outerRepeater),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B",
            "outer-group[1][text-input]": "",
            "outer-group[1][inner-group][0][inner-text-input]": "",
            "outer-group[1][inner-group][1][inner-text-input]": "",
        },
        'renamed items'
    );
});

QUnit.test('delete added item nested inner from repeated outer', function (assert) {
    this.$outerRepeater.repeater({ repeaters: [{ selector: '.inner-repeater' }] });
    this.$outerAddButton.click();

    var $lastOuterItem = this.$outerRepeater
        .find('[data-repeater-list="outer-group"] > [data-repeater-item]')
        .last();

    $lastOuterItem.find('[data-repeater-create]').click();
    $lastOuterItem.find('[data-repeater-list] [data-repeater-delete]').first().click();

    assert.deepEqual(
        getNamedInputValues(this.$outerRepeater),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B",
            "outer-group[1][text-input]": "",
            "outer-group[1][inner-group][0][inner-text-input]": ""
        }
    );
});

QUnit.test('nested default values first item', function (assert) {
    this.$outerRepeater.repeater({
        repeaters: [{
            selector: '.inner-repeater',
            defaultValues: { 'inner-text-input': 'foo' }
        }]
    });

    this.$innerAddButton.click();

    assert.deepEqual(
        getNamedInputValues(this.$outerRepeater),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B",
            "outer-group[0][inner-group][1][inner-text-input]": "foo"
        }
    );
});

QUnit.test('nested default values last item', function (assert) {
    this.$outerRepeater.repeater({
        repeaters: [{
            selector: '.inner-repeater',
            defaultValues: { 'inner-text-input': 'foo' }
        }]
    });

    this.$outerAddButton.click();
    var $lastOuterItem = this.$outerRepeater
        .find('[data-repeater-list="outer-group"] > [data-repeater-item]')
        .last();

    assert.deepEqual(
        getNamedInputValues(this.$outerRepeater),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B",
            "outer-group[1][text-input]": "",
            "outer-group[1][inner-group][0][inner-text-input]": "foo"
        }
    );

    $lastOuterItem.find('[data-repeater-create]').click();

    assert.deepEqual(
        getNamedInputValues(this.$outerRepeater),
        {
            "outer-group[0][text-input]": "A",
            "outer-group[0][inner-group][0][inner-text-input]": "B",
            "outer-group[1][text-input]": "",
            "outer-group[1][inner-group][0][inner-text-input]": "foo",
            "outer-group[1][inner-group][1][inner-text-input]": "foo"
        }
    );
});

QUnit.test('repeaterVal nested', function (assert) {
    this.$outerRepeater.repeater({
        repeaters: [{ selector: '.inner-repeater' }]
    });

    assert.deepEqual(this.$outerRepeater.repeaterVal(), {
        'outer-group': [{
            'text-input': 'A',
            'inner-group': [{ 'inner-text-input': 'B' }]
        }]
    });
});
