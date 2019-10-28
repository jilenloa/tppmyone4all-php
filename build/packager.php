<?php
require __DIR__ . '/Burgomaster.php';

$stageDirectory = __DIR__ . '/artifacts/staging';
$projectRoot = __DIR__ . '/../';
$packager = new \Burgomaster($stageDirectory, $projectRoot);

// Copy basic files to the stage directory. Note that we have chdir'd onto
// the $projectRoot directory, so use relative paths.
foreach (['README.md'] as $file) {
    $packager->deepCopy($file, $file);
}

// Copy each dependency to the staging directory. Copy *.php and *.pem files.
$packager->recursiveCopy('src', 'MyOne4All', ['php']);
$packager->recursiveCopy('vendor/guzzlehttp/guzzle/src', 'GuzzleHttp');
$packager->recursiveCopy('vendor/guzzlehttp/promises/src', 'GuzzleHttp/Promise');
$packager->recursiveCopy('vendor/guzzlehttp/psr7/src', 'GuzzleHttp/Psr7');
$packager->recursiveCopy('vendor/psr/http-message/src', 'Psr/Http/Message');

$packager->createAutoloader([
    'GuzzleHttp/functions_include.php',
    'GuzzleHttp/Psr7/functions_include.php',
    'GuzzleHttp/Promise/functions_include.php',
]);

$packager->createPhar(__DIR__ . '/artifacts/tppmyone4all-php.phar');
$packager->createZip(__DIR__ . '/artifacts/tppmyone4all-php.zip');