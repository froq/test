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

use PHPUnit\Util\ExcludeList;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    function assertLength(int $length, string $string, string $message = ''): void
    {
        // @keep
        // try {
        //     if (strlen($string) != $length) {
        //         $this->fail(sprintf(
        //             'Failed asserting that string length `%d` is identical to `%d`.',
        //             strlen($string), $length
        //         ));
        //     }
        // } catch (Throwable $error) {
        //     $this->throw($error, $message, __function__);
        // }

        if (strlen($string) != $length) {
            $error = $this->error(
                'Failed asserting that string length `%d` is identical to `%d`.',
                strlen($string), $length
            );
            $this->throw($error, $message, __function__);
        }

        $this->okay();
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

    function assertMatches(string $pattern, string $string, string $message = ''): void
    {
        try {
            $this->assertMatchesRegularExpression($pattern, $string, $message);
        } catch (Throwable $error) {
            $this->throw($error, $message, __function__);
        }
    }

    function assertNotMatches(string $pattern, string $string, string $message = ''): void
    {
        try {
            $this->assertDoesNotMatchRegularExpression($pattern, $string, $message);
        } catch (Throwable $error) {
            $this->throw($error, $message, __function__);
        }
    }

    function assertTypeOf(string $type, mixed $input, string $message = ''): void
    {
        if (!Assert::type($input, $type)) {
            $error = $this->error('Failed asserting that `%t` is type of `%s`', $input, $type);
            $this->throw($error, $message, __function__);
        }

        $this->okay();
    }

    function assertNotTypeOf(string $type, mixed $input, string $message = ''): void
    {
        if (Assert::type($input, $type)) {
            $error = $this->error('Failed asserting that `%t` is not type of `%s`', $input, $type);
            $this->throw($error, $message, __function__);
        }

        $this->okay();
    }

    private function okay(): void
    {
        // Faking "This test did not perform any assertions" error.
        $this->assertTrue(true);
    }

    private function error(string $message, mixed ...$messageParams): Throwable
    {
        return new Error(format($message, ...$messageParams));
    }

    private function throw(Throwable $error, string $message, string $function): string
    {
        $this->checkExcludeList();

        // Separate given error as original.
        $message && $message .= PHP_EOL;

        // Append call path to message as original, cos ExcludeList'ed this file.
        $message .= $error->getMessage() . PHP_EOL . PHP_EOL;
        $message .= $this->getCallPath($error->getTrace(), $function);

        throw new AssertionFailedError($message);
    }

    private function getCallPath(array $traces, string $function): string
    {
        foreach ($traces as $trace) {
            if ($trace['function'] == $function) {
                return sprintf('%s:%s', $trace['file'], $trace['line']);
            }
        }
        return '[unknown]';
    }

    private function checkExcludeList(): void
    {
        // To remove this file from error trace.
        $excludeList = new ExcludeList();
        $excludeList->isExcluded(__file__) || $excludeList::addDirectory(__dir__);
    }
}
