<?php

error_reporting(E_ALL | E_STRICT);
ini_set('short_open_tag', false);

if ('cli' !== php_sapi_name()) {
    die('This script is designed for running on the command line.');
}

function showHelp($error) {
    die($error . "\n\n" .
<<<OUTPUT
This script has to be called with the following signature:

    php run.php [--no-progress] testType pathToTestFiles

The test type must be one of: PHP5, PHP7 or Symfony.

The following options are available:

    --no-progress    Disables showing which file is currently tested.

OUTPUT
    );
}

$options = array();
$arguments = array();

// remove script name from argv
array_shift($argv);

foreach ($argv as $arg) {
    if ('-' === $arg[0]) {
        $options[] = $arg;
    } else {
        $arguments[] = $arg;
    }
}

if (count($arguments) !== 2) {
    showHelp('Too little arguments passed!');
}

$showProgress = true;
if (count($options) > 0) {
    if (count($options) === 1 && $options[0] === '--no-progress') {
        $showProgress = false;
    } else {
        showHelp('Invalid option passed!');
    }
}

$testType = $arguments[0];
$dir = $arguments[1];

switch ($testType) {
    case 'Symfony':
        $version = 'Php5';
        $fileFilter = function($path) {
            return preg_match('~\.php(?:\.cache)?$~', $path) && false === strpos($path, 'skeleton');
        };
        $codeExtractor = function($file, $code) {
            return $code;
        };
        break;
    case 'PHP5':
    case 'PHP7':
    $version = $testType === 'PHP5' ? 'Php5' : 'Php7';
        $fileFilter = function($path) {
            return preg_match('~\.phpt$~', $path);
        };
        $codeExtractor = function($file, $code) {
            if (preg_match('~(?:
# skeleton files
  ext.gmp.tests.001
| ext.skeleton.tests.001
# multibyte encoded files
| ext.mbstring.tests.zend_multibyte-01
| Zend.tests.multibyte.multibyte_encoding_001
| Zend.tests.multibyte.multibyte_encoding_004
| Zend.tests.multibyte.multibyte_encoding_005
# token_get_all bug (https://bugs.php.net/bug.php?id=60097)
| Zend.tests.bug47516
# pretty print difference due to INF vs 1e1000
| ext.standard.tests.general_functions.bug27678
| tests.lang.bug24640
)\.phpt$~x', $file)) {
                return null;
            }

            if (!preg_match('~--FILE--\s*(.*?)--[A-Z]+--~s', $code, $matches)) {
                return null;
            }
            if (preg_match('~--EXPECT(?:F|REGEX)?--\s*(?:Parse|Fatal) error~', $code)) {
                return null;
            }

            return $matches[1];
        };
        break;
    default:
        showHelp('Test type must be one of: PHP5, PHP7 or Symfony');
}

require_once dirname(__FILE__) . '/../lib/PhpParser/Autoloader.php';
PhpParser\Autoloader::register();

$parserName    = 'PhpParser\Parser\\' . $version;
$parser        = new $parserName(new PhpParser\Lexer\Emulative);
$prettyPrinter = new PhpParser\PrettyPrinter\Standard;
$nodeDumper    = new PhpParser\NodeDumper;

$parseFail = $ppFail = $compareFail = $count = 0;

$readTime = $parseTime = $ppTime = $reparseTime = $compareTime = 0;
$totalStartTime = microtime(true);

foreach (new RecursiveIteratorIterator(
             new RecursiveDirectoryIterator($dir),
             RecursiveIteratorIterator::LEAVES_ONLY)
         as $file) {
    if (!$fileFilter($file)) {
        continue;
    }

    $startTime = microtime(true);
    $code = file_get_contents($file);
    $readTime += microtime(true) - $startTime;

    if (null === $code = $codeExtractor($file, $code)) {
        continue;
    }

    set_time_limit(10);

    ++$count;

    if ($showProgress) {
        echo substr(str_pad('Testing file ' . $count . ': ' . substr($file, strlen($dir)), 79), 0, 79), "\r";
    }

    try {
        $startTime = microtime(true);
        $stmts = $parser->parse($code);
        $parseTime += microtime(true) - $startTime;

        $startTime = microtime(true);
        $code = '<?php' . "\n" . $prettyPrinter->prettyPrint($stmts);
        $ppTime += microtime(true) - $startTime;

        try {
            $startTime = microtime(true);
            $ppStmts = $parser->parse($code);
            $reparseTime += microtime(true) - $startTime;

            $startTime = microtime(true);
            $same = $nodeDumper->dump($stmts) == $nodeDumper->dump($ppStmts);
            $compareTime += microtime(true) - $startTime;

            if (!$same) {
                echo $file, ":\n    Result of initial parse and parse after pretty print differ\n";

                ++$compareFail;
            }
        } catch (PhpParser\Error $e) {
            echo $file, ":\n    Parse of pretty print failed with message: {$e->getMessage()}\n";

            ++$ppFail;
        }
    } catch (PhpParser\Error $e) {
        echo $file, ":\n    Parse failed with message: {$e->getMessage()}\n";

        ++$parseFail;
    }
}

if (0 === $parseFail && 0 === $ppFail && 0 === $compareFail) {
    echo "\n\n", 'All tests passed.', "\n";
} else {
    echo "\n\n", '==========', "\n\n", 'There were: ', "\n";
    if (0 !== $parseFail) {
        echo '    ', $parseFail,   ' parse failures.',        "\n";
    }
    if (0 !== $ppFail) {
        echo '    ', $ppFail,      ' pretty print failures.', "\n";
    }
    if (0 !== $compareFail) {
        echo '    ', $compareFail, ' compare failures.',      "\n";
    }
}

echo "\n",
     'Tested files:         ', $count,        "\n",
     "\n",
     'Reading files took:   ', $readTime,    "\n",
     'Parsing took:         ', $parseTime,   "\n",
     'Pretty printing took: ', $ppTime,      "\n",
     'Reparsing took:       ', $reparseTime, "\n",
     'Comparing took:       ', $compareTime, "\n",
     "\n",
     'Total time:           ', microtime(true) - $totalStartTime, "\n",
     'Maximum memory usage: ', memory_get_peak_usage(true), "\n";
