<?php

namespace ksoftm\system\utils\io;


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
     * get all the files in a given path
     *
     * @return array
     */
    function getDirectoryFiles(bool $getSubFoldersFile = false): array;

    /**
     * get all the directories and files in a give path
     *
     * @return array
     */
    function getDirectories(bool $getSubFolders = false): array;

    /**
     * get all the directory path only in a given path
     *
     * @param boolean $getSubFolders
     *
     * @return array
     */
    function getDirectoriesOnly(bool $getSubFolders = false): array;
}
