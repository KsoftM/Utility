<?php

namespace ksoftm\utils\io;


interface IDirectory
{
    /**
     * check the given path is valid
     *
     * @param boolean $createIfNotExist
     *
     * @return boolean
     */
    function isValidDirectory(bool $createIfNotExist = false): bool;

    /**
     * get the files in a given path
     *
     * @return array
     */
    function getDirectoryFileNames(bool $getSubFoldersFile = false): array;

    /**
     * get the directories in a give path
     *
     * @return array
     */
    function getDirectories(bool $getSubFolders = false): array;

    /**
     * get the directory path only in a given path
     *
     * @param boolean $getSubFolders
     *
     * @return array
     */
    function getDirectoriesOnly(bool $getSubFolders = false): array;
}
