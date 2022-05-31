<?php
// Bootstrap file of all tests.
// $ vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./
// $ vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./acl/

if (PHP_SAPI != 'cli') {
    echo 'This file must be run via CLI only!', PHP_EOL;
    exit(1);
}

// Path to "vendor/froq" folder.
$froqDir = __dir__ . '/vendor/froq';

// Froq loader.
$froqLoader = $froqDir . '/froq/src/Autoloader.php';
if (is_file($froqLoader)) {
    include $froqLoader;
    $loader = froq\Autoloader::init($froqDir);
    $loader->register();
}

// Froq sugars.
$froqSugars = $froqDir . '/froq-util/src/sugars.php';
if (is_file($froqSugars)) {
    include $froqSugars;
}

// Composer loader.
$composerLoader = $froqDir . '/../vendor/autoload.php';
if (is_file($composerLoader)) {
    include $composerLoader;
}

// @cancel: Not needed yet.
// if (is_file($composerLoader)) {
//     $loader->addPsr4('froq\\acl\\', __dir__ . '/../src/');

//     $composerJson = file_get_contents(__dir__ . '/../composer.json');
//     $composerData = json_decode($composerJson, true);

//     // Load deps.
//     foreach ($composerData['require'] as $package => $_) {
//         if (substr($package, 0, 5) == 'froq/') {
//             $package = substr($package, 5);
//             $packagePrefix = strtr($package, '-', '\\') . '\\';
//             $packageSource = $froqDir . $package . '/src/';
//             $loader->addPsr4($packagePrefix, $packageSource);
//         }
//     }
//     // Load files.
//     if (isset($composerData['autoload']['files'])) {
//         foreach ($composerData['autoload']['files'] as $file) {
//             include $file;
//         }
//     }
// }
