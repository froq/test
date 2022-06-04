<?php
// Bootstrap file of all tests.
// $ vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./
// $ vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./acl/

if (PHP_SAPI != 'cli') {
    echo 'This file must be run via CLI only!', PHP_EOL;
    exit(1);
}

// @important
date_default_timezone_set('UTC');

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
$composerLoader = __dir__ . '/vendor/autoload.php';
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

// Apply exclusions.
$excludeList = new PHPUnit\Util\ExcludeList();

// Must be re-write here (as Composer does).
$phpunitBin = realpath(__dir__ . '/vendor/bin/phpunit');
$phpunitDir = realpath(dirname($phpunitBin) . '/../phpunit/phpunit');
$excludeList->addDirectory($phpunitDir);

// Drop Froq! dir from trace stack (as Composer does).
foreach (glob(__dir__ . '/vendor/*') as $item) {
    $item = realpath($item);
    if (is_dir($item)) foreach (glob($item . '/*') as $item) {
        if (is_dir($item)) $excludeList->addDirectory(realpath($item));
    }
}

// Drop this file from trace stack (as Composer does in bin/phpunit file).
$GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'][] = __file__;

// Base test case for all test classes.
abstract class TestCase extends PHPUnit\Framework\TestCase
{
    function assertLength(int $length, string $string, string $message = ''): void
    {
        $this->assert(
            strlen($string) === $length,
            'Failed asserting that length of `%s`, `%d` is identical to `%d`.',
            [$string, strlen($string), $length], $message
        );
    }

    function assertStringContains(string $needle, string $haystack, bool $icase = false, string $message = ''): void
    {
        $this->assert(
            str_has($haystack, $needle, $icase) === true,
            'Failed asserting that string `%s` contains `%s`',
            [$haystack, $needle], $message
        );
    }

    function assertStringNotContains(string $needle, string $haystack, bool $icase = false, string $message = ''): void
    {
        $this->assert(
            str_has($haystack, $needle, $icase) === false,
            'Failed asserting that string `%s` not contains `%s`',
            [$haystack, $needle], $message
        );
    }

    function assertMatches(string $pattern, string $subject, string $message = ''): void
    {
        $this->assert(
            preg_test($pattern, $subject) === true,
            'Failed asserting that subject `%s` matches pattern `%s`.',
            [$subject, $pattern], $message
        );
    }

    function assertNotMatches(string $pattern, string $subject, string $message = ''): void
    {
        $this->assert(
            preg_test($pattern, $subject) === false,
            'Failed asserting that subject `%s` not matches pattern `%s`.',
            [$subject, $pattern], $message
        );
    }

    function assertTypeOf(string $type, mixed $input, string $message = ''): void
    {
        $this->assert(
            is_type_of($input, $type) === true,
            'Failed asserting that `%t` is type of `%s`',
            [$input, $type], $message
        );
    }

    function assertNotTypeOf(string $type, mixed $input, string $message = ''): void
    {
        $this->assert(
            is_type_of($input, $type) === false,
            'Failed asserting that `%t` is not type of `%s`',
            [$input, $type], $message
        );
    }

    private function assert(bool $assertion, string $format, array $formatArgs, string $message): void
    {
        if (!$assertion) {
            $message && $message .= PHP_EOL;
            $message .= format($format, ...$formatArgs);
            $this->fail($message);
        }
        $this->okay();
    }

    private function okay(): void
    {
        // Faking "This test did not perform any assertions" error.
        $this->assertTrue(true);
    }
}
