<?php

namespace ksoftm\utils\io;

class FileManager implements IDirectory, IFile
{
    /** @var null|string $path path fo the file. */
    protected ?string $path = null;

    /**
     * Class constructor.
     */
    public function __construct(string $path = null)
    {
        $this->path = $path;
    }

    // get the path of the file
    public function getPath(): string
    {
        return $this->path;
    }


    // read content using a path
    public function read(bool $use_include_path = false): string|false
    {
        if ($this->isExist()) {
            return file_get_contents($this->path, $use_include_path);
        }

        return false;
    }

    public function requireOnce(): string|false
    {
        if ($this->isExist()) {
            if (ob_get_length() != false) {
                ob_end_clean();
            }
            ob_start();
            require_once($this->path);
            return ob_get_clean();
        }

        return false;
    }

    public function includeOnce(): string|false
    {
        if ($this->isExist()) {
            ob_clean();
            ob_start();
            include_once($this->path);
            return ob_get_clean();
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

    public function replace(string|array $search, string|array $data, bool $writeToFile = false): string
    {
        if ($this->isExist()) {
            $d = str_replace($search, $data, $this->read());

            if ($writeToFile) {
                $this->write($d);
            }
        }

        return $d ?? '';
    }

    // read content using a path
    public function write(mixed $data = false, bool $createIfNotExist = false, $flag = FILE_TEXT): bool
    {
        if ($this->isValidDirectory($createIfNotExist)) {
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

    // check the path is valid
    public function isValidDirectory(bool $createIfNotExist = false): bool
    {
        $dir = pathinfo($this->path, PATHINFO_DIRNAME);

        if (!is_dir($dir) && $createIfNotExist) {
            mkdir($dir);
        }

        return is_dir($dir);
    }

    // self reference
    public function getDirectoryFileNames(bool $getSubFoldersFile = false): array
    {
        if (is_dir($this->path)) {
            return $this->openFilesInAFolder($this->path, $getSubFoldersFile);
        }
        return [];
    }

    // self reference
    public function getDirectories(bool $getSubFolders = false): array
    {
        if (is_dir($this->path)) {
            return $this->openFolder($this->path, $getSubFolders);
        }
        return [];
    }

    // self reference
    public function getDirectoriesOnly(bool $getSubFolders = false): array
    {
        if (is_dir($this->path)) {
            return $this->openFolderOnly($this->path, $getSubFolders);
        }
        return [];
    }

    // self reference
    private function ignoreFiles($file): bool
    {
        $ignores = [
            ".",
            "..",
            "autoload.php",
            ".gitignore",
            ".htaccess",
        ];

        foreach ($ignores as $ignore) {

            if ($file === $ignore) {
                return false;
            }
        }
        return true;
    }

    // self reference
    private function openFilesInAFolder($path, bool $getSubFoldersFile = false): array
    {
        $files = [];

        if (false !== ($handle = opendir($path))) {
            while (false !== ($file = readdir($handle))) {
                if ($this->ignoreFiles($file)) {
                    $dirPath = $this->makeValidPath($path, $file);
                    if (is_dir($dirPath)) {
                        if ($getSubFoldersFile) {
                            foreach ($this->openFilesInAFolder($dirPath, $getSubFoldersFile) as $value) {
                                $files[] = new FileManager($value);
                            }
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

    // self reference
    private function openFolderOnly($path, bool $getSubFolders = false): array
    {
        $files[] = new FileManager($path);
        if (false !== ($handle = opendir($path))) {
            while (false !== ($file = readdir($handle))) {
                if ($this->ignoreFiles($file)) {
                    $dirPath = $this->makeValidPath($path, $file);
                    if (is_dir($dirPath)) {
                        if ($getSubFolders) {
                            foreach ($this->openFolderOnly($dirPath, $getSubFolders) as $value) {
                                $files[] = new FileManager($value);
                            }
                        }
                    }
                }
            }
        }
        closedir($handle);
        return $files ?? [];
    }

    // self reference
    private function openFolder($path, bool $getSubFolders = false): array
    {
        $files[] = new FileManager($path);
        if (false !== ($handle = opendir($path))) {
            while (false !== ($file = readdir($handle))) {
                if ($this->ignoreFiles($file)) {
                    $dirPath = $this->makeValidPath($path, $file);
                    if (is_dir($dirPath)) {
                        if ($getSubFolders) {
                            foreach ($this->openFolder($dirPath, $getSubFolders) as $value) {
                                $files[] = new FileManager($value);
                            }
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

    // self reference
    private function makeValidPath($path, $file)
    {
        return $path . DIRECTORY_SEPARATOR . $file;
    }
}
