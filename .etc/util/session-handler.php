<?php
namespace foo\bar;

if (!class_exists(SessionHandler::class, false)) {
    class SessionHandler extends \froq\session\SessionHandler {
        // @cancel: Not needed anymore.
        // function open($a,$b): bool { return true; }
        // function close(): bool { return true; }
        // function write($a,$b): bool { return true; }
        // function read($a): string { return ""; }
        // function destroy($a): bool { return true; }
        // function gc($a): int { return 0; }
    }
}
