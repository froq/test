<?php
// Bootstrap file of all tests.
// $ vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./
// $ vendor/bin/phpunit --verbose --colors --bootstrap=./boot.php ./acl/

if (PHP_SAPI !== 'cli') {
    echo 'This file must be run via CLI only!', PHP_EOL;
    exit(1);
}

// @important
date_default_timezone_set('UTC');

// Path to "vendor/froq" folder.
$froqDir = __DIR__ . '/vendor/froq';

// Froq loader.
$froqLoader = $froqDir . '/froq/src/Autoloader.php';
if (is_file($froqLoader)) {
    require $froqLoader;
    $loader = froq\Autoloader::init($froqDir);
    $loader->register();
}

// Froq sugars.
$froqSugars = $froqDir . '/froq-util/src/sugars.php';
if (is_file($froqSugars)) {
    require $froqSugars;
}

// Composer loader.
$composerLoader = __DIR__ . '/vendor/autoload.php';
if (is_file($composerLoader)) {
    require $composerLoader;
}

// @cancel: Not needed yet.
// if (is_file($composerLoader)) {
//     $loader->addPsr4('froq\\acl\\', __DIR__ . '/../src/');

//     $composerJson = file_get_contents(__DIR__ . '/../composer.json');
//     $composerData = json_decode($composerJson, true);

//     // Load deps.
//     foreach ($composerData['require'] as $package => $_) {
//         if (substr($package, 0, 5) === 'froq/') {
//             $package = substr($package, 5);
//             $packagePrefix = strtr($package, '-', '\\') . '\\';
//             $packageSource = $froqDir . $package . '/src/';
//             $loader->addPsr4($packagePrefix, $packageSource);
//         }
//     }
//     // Load files.
//     if (isset($composerData['autoload']['files'])) {
//         foreach ($composerData['autoload']['files'] as $file) {
//             require $file;
//         }
//     }
// }

// // Apply exclusions.
// $excludeList = new PHPUnit\Util\ExcludeList();

// // Must be re-write here (as Composer does).
// $phpunitBin = realpath(__DIR__ . '/vendor/bin/phpunit');
// $phpunitDir = realpath(dirname($phpunitBin) . '/../phpunit/phpunit');
// $excludeList->addDirectory($phpunitDir);

// // Drop Froq! dir from trace stack (as Composer does).
// foreach (glob(__DIR__ . '/vendor/*') as $item) {
//     $item = realpath($item);
//     if (is_dir($item)) foreach (glob($item . '/*') as $item) {
//         if (is_dir($item)) $excludeList->addDirectory(realpath($item));
//     }
// }

// // Drop this file from trace stack (as Composer does in bin/phpunit file).
// $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'][] = __FILE__;

// Base test case for all test classes.
abstract class TestCase extends PHPUnit\Framework\TestCase
{
    function __construct()
    {
        parent::__construct();
        if (method_exists($this, 'before')) {
            $this->before();
        }
    }

    function __destruct()
    {
        if (method_exists($this, 'after')) {
            $this->after();
        }
    }

    function assertLength(int $length, string $string, string $message = ''): void
    {
        $this->assert(
            strlen($string) === $length,
            'Failed asserting that length of `%s`, `%d` is identical to `%d`.',
            [$string, strlen($string), $length], $message
        );
    }

    // function assertEqual(mixed $expected, mixed $actual, string $message = ''): void
    // {
    //     $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'][] = __FILE__;
    //     try {
    //         $this->assertEquals($expected, $actual, $message);
    //     } catch (\Throwable $error) {
    //         throw $error;
    //         array_delete($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], __FILE__);
    //     }
    //     self::okay();
    // }

    // function assertNotEqual(mixed $expected, mixed $actual, string $message = ''): void
    // {
    //     $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'][] = __FILE__;
    //     try {
    //         $this->assertNotEquals($expected, $actual, $message);
    //     } catch (\Throwable $error) {
    //         throw $error;
    //         array_delete($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], __FILE__);
    //     }
    //     self::okay();
    // }

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

    function assertSubclassOf(string|object $class, string|object $subclass, string $message = ''): void
    {
        $this->assert(
            is_subclass_of($subclass = get_class_name($subclass), $class = get_class_name($class)) === true,
            'Failed asserting that `%s` is subclass of `%s`',
            [$subclass, $class], $message
        );
    }

    function assertNotSubclassOf(string|object $class, string|object $subclass, string $message = ''): void
    {
        $this->assert(
            is_subclass_of($subclass = get_class_name($subclass), $class = get_class_name($class)) === false,
            'Failed asserting that `%s` is not subclass of `%s`',
            [$subclass, $class], $message
        );
    }

    /** @override */
    static function assertFileNotExists(string $file, string $message = ''): void
    {
        self::assert(
            file_exists($file) === false,
            'Failed asserting that file `%s` not exists.',
            [$file], $message
        );
    }

    // function assertDirectoryNotExists() {}

    static function assert(bool $assertion, string $format, array $formatArgs, string $message): void
    {
        if (!$assertion) {
            $message && $message .= PHP_EOL;
            $message .= format($format, ...$formatArgs);
            self::fail($message);
        }
        self::okay();
    }

    /** Faking "This test did not perform any assertions" error. */
    static function okay(): void
    {
        self::assertTrue(true);
    }

    protected mixed $util = null;

    /** Get an etc file constents. */
    protected function etc(string $file): mixed
    {
        $file = sprintf('%s/.etc/%s.php', __DIR__, $file);
        // $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'][] = $file;
        return require $file;
    }

    /** Get an etc/util file constents. */
    protected function util(string $name): mixed
    {
        return $this->etc('util/' . $name . '-util');
    }
}
