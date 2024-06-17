<?php

namespace App\Service\Storage;

interface StorageInterface
{
    /**
     * @param string $directory
     * @return void
     */
    public function makeDirectory(string $directory): void;

    /**
     * @param string $path
     * @param string $file
     * @param string $content
     * @param bool $append
     * @return void
     */
    public function write(string $path, string $file, string $content, bool $append = false): void;
}