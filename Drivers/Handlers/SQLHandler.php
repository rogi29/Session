<?php

namespace Ruddy\Session\Drivers\Handlers;

/**
 * Ruddy Framework Session
 *
 * @author Gil Nimer <gil@ruddy.nl>
 */

class SQLHandler implements \SessionHandlerInterface
{
    /**
     * @var bool
     */
    protected $sql = false;

    /**
     * @var null
     */
    private $savePath = null;

    /**
     * @var array
     */
    protected $config = array( );

    /**
     * @var null
     */
    protected $prefix = 'sess';

    /**
     * Construct Session FileHandler
     */
    public function __construct()
    {

    }

    /**
     * Connect Database
     *
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     * @param $secDriver
     * @param null $port
     */
    protected function connect($host, $database, $username, $password, $secDriver, $port = null)
    {
        if($this->sql != false)
            $this->sql->connect($host, $database, $username, $password, $secDriver, $port);
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
    public function open($savePath, $sessionName)
    {
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
        return true;
    }

    /**
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data)
    {
        return true;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function destroy($id)
    {
        return true;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return true;
    }
} 