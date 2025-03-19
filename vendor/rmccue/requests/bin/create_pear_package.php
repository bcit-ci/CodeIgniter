<?php
/**
 * PEAR package builder
 *
 * Inspired by Twig's create_pear_package.php.
 * @link https://raw.github.com/fabpot/Twig/master/bin/create_pear_package.php
 * @author Twig Team
 * @license BSD license
 */

if (!isset($argv[1]) || $argv[1] === '-h' || $argv[1] === '--help') {
	echo 'usage: php ' . $argv[0] . ' <version> <stability>' . PHP_EOL;
	echo PHP_EOL;
	echo '    version:' . PHP_EOL;
	echo '        Version of the package, in the form of major.minor.bug' . PHP_EOL;
	echo PHP_EOL;
	echo '    stability:' . PHP_EOL;
	echo '        One of alpha, beta, stable' . PHP_EOL;
	die();
}

if (!isset($argv[2])) {
	die('You must provide the stability (alpha, beta, or stable)');
}

$context = array(
	'date'          => gmdate('Y-m-d'),
	'time'          => gmdate('H:m:00'),
	'version'       => $argv[1],
	'api_version'   => $argv[1],
	'stability'     => $argv[2],
	'api_stability' => $argv[2],
);

$context['files'] = '';
$path = realpath(dirname(__FILE__).'/../library/Requests');
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
	if (preg_match('/\.php$/', $file)) 	{
		$name = str_replace($path . DIRECTORY_SEPARATOR, '', $file);
		$name = str_replace(DIRECTORY_SEPARATOR, '/', $name);
		$context['files'][] = "\t\t\t\t\t" . '<file install-as="Requests/' . $name . '" name="' . $name . '" role="php" />';
	}
}

$context['files'] = implode("\n", $context['files']);

$template = file_get_contents(dirname(__FILE__).'/../package.xml.tpl');
$content = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', 'replace_parameters', $template);
file_put_contents(dirname(__FILE__).'/../package.xml', $content);

function replace_parameters($matches) {
	global $context;

	return isset($context[$matches[1]]) ? $context[$matches[1]] : null;
}
