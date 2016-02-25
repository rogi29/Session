<?php

namespace Ruddy\FileSystem\Drivers;

/**
 * Ruddy Framework Config
 *
 * @author Nick Vlug <nick@ruddy.nl> & Gil Nimer <gil@ruddy.nl>
 */

class Direct
{
    /**
     * Store file handler
     *
     * @var null
     */
    private $_handle = null;

    /**
     * Store path
     *
     * @var null
     */
    private $_path = null;

    /**
     * Tells whether the filename is a directory
     *
     * @param $dirname
     * @return bool
     */
    public function isDir($dirname)
    {
        return is_dir($dirname);
    }

    /**
     * Tells whether the filename is a regular file
     *
     * @param $filename
     * @return bool
     */
    public function isFile($filename)
    {
        return is_file($filename);
    }

    /**
     * Open File
     *
     * @param $filename
     * @param string $mode
     * @return null|resource
     */
    public function fopen($filename, $mode = 'a+')
    {
        $this->_path = $filename;
        $this->_handle = fopen($filename, $mode);
        flock($this->_handle, LOCK_UN);
        return $this->_handle;
    }

    /**
     * Close file
     *
     * @param bool $handle
     * @return bool
     */
    public function fclose($handle = false)
    {
        $handle = !$handle ? $this->_handle : $handle;
        if (!flock($handle, LOCK_EX)) {
            return false;
        }

        return fclose($handle);
    }

    /**
     * Write to file
     *
     * @param bool $handle
     * @param $string
     * @return bool|int
     */
    public function fwrite($handle = false, $string)
    {
        $handle = !$handle ? $this->_handle : $handle;
        if (!flock($handle, LOCK_EX)) {
            return false;
        }

        $bytes = fwrite($handle, $string);
        flock($handle, LOCK_UN);
        return $bytes;
    }

    /**
     * Makes directory
     *
     * @param $dirname
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function mkdir($dirname, $mode = 0777, $recursive = false)
    {
        return mkdir($dirname, $mode, $recursive);
    }

    /**
     * Removes directory
     *
     * @param $dirname
     * @return bool
     */
    public function rmdir($dirname)
    {
        return rmdir($dirname);
    }

    /**
     * Deletes a file
     *
     * @param bool $filename
     * @return bool
     */
    public function delete($filename = false)
    {
        $filename = !$filename ? $this->_path : $filename;

        return unlink($filename);
    }

    /**
     * Copies file
     *
     * @param bool $source
     * @param $dest
     * @return bool
     */
    public function copy($source = false, $dest)
    {
        $source = !$source ? $this->_path : $source;

        return copy($source, $dest);
    }

    /**
     * Renames a file or directory
     *
     * @param bool $oldname
     * @param $newname
     * @return bool
     */
    public function rename($oldname = false, $newname)
    {
        $oldname = !$oldname ? $this->_path : $oldname;

        return rename($oldname, $newname);
    }

    /**
     * Gets file modification time
     *
     * @param $filename
     * @return int
     */
    public function fileMTime($filename)
    {
        return filemtime($filename);
    }

    /**
     * Reads entire file into a string
     *
     * @param bool $filename
     * @return string
     */
    public function fileGetContents($filename = false)
    {
        $filename = !$filename ? $this->_path : $filename;

        return (string)@file_get_contents($filename);
    }

    /**
     * Write a string to a file
     *
     * @param bool $filename
     * @param $data
     * @return int
     */
    public function filePutContents($filename = false, $data)
    {
        $filename = !$filename ? $this->_path : $filename;

        return file_put_contents($filename, $data);
    }

} 