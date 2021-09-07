<?php

namespace ksoftm\utils\io;


interface IFile
{
    /**
     * read the given file using its path
     *
     * @param boolean $use_include_path
     *
     * @return string|false
     */
    function read(bool $use_include_path = false): string|false;

    /**
     * write text in a given path
     *
     * @param mixed $data
     * @param boolean $createIfNotExist
     * @param int $flag
     *
     * @return boolean
     */
    function write(mixed $data = false, bool $createIfNotExist = false, int $flag = FILE_TEXT): bool;

    /**
     * check some text is given in the file
     *
     * @param string $search
     *
     * @return boolean
     */
    function contains(string $search): bool;

    /**
     * request the given path file
     *
     * @return string|false
     */
    function requireOnce(): string|false;

    /**
     * include the given path file
     *
     * @return string|false
     */
    function includeOnce(): string|false;

    /**
     * clean the given path file
     *
     * @return boolean
     */
    function clean(): bool;

    /**
     * replace some text into new text using given path
     *
     * @param string|array $search
     * @param string|array $data
     * @param boolean $writeToFile
     *
     * @return string
     */
    function replace(string|array $search, string|array $data, bool $writeToFile = false): string;

    /**
     * check the given path file is exist
     *
     * @return boolean
     */
    function isExist(): bool;
}
