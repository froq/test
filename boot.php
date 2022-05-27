<?php
// Bootstrap file of all tests.
// $ ~/.composer/vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./
// $ ~/.composer/vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./acl/

// Path to "vendor/froq" folder.
const __froqDir = __dir__ . '/../';

// Froq loader.
$froqLoader = __froqDir . '/froq/src/Autoloader.php';
if (is_file($froqLoader)) {
    include $froqLoader;
    $loader = froq\Autoloader::init(__froqDir);
    $loader->register();
}

// Froq sugars.
$froqSugars = __froqDir . '/froq-util/src/sugars.php';
if (is_file($froqSugars)) {
    include $froqSugars;
}

// Composer loader.
$composerLoader = __froqDir . '/../vendor/autoload.php';
if (is_file($composerLoader)) {
    include $composerLoader;
}

// @cancel: Not needed yet.
// $composerLoader = __froqDir . '/vendor/autoload.php';
// if (is_file($composerLoader)) {
//     $loader = include $composerLoader;
//     $loader->addPsr4('froq\\acl\\', __dir__ . '/../src/');

//     $composerJson = file_get_contents(__dir__ . '/../composer.json');
//     $composerData = json_decode($composerJson, true);

//     // Load deps.
//     foreach ($composerData['require'] as $package => $_) {
//         if (substr($package, 0, 5) == 'froq/') {
//             $package = substr($package, 5);
//             $packagePrefix = strtr($package, '-', '\\') . '\\';
//             $packageSource = __froqDir . $package . '/src/';
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
