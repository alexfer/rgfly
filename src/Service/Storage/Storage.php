<?php

namespace App\Service\Storage;

use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class Storage implements StorageInterface
{

    public const string EXT = '.data';
    /**
     * @var int
     */
    private int $permission = 0777;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @var mixed
     */
    private static mixed $fs;

    /**
     * @param string $storage
     */
    public function __construct(private readonly string $storage)
    {
        $this->filesystem = new Filesystem();
        self::$fs = new $this->filesystem;
        $this->initial($storage);
    }

    /**
     * @param string $path
     * @param string $content
     * @return void
     */
    public function write(string $path, string $file, string $content, bool $append = false): void
    {
        $file = sprintf("%s/%s/%s", $this->storage, $path, $file);

        if (!$this->filesystem->exists($file)) {
            $this->filesystem->touch($file);
            $this->filesystem->dumpFile($file, $content);
        }
        if ($append && $this->filesystem->exists($file)) {
            $this->filesystem->appendToFile($file, $content);
        }
    }

    /**
     * @param string $storage
     * @param string $dir
     * @param string $file
     * @return string
     */
    public static function data(string $storage, string $dir, string $file): string
    {
        $file = $file . self::EXT;
        $dir = $storage . DIRECTORY_SEPARATOR . $dir;

        if (Kernel::VERSION >= 7.1) {
            return self::$fs->readFile($dir . DIRECTORY_SEPARATOR . $file);
        }
        return file_get_contents($dir . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function existsDirectory(string $directory): bool
    {
        return $this->filesystem->exists($directory);
    }

    /**
     * @param string $storage
     * @return void
     */
    private function initial(string $storage): void
    {
        if (!!$this->existsDirectory($storage)) {
            try {
                $this->filesystem->mkdir($storage, $this->permission);
            } catch (IOExceptionInterface $exception) {
                throw new IOException($exception->getMessage());
            }
        }
    }

    /**
     * @param string $directory
     * @return void
     */
    public function makeDirectory(string $directory): void
    {

        if (!$this->existsDirectory($this->storage . '/' . $directory)) {
            try {
                $this->filesystem->mkdir($this->storage . '/' . $directory, $this->permission);
            } catch (IOExceptionInterface $exception) {
                throw new IOException($exception->getMessage());
            }
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->storage;
    }

}