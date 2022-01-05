{
	"description": "The CodeIgniter framework",
	"name": "codeigniter/framework",
	"type": "project",
	"homepage": "https://codeigniter.com",
	"license": "MIT",
	"support": {
		"forum": "http://forum.codeigniter.com/",
		"wiki": "https://github.com/bcit-ci/CodeIgniter/wiki",
		"slack": "https://codeigniterchat.slack.com",
		"source": "https://github.com/bcit-ci/CodeIgniter"
	},
	"require": {
		"php": ">=5.3.7"
	},
	"suggest": {
		"paragonie/random_compat": "Provides better randomness in PHP 5.x"
	},
	"scripts": {
		"test:coverage": [
			"@putenv XDEBUG_MODE=coverage",
			"phpunit --color=always --coverage-text --configuration tests/travis/sqlite.phpunit.xml"
		],
		"post-install-cmd": [
			"sed -i s/name{0}/name[0]/ vendor/mikey179/vfsstream/src/main/php/org/bovigo/vfs/vfsStream.php"
		],
		"post-update-cmd": [
			"sed -i s/name{0}/name[0]/ vendor/mikey179/vfsstream/src/main/php/org/bovigo/vfs/vfsStream.php"
		]
	},
	"require-dev": {
		"mikey179/vfsstream": "1.6.*",
		"phpunit/phpunit": "4.* || 5.* || 9.*"
	}
}
