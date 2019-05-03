function testLoader(){
	var jasmineEnv = jasmine.getEnv();
	jasmineEnv.updateInterval = 1000;

	var htmlReporter = new jasmine.HtmlReporter();

	jasmineEnv.addReporter(htmlReporter);

	jasmineEnv.specFilter = function(spec) {
		return htmlReporter.specFilter(spec);
	};

	var currentWindowOnload = window.onload;

	window.onload = function() {
		var count = 0;
		var loadCoffee = function(files) {
			for (var i = 0, len = files.length; i < len; i++) {
				count++;
				CoffeeScript.load(files[i], function() {
					count--;
					if (!count) {
						jasmine.getFixtures().fixturesPath = 'fixtures';
						execJasmine();
					}
				});
			}
		};

		if (currentWindowOnload) {
			currentWindowOnload();
		}
		loadCoffee([
			'waypoints.coffee',
			'infinite.coffee',
			'sticky.coffee'
		]);
	};

	function execJasmine() {
		jasmineEnv.execute();
	}

	if (document.readyState === 'complete'){
		window.onload();
	}
}
