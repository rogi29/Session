<?php

namespace Ruddy\Session;

/**
 * Ruddy Framework Session
 *
 * @author Gil Nimer <gil@ruddy.nl>
 */

class Session
{

    /**
     * @var
     */
    private static $instance;

    /**
     * @var null
     */
    protected $driver = null;

    /**
     * @var null
     */
    protected $strDriver = null;

    /**
     * @var array
     */
    protected $drivers = array('File', 'SQL');

    /**
     * Construct Session
     */
    protected function __construct()
    {
    }

    /**
     * Clone
     */
    private function __clone()
    {

    }

    /**
     * Wake up
     */
    private function __wakeup()
    {

    }

    /**
     * Get Session Instance
     *
     * @return static
     */
    public static function getInstance()
    {
        return isset(static::$instance) ? static::$instance : static::$instance = new static();
    }

    /**
     * Set Driver
     *
     * @param $string
     * @throws \Exception
     */
    public function setDriver($string)
    {
        if(in_array($string, $this->drivers)){
            $this->strDriver = $string;
            $driver = "\\Ruddy\\Session\\Drivers\\{$string}";
            $this->driver = new $driver();
            session_set_save_handler($this->driver, true);
        } else {
            throw new \Exception("Session driver does not exists", "500");
        }
    }

    /**
     * Connect to SQL
     *
     * @param $driver
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     * @param null $port
     * @return bool
     */
    public function connSQL($driver, $host, $database, $username, $password, $port = null)
    {
        if(is_null($this->driver) && $this->strDriver != 'SQL'){
            return false;
        }
        return $this->driver->connect($driver, $host, $database, $username, $password, $port);
    }

    /**
     * Setup Session
     *
     * @param null $name
     * @param null $key
     * @param null $path
     * @return bool
     */
    public function setup($name = null, $key = null, $path = null, $prefix = 'sess')
    {
        if(is_null($this->driver)){
            return false;
        }
        return $this->driver->setup($name, $key, $path, $prefix);
    }

    /**
     * Start Session
     *
     * @return bool
     */
    public function start()
    {
        if(is_null($this->driver)){
            return false;
        }
        return $this->driver->start();
    }

    /**
     * Destroy session
     *
     * @return bool
     */
    public function destroy()
    {
        if(is_null($this->driver)){
            return false;
        }
        return session_destroy();
    }

    /**
     * Set session value
     *
     * @param $name
     * @param $value
     * @return bool
     */
    public function set($name, $value, $expression = null)
    {
        if(is_null($this->driver))
            return false;
        return $this->driver->set($name, $value, $expression = null);
    }

    /**
     * Delete session element
     *
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        if(is_null($this->driver)){
            return false;
        }
        return $this->driver->delete($name);
    }

    /**
     * Get session value
     *
     * @param $name
     * @return bool
     */
    public function get($name)
    {
        if(is_null($this->driver)){
            return false;
        }
        return $this->driver->get($name);
    }

    /**
     * Is session key set
     *
     * @param $name
     * @return bool
     */
    public function is($name)
    {
        if(is_null($this->driver)){
            return false;
        }
        return $this->driver->is($name);
    }

    /**
     * Print $_SESSION array
     *
     * @return bool
     */
    public function debug()
    {
        if(is_null($this->driver)){
            return false;
        }
        return $this->driver->debug();
    }

} 