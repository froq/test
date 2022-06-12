<?php declare(strict_types=1);

/**
 * File utility class for working temp files.
 */
return new class() {
    var $file, $files = [];
    var $options = ['autoclean' => true];

    function __construct() {
        $this->file = $this->fileMake();
    }

    function __destruct() {
        if ($this->options['autoclean']) {
            $this->drop(...$this->files);
        }
    }

    /**
     * Create or return a temp file.
     */
    function file(string $prefix = 'test', bool $create = false): string {
        $file = tmp() . '/' . $prefix . suid(); // @sugar
        $this->files[] = $file; // For clean up.
        $create && touch($file);
        return $file;
    }

    /**
     * Create and return a temp file.
     */
    function fileMake(string $prefix = 'test'): string {
        return $this->file($prefix, true);
    }

    /**
     * Open a file or create opening a new temp file.
     * @return resource
     */
    function fileOpen(string $file = null, string $mode = 'r+b') {
        return fopen($file ?? $this->fileMake(), $mode);
    }

    /**
     * Return froq image.
     */
    function image(): string {
        return realpath(__dir__ . '/../img/froq.png');
    }

    /**
     * Copy image to temp directory.
     */
    function imageMake(): string {
        $image = $this->file() . '.png';
        copy($this->image(), $image);
        return $image;
    }

    /**
     * Remove given files.
     */
    function drop(string ...$files): void {
        foreach ($files as $file) {
            is_file($file) && unlink($file);
            is_file($file . '.png') && unlink($file . '.png');
        }
    }
};
