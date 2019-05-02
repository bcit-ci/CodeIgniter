QUnit.module('complex-repeater', {
    setup: function () {
        this.$fixture = $('#qunit-fixture');
        this.$fixture.html($('#template').html());
        this.$repeater = this.$fixture.find('.complex-repeater');
        this.$addButton = this.$repeater.find('[data-repeater-create]');
        this.$fixture.append($('#template').html());
    }
});

QUnit.test('add item', function (assert) {
    this.$repeater.repeater();
    this.$addButton.click();
    var $items = this.$repeater.find('[data-repeater-item]');
    assert.strictEqual($items.length, 2, 'adds a second item to list');

    assert.deepEqual(
        getNamedInputValues($items.last()),
        { 'complex-repeater[1][text-input]': '' },
        'added items inputs are clear'
    );

    assert.deepEqual(
        getNamedInputValues($items.first()),
        { 'complex-repeater[0][text-input]': 'A' },
        'does not clear other inputs'
    );
});

QUnit.test('delete item', function (assert) {
    this.$repeater.repeater();
    this.$repeater.find('[data-repeater-delete]').first().click();
    assert.strictEqual(
        this.$repeater.find('[data-repeater-item]').length, 0,
        'deletes item'
    );
});

QUnit.test('delete item that has been added', function (assert) {
    this.$repeater.repeater();
    this.$addButton.click();
    assert.strictEqual(
        this.$repeater.find('[data-repeater-item]').length, 2,
        'item added'
    );
    this.$repeater.find('[data-repeater-delete]').last().click();
    assert.strictEqual(
        this.$repeater.find('[data-repeater-item]').length, 1,
        'item deleted'
    );
});
