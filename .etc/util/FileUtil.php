<?php declare(strict_types=1);

/**
 * File utility class for working temp files.
 */
return new class() {
    var $base;
    // var $dirs = [], $files = [];
    var $options = ['autoclean' => true];

    function __construct() {
        $this->base = tmp() . '/froq-test';
        dirmake($this->base); // Ensure.
    }

    function __destruct() {
        if ($this->options['autoclean']) {
            $glob = glob($this->base . '/*');
            $this->drop(...$glob);
        }
    }

    /**
     * Create or return a temp dir.
     */
    function dir(string $prefix = '', bool $make = false): string {
        $dir = $this->base . '/' . $prefix . suid();
        // $this->dirs[] = $dir; // For clean up.
        $make && dirmake($dir, 0777);
        return $dir;
    }

    /**
     * Create and return a temp file.
     */
    function dirMake(string $prefix = ''): string {
        return $this->dir($prefix, true);
    }

    /**
     * Create or return a temp file.
     */
    function file(string $prefix = '', bool $make = false): string {
        $file = $this->base . '/' . $prefix . suid();
        // $this->files[] = $file; // For clean up.
        $make && filemake($file, 0777);
        return $file;
    }

    /**
     * Create and return a temp file.
     */
    function fileMake(string $prefix = '', string $contents = null): string {
        $file = $this->file($prefix, true);
        $contents && file_put_contents($file, $contents);
        return $file;
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
     * Remove given dirs/files.
     */
    function drop(string ...$paths): void {
        foreach ($paths as $path) {
            is_dir($path) && $this->dropDirs($path);
            is_file($path) && $this->dropFiles($path);
        }
    }

    /**
     * Remove given dirs.
     */
    function dropDirs(string ...$dirs): void {
        foreach ($dirs as $dir) {
            $glob = glob($dir . '/*');
            $this->dropDirs(...filter($glob, 'is_dir'));
            $this->dropFiles(...filter($glob, 'is_file'));
            is_dir($dir) && rmdir($dir);
        }
    }

    /**
     * Remove given files.
     */
    function dropFiles(string ...$files): void {
        foreach ($files as $file) {
            is_file($file) && unlink($file);
            is_file($file . '.png') && unlink($file . '.png');
        }
    }
};
