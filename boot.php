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

use PHPUnit\Util\Blacklist;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    function assertLength(int $length, string $string, string $message = ''): void
    {
        if (strlen($string) == $length) {
            $this->okay();
            return;
        }

        try {
            $this->fail(sprintf(
                'Failed asserting that length %d is identical to %d.',
                strlen($string), $length
            ));
        } catch (Throwable $error) {
            $this->throw($error, $message, __function__);
        }
    }

    function assertStringContains(string $needle, string $haystack, bool $icase = false, string $message = ''): void
    {
        try {
            $icase ? $this->assertStringContainsStringIgnoringCase($needle, $haystack)
                   : $this->assertStringContainsString($needle, $haystack);
        } catch (Throwable $error) {
            $this->throw($error, $message, __function__);
        }
    }

    function assertStringNotContains(string $needle, string $haystack, bool $icase = false, string $message = ''): void
    {
        try {
            $icase ? $this->assertStringNotContainsStringIgnoringCase($needle, $haystack)
                   : $this->assertStringNotContainsString($needle, $haystack);
        } catch (Throwable $error) {
            $this->throw($error, $message, __function__);
        }
    }

    private function okay(): void
    {
        // Faking "This test did not perform any assertions" error.
        $this->assertTrue(true);
    }

    private function throw(Throwable $error, string $message, string $function): string
    {
        $this->checkBlacklist();

        // Separate given error as original.
        $message && $message .= PHP_EOL;

        // Append call path to message as original, cos Blacklist'ed this file.
        $message .= $error->getMessage() . PHP_EOL . PHP_EOL;
        $message .= $this->getCallPath($error->getTrace(), $function);

        throw new AssertionFailedError($message);
    }

    private function getCallPath(array $trace, string $function): string
    {
        foreach ($trace as $trace) {
            if ($trace['function'] == $function) {
                return sprintf('%s:%s', $trace['file'], $trace['line']);
            }
        }
        return '[unknown]';
    }

    private function checkBlacklist(): void
    {
        // To remove this file from error trace.
        Blacklist::$blacklistedClassNames[TestCase::class] ??= 1;
    }
}
