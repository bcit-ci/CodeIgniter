module('Core tests');

test('replace with loop', function() {
	expect(1);
	before_and_after_replace({ loop: true });
});

test('replace without loop', function() {
	expect(1);
	before_and_after_replace({ loop: false });
});

function before_and_after_replace(options) {
	var simple = $('#simple'),
		replacement = simple.html(),
		expected = null;
	
	simple.owlCarousel(options);
	
	expected = simple.html();
	
	simple.trigger('replace.owl.carousel', [ replacement ]);
	simple.trigger('refresh.owl.carousel');
	
	equal(simple.html(), expected, 'Inner HTML before and after replace equals.');
}

test('remove with loop', function() {
	expect(3);
	
	before_and_after_remove({ loop: true });
});

test('remove without loop', function() {
	expect(3);
	
	before_and_after_remove({ loop: false });
});

function before_and_after_remove(options) {
	var simple = $('#simple'),
		one = simple.clone().removeAttr('id').insertAfter('#simple'),
		two = one.clone().insertAfter(one),
		all = two.clone().insertAfter(two);
	
	one.children(':eq(0)').remove();
	two.children(':eq(0),:eq(2)').remove();
	all.children().remove();

	simple.owlCarousel(options);
	one.owlCarousel(options);
	two.owlCarousel(options);
	all.owlCarousel(options);
	
	simple.trigger('remove.owl.carousel', [ 0 ]);
	simple.trigger('refresh.owl.carousel');
	
	equal(simple.html(), one.html(), 'Inner HTML before and after remove one equals.');
	
	simple.trigger('remove.owl.carousel', [ 1 ]);
	simple.trigger('refresh.owl.carousel');
	
	equal(simple.html(), two.html(), 'Inner HTML before and after remove two equals.');
	
	simple.trigger('remove.owl.carousel', [ 0 ]);
	simple.trigger('refresh.owl.carousel');
	
	equal(simple.html(), all.html(), 'Inner HTML before and after remove all equals.');
}

test('remove and add with loop', function() {
	expect(1);
	
	before_and_after_remove_add({ loop: true });
});

test('remove and add without loop', function() {
	expect(1);
	
	before_and_after_remove_add({ loop: false });
});

function before_and_after_remove_add(options) {
	var simple = $('#simple'),
		simpleClone = $('#simple').clone().removeAttr('id').insertAfter('#simple');

    //move the text along as 'add' adds the new element to the add as expected.
    simpleClone.children(':eq(0)').text('2');
    simpleClone.children(':eq(1)').text('3');
    simpleClone.children(':eq(2)').text('1');

	simple.owlCarousel(options);
    simpleClone.owlCarousel(options);

	simple.trigger('remove.owl.carousel', [ 0 ]);
	simple.trigger('add.owl.carousel', [ '<li>1</li>' ]);
	simple.trigger('refresh.owl.carousel');
	
	equal(simple.html(), simpleClone.html(), 'Inner HTML before and after `remove()` and `add()` equals.');
}

test('invalidate', function() {
	expect(6);
	
	var carousel = $('#simple').owlCarousel().data('owl.carousel');
	
	deepEqual(carousel.invalidate(), [], 'No invalid parts after initializing.');

	carousel.invalidate('first');
	
	deepEqual(carousel.invalidate(), [ 'first' ], 'One invalid part after invalidating one.');
	
	carousel.invalidate('second');
	
	deepEqual(carousel.invalidate(), [ 'first', 'second' ], 'Two invalid parts after invalidating a second one.');
	
	carousel.invalidate('second');
	
	deepEqual(carousel.invalidate(), [ 'first', 'second' ], 'Two invalid parts after invalidating a part twice.');
	
	carousel.update();
	
	deepEqual(carousel.invalidate(), [], 'No invalid parts after updating.');
	
	deepEqual(carousel.invalidate('first'), [ 'first' ], 'Invalidating one part returns it directly.');
});

test('destroy', function() {
	expect(1);
	
	var simple = $('#simple'),
		expected = simple.get(0).outerHTML.replace(/\s{2,}/g, '');
	
	simple.owlCarousel().owlCarousel('destroy');
	
	equal(simple.get(0).outerHTML.replace(/\s{2,}/g, ''), expected, 'Outer HTML before create and after destroy is equal.');
});

start();

