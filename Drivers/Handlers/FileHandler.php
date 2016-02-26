<?php

namespace Ruddy\Session\Drivers\Handlers;

/**
 * Ruddy Framework Session
 *
 * @author Gil Nimer <gil@ruddy.nl>
 */

class FileHandler implements \SessionHandlerInterface
{
    /**
     * @var bool|\Ruddy\FileSystem\Drivers\Direct
     */
    private $direct = false;

    /**
     * @var null
     */
    private $savePath = null;

    /**
     * @var null
     */
    protected $prefix = 'sess';

    /**
     * Construct Session FileHandler
     */
    public function __construct()
    {
        $this->direct = new \Ruddy\FileSystem\Drivers\Direct();
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        if (!$this->direct->isDir($this->savePath)) {
            $this->direct->mkdir($this->savePath, 0777);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }

    /**
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        return $this->direct->fileGetContents("{$this->savePath}/{$this->prefix}_{$id}");
    }

    /**
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data)
    {
        return ($this->direct->filePutContents("{$this->savePath}/{$this->prefix}_{$id}", $data) === false) ? false : true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function destroy($id)
    {
        $file = "{$this->savePath}/sess_{$id}";
        if (file_exists($file)) {
            $this->direct->delete($file);
        }

        return true;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        foreach (glob("{$this->savePath}/{$this->prefix}_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
} 