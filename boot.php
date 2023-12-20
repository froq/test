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
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    function __destruct()
    {
        if (method_exists($this, 'dinit')) {
            $this->dinit();
        }
    }

    static function assertLength(int $length, string $string, string $message = ''): void
    {
        self::assert(
            strlen($string) === $length,
            'Failed asserting that length of `%s`, `%d` is identical to `%d`.',
            [$string, strlen($string), $length], $message
        );
    }

    // static function assertEqual(mixed $expected, mixed $actual, string $message = ''): void
    // {
    //     $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'][] = __FILE__;
    //     try {
    //         self::assertEquals($expected, $actual, $message);
    //     } catch (\Throwable $error) {
    //         throw $error;
    //         array_delete($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], __FILE__);
    //     }
    //     self::okay();
    // }

    // static function assertNotEqual(mixed $expected, mixed $actual, string $message = ''): void
    // {
    //     $GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'][] = __FILE__;
    //     try {
    //         self::assertNotEquals($expected, $actual, $message);
    //     } catch (\Throwable $error) {
    //         throw $error;
    //         array_delete($GLOBALS['__PHPUNIT_ISOLATION_EXCLUDE_LIST'], __FILE__);
    //     }
    //     self::okay();
    // }

    static function assertStringContains(string $needle, string $haystack, bool $icase = false, string $message = ''): void
    {
        self::assert(
            str_has($haystack, $needle, $icase) === true,
            'Failed asserting that string `%s` contains `%s`',
            [$haystack, $needle], $message
        );
    }

    static function assertStringNotContains(string $needle, string $haystack, bool $icase = false, string $message = ''): void
    {
        self::assert(
            str_has($haystack, $needle, $icase) === false,
            'Failed asserting that string `%s` not contains `%s`',
            [$haystack, $needle], $message
        );
    }

    static function assertMatches(string $pattern, string $subject, string $message = ''): void
    {
        self::assert(
            preg_test($pattern, $subject) === true,
            'Failed asserting that subject `%s` matches pattern `%s`.',
            [$subject, $pattern], $message
        );
    }

    static function assertNotMatches(string $pattern, string $subject, string $message = ''): void
    {
        self::assert(
            preg_test($pattern, $subject) === false,
            'Failed asserting that subject `%s` not matches pattern `%s`.',
            [$subject, $pattern], $message
        );
    }

    static function assertTypeOf(string $type, mixed $input, string $message = ''): void
    {
        self::assert(
            is_type_of($input, $type) === true,
            'Failed asserting that `%t` is type of `%s`',
            [$input, $type], $message
        );
    }

    static function assertNotTypeOf(string $type, mixed $input, string $message = ''): void
    {
        self::assert(
            is_type_of($input, $type) === false,
            'Failed asserting that `%t` is not type of `%s`',
            [$input, $type], $message
        );
    }

    static function assertClassOf(string|object|array $class, string|object $subclass, string $message = ''): void
    {
        self::assert(
            is_class_of(
                $subclass = get_class_name($subclass),
                ...($class = is_array($class) ? $class : [$class])
            ) === true,
            'Failed asserting that `%s` is class of `%A`.',
            [$subclass, $class], $message
        );
    }

    static function assertNotClassOf(string|object|array $class, string|object $subclass, string $message = ''): void
    {
        self::assert(
            is_class_of(
                $subclass = get_class_name($subclass),
                ...($class = is_array($class) ? $class : [$class])
            ) === false,
            'Failed asserting that `%s` is class of `%A`.',
            [$subclass, $class], $message
        );
    }

    static function assertSubclassOf(string|object $class, string|object $subclass, string $message = ''): void
    {
        self::assert(
            is_subclass_of(
                $subclass = get_class_name($subclass),
                $class = get_class_name($class)
            ) === true,
            'Failed asserting that `%s` is subclass of `%s`',
            [$subclass, $class], $message
        );
    }

    static function assertNotSubclassOf(string|object $class, string|object $subclass, string $message = ''): void
    {
        self::assert(
            is_subclass_of(
                $subclass = get_class_name($subclass),
                $class = get_class_name($class)
            ) === false,
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

    static function assertClassExists(string $class, string $message = ''): void
    {
        self::assert(
            class_exists($class) === true,
            'Failed asserting that class `%s` exists.',
            [$class], $message
        );
    }
    static function assertClassNotExists(string $class, string $message = ''): void
    {
        self::assert(
            class_exists($class) === false,
            'Failed asserting that class `%s` not exists.',
            [$class], $message
        );
    }

    static function assertClassMethodExists(string $class, string $method, string $message = ''): void
    {
        self::assert(
            method_exists($class, $method) === true,
            'Failed asserting that class method `%s::%s` exists.',
            [$class, $method], $message
        );
    }
    static function assertClassMethodNotExists(string $class, string $method, string $message = ''): void
    {
        self::assert(
            method_exists($class, $method) === false,
            'Failed asserting that class method `%s::%s` not exists.',
            [$class, $method], $message
        );
    }

    static function assertClassConstantExists(string $class, string $constant, string $message = ''): void
    {
        self::assert(
            constant_exists($class, $constant) === true,
            'Failed asserting that class constant `%s::%s` exists.',
            [$class, $constant], $message
        );
    }
    static function assertClassConstantNotExists(string $class, string $constant, string $message = ''): void
    {
        self::assert(
            constant_exists($class, $constant) === false,
            'Failed asserting that class constant `%s::%s` not exists.',
            [$class, $constant], $message
        );
    }

    static function assertClassPropertyExists(string $class, string $property, string $message = ''): void
    {
        self::assert(
            property_exists($class, $property) === true,
            'Failed asserting that class property `%s::%s` exists.',
            [$class, $property], $message
        );
    }
    static function assertClassPropertyNotExists(string $class, string $property, string $message = ''): void
    {
        self::assert(
            property_exists($class, $property) === false,
            'Failed asserting that class property `%s::%s` not exists.',
            [$class, $property], $message
        );
    }

    static function assertFunctionExists(string $function, string $message = ''): void
    {
        self::assert(
            function_exists($function) === true,
            'Failed asserting that function `%s` exists.',
            [$function], $message
        );
    }
    static function assertFunctionNotExists(string $function, string $message = ''): void
    {
        self::assert(
            function_exists($function) === false,
            'Failed asserting that function `%s` not exists.',
            [$function], $message
        );
    }

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
