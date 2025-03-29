<?php

declare(strict_types=1);

namespace ExeQue\PlaceholdDotCo\Cache\Distinct;

use DateInterval;
use DateTime;
use RuntimeException;

class FileStore extends Store
{
    public function __construct(
        private string $directory,
        private string $prefix = 'placehold.co'
    ) {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $key = $this->ensureKeyValidity($key);
        $file = $this->fileName($key);

        if (is_file($file) === false) {
            return $default;
        }

        $contents = file_get_contents($file);

        $timestamp = (int)substr($contents, 0, 12);
        $data = substr($contents, 12);

        $expires = new DateTime('@' . $timestamp);

        if ($this->isExpired($expires)) {
            $this->delete($key);

            return $default;
        }

        return unserialize($data, ['allowed_classes' => true]);
    }

    public function set(string $key, mixed $value, DateInterval|int|null $ttl = null): bool
    {
        $key = $this->ensureKeyValidity($key);
        $expires = $this->resolveTtl($ttl);
        $file = $this->fileName($key);

        // @codeCoverageIgnoreStart
        if (is_dir(dirname($file)) === false &&
            mkdir(dirname($file), 0755, true) === false &&
            is_dir(dirname($file)) === false) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $this->directory));
        }
        // @codeCoverageIgnoreEnd

        $timestamp = str_pad($expires->format('U'), 12, '0', STR_PAD_LEFT);

        $data = $timestamp . serialize($value);

        return file_put_contents($file, $data) !== false;
    }

    public function delete(string $key): bool
    {
        $key = $this->ensureKeyValidity($key);
        $file = $this->fileName($key);

        if (file_exists($file) === false) {
            return false;
        }

        return unlink($file);
    }

    public function clear(): bool
    {
        $files = glob($this->directory . '/' . $this->prefix . '/*');

        // @codeCoverageIgnoreStart
        if ($files === false) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    public function has(string $key): bool
    {
        $key = $this->ensureKeyValidity($key);
        $file = $this->fileName($key);

        if (is_file($file) === false) {
            return false;
        }

        $stream = fopen($file, 'rb');

        $expires = fread($stream, 12);
        fclose($stream);

        $expires = new DateTime('@' . (int) $expires);

        if ($this->isExpired($expires)) {
            $this->delete($key);

            return false;
        }

        return true;
    }

    private function fileName(string $key): string
    {
        return sprintf('%s/%s/%s', $this->directory, $this->prefix, hash('sha256', $key));
    }
}
