<?php declare(strict_types=1);

/**
 * File utility class.
 */
return new class() {
    var $file, $files = [];
    var $options = ['autoclean' => true];

    function __construct() {
        $this->file = $this->file('', true);
    }

    function __destruct() {
        if ($this->options['autoclean']) {
            $this->drop(...$this->files);
        }
    }

    /**
     * Create or return a temp file.
     */
    function file(string $prefix = null, bool $create = false): string {
        $file = tmp() . '/' . $prefix . suid(); // @sugar
        $this->files[] = $file; // For clean up.
        $create && touch($file);
        return $file;
    }

    /**
     * Open a file or create opening a new temp file.
     * @return resource
     */
    function fileOpen(string $file = null, string $mode = 'r+b') {
        $file ??= $this->file('', true);
        return fopen($file, $mode);
    }

    /**
     * Remove given files.
     */
    function drop(string ...$files): void {
        foreach ($files as $file) {
            @unlink($file);
        }
    }
};
