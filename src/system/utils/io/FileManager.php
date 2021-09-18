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


    // read content using a path
    //TODO in the future this will change [file_get_contents] to [stream_get_contents]
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
            if (ob_get_length() != false) {
                ob_end_clean();
            }
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

    //TODO make this function is comfortable for array format data
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
        is_dir($dir) ?: mkdir($dir, recursive: true);
        return is_dir($dir);
    }

    public function getDirectoryFiles(bool $getSubFoldersFile = false): array
    {
        if (is_dir($this->path)) {

            $files = $this->openFilesInAFolder($this->path, $getSubFoldersFile);

            foreach ($files as $file) {
                if ($file instanceof FileManager) {
                    if (is_file($file->getPath())) {
                        $output[] = $file;
                    }
                }
            }
        }
        return $output ?? [];
    }

    public function getDirectories(bool $getSubFolders = false): array
    {
        if (is_dir($this->path)) {
            return $this->openFilesInAFolder($this->path, $getSubFolders);
        }
        return [];
    }

    public function getDirectoriesOnly(bool $getSubFolders = false): array
    {
        if (is_dir($this->path)) {

            $files = $this->openFilesInAFolder($this->path, $getSubFolders);

            foreach ($files as $file) {
                if ($file instanceof FileManager) {
                    if (is_dir($file->getPath())) {
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
        $files[] = new FileManager($path);

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
