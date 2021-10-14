<?php

namespace ksoftm\system\utils\io;

class FileManager implements IDirectory, IFile
{
    /** @var null|string $path path fo the file. */
    protected ?string $path = null;

    /** @var null|array $ignores ignores files in a given directory. */
    protected ?array $ignores = [
        ".",
        "..",
        ".gitignore",
        ".git",
        ".htaccess",
    ];

    public function __debugInfo()
    {
        return ['PATH' => $this->path];
    }

    /**
     * Class constructor.
     */
    public function __construct(string $path, array $ignores = null)
    {
        $this->path = $path;
        if (!empty($ignores)) {
            $this->ignores = $ignores;
        }
    }

    // get the path of the file
    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string|false
    {
        if ($this->isExist()) {
            $name = pathinfo($this->path, PATHINFO_BASENAME);
        }

        return $name ?? false;
    }

    public function getNameOnly(): string|false
    {
        if ($this->isExist()) {
            $name = pathinfo($this->path, PATHINFO_FILENAME);
        }

        return $name ?? false;
    }

    public function getExtension(): string|false
    {
        if ($this->isExist()) {
            $name = pathinfo($this->path, PATHINFO_EXTENSION);
        }

        return $name ?? false;
    }

    public function getParentDir(): string|false
    {
        if ($this->isExist()) {
            $name = pathinfo($this->path, PATHINFO_DIRNAME);
        }

        return $name ?? false;
    }

    // read content using a path without stream
    public function read(bool $use_include_path = false): string|false
    {
        if ($this->isExist()) {
            return file_get_contents($this->path, $use_include_path);
        }

        return false;
    }

    public function readLines(): array|false
    {
        if ($this->isExist()) {
            $stream = fopen($this->path, 'r');

            while (($line = fgets($stream)) !== false) {
                $d[] = $line;
            }

            fclose($stream);
        }

        return $d ?? false;
    }

    public function getSize(): int|false
    {
        $size = 0;

        if ($this->isFile()) {
            $size = filesize($this->getPath());
        } else {
            foreach ($this->getDirectoryFiles($this->getPath()) as $value) {
                if ($value instanceof FileManager) {
                    $size += $value->getSize();
                }
            }
        }

        return $size ?: false;
    }

    // read content using a path with stream
    public function readStream(): string|false
    {
        if ($this->isFile()) {
            $ctx = stream_context_create();

            if (false !== ($fRes = fopen($this->path, 'r', context: $ctx))) {
                if ($this->getSize() > 1024 * 512) {
                    $readLength = 1024 * 512;
                } else {
                    $readLength = $this->getSize();
                }
                while ((false !== ($d = stream_get_contents($fRes, $readLength))) && strlen($d) > 0) {
                    echo $d;
                }
                fclose($fRes);
                return '';
            }
        }

        return false;
    }

    public function requireOnce(bool $beforeClean = false, bool $cleanCode = false): mixed
    {
        if ($this->isExist()) {
            if (ob_get_length() != false && $beforeClean) {
                ob_end_clean();
            }

            if ($cleanCode) {
                ob_start();
                require_once($this->path);
                return ob_get_clean();
            } else {
                return require_once($this->path);
            }
        }

        return false;
    }

    public function require(bool $beforeClean = false, bool $cleanCode = false): mixed
    {
        if ($this->isExist()) {
            if (ob_get_length() != false && $beforeClean) {
                ob_end_clean();
            }

            if ($cleanCode) {
                ob_start();
                require($this->path);
                return ob_get_clean();
            } else {
                return require($this->path);
            }
        }

        return false;
    }

    public function includeOnce(bool $beforeClean = false, bool $cleanCode = false): mixed
    {
        if ($this->isExist()) {
            if (ob_get_length() != false && $beforeClean) {
                ob_end_clean();
            }

            if ($cleanCode) {
                ob_start();
                include_once($this->path);
                return ob_get_clean();
            } else {
                return include_once($this->path);
            }
        }

        return false;
    }

    public function include(bool $beforeClean = false, bool $cleanCode = false): mixed
    {
        if ($this->isExist()) {
            if (ob_get_length() != false && $beforeClean) {
                ob_end_clean();
            }

            if ($cleanCode) {
                ob_start();
                include($this->path);
                return ob_get_clean();
            } else {
                return include($this->path);
            }
        }

        return false;
    }

    public function contains(string $search): bool
    {
        if ($this->isExist()) {
            return str_contains($this->read(), $search);
        }

        return false;
    }

    //TODO make this function is comfortable for array format data
    public function replace(string|array $search, string|array $data, bool $writeToFile = false): string
    {
        if ($this->isExist()) {
            if (is_string($search) && is_string($data)) {
                $d = str_replace($search, $data, $this->read());

                if ($writeToFile) {
                    $this->write($d);
                }
            }
        }

        return $d ?? '';
    }

    // read content using a path
    public function write(mixed $data = false, bool $createIfNotExist = false, $flag = FILE_TEXT): bool
    {
        if ($this->isValidDirectory($createIfNotExist) && $data != false) {
            return file_put_contents($this->path, $data, $flag) == false ? false : true;
        }

        return false;
    }

    // read content using a path
    public function clean(): bool
    {
        if ($this->isExist()) {
            return file_put_contents($this->path, '') == false ? false : true;
        }

        return false;
    }

    // check the file exist
    public function isExist(): bool
    {
        return file_exists($this->path);
    }

    public function isFile(): bool
    {
        return is_file($this->path);
    }

    public function isDirectory(): bool
    {
        return is_dir($this->path);
    }

    // check the path is valid
    public function isValidDirectory(bool $createIfNotExist = false): bool
    {
        $dir = pathinfo($this->path, PATHINFO_DIRNAME);

        if ($createIfNotExist) {
            is_dir($dir) ?: mkdir($dir, recursive: true);
        }

        return is_dir($dir);
    }

    public function getDirectoryFiles(bool $getSubFoldersFile = false): array
    {
        if ($this->isDirectory()) {

            $files = $this->openFilesInAFolder($this->path, $getSubFoldersFile);

            foreach ($files as $file) {
                if ($file instanceof FileManager) {
                    if ($file->isFile()) {
                        $output[] = $file;
                    }
                }
            }
        }
        return $output ?? [];
    }

    public function getDirectories(bool $getSubFolders = false): array
    {
        if ($this->isDirectory()) {
            return $this->openFilesInAFolder($this->path, $getSubFolders);
        }
        return [];
    }

    public function getDirectoriesOnly(bool $getSubFolders = false): array
    {
        if ($this->isDirectory()) {

            $files = $this->openFilesInAFolder($this->path, $getSubFolders);

            foreach ($files as $file) {
                if ($file instanceof FileManager) {
                    if ($file->isDirectory()) {
                        $output[] = $file;
                    }
                }
            }
        }
        return $output ?? [];
    }

    private function ignoreFiles($file): bool
    {
        return !in_array($file, $this->ignores);
    }

    private function openFilesInAFolder($path, bool $getSubFoldersFile = false): array
    {
        $files = [];

        if (false !== ($handle = opendir($path))) {
            while (false !== ($file = readdir($handle))) {
                if ($this->ignoreFiles($file)) {
                    $dirPath = $this->makeValidPath($path, $file);
                    if (is_dir($dirPath)) {
                        $files[] = new FileManager($dirPath);
                        if ($getSubFoldersFile) {
                            $files = array_merge(
                                $files,
                                $this->openFilesInAFolder($dirPath, $getSubFoldersFile)
                            );
                        }
                    } else {
                        $files[] = new FileManager($this->makeValidPath($path, $file));
                    }
                }
            }
        }
        closedir($handle);

        return $files;
    }

    private function makeValidPath($path, $file)
    {
        return $path . DIRECTORY_SEPARATOR . $file;
    }
}
