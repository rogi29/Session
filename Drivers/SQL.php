<?php

namespace Ruddy\Session\Drivers;

/**
 * Ruddy Framework Session
 *
 * @author Gil Nimer <gil@ruddy.nl>
 */

class SQL extends Handlers\SQLHandler
{
    /**
     * @var null|string
     */
    private static $name = 'RuddyPHP';

    /**
     * @var null
     */
    private $_key = null;

    /**
     * @var null
     */
    private $_table = null;

    /**
     * @var array
     */
    private $_cookie = array( );

    /**
     * Connect to SQL
     *
     * @param $driver
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     * @param $port
     */
    public function connSQL($driver, $host, $database, $username, $password, $port)
    {
        $this->connect($driver, $host, $database, $username, $password, $port);
    }

    /**
     * Setup session
     *
     * @param $name
     * @param $key
     * @param $table
     * @param $prefix
     */
    public function setup($name, $key, $table, $prefix)
    {
        if($name != null) self::$name = $name;
        $this->_key     = ($key == null) ? substr(md5($name),0,24) : $key;
        $this->_table   = $table;
        $this->prefix   = $prefix;

        ini_set('session.use_trans_sid', 0);
        session_name(self::$name);
        ini_set('session.save_path', $this->_table);
    }

    /**
     * Start Session
     *
     * @return bool
     */
    public function start()
    {
        $id = true;
        if (!$this->session_status()) {
            if (session_start()) {
                $id = (mt_rand(0, 20) === 0) ? session_regenerate_id(true) : true;
            }
        }

        return $id;
    }

    /**
     * Check session status
     *
     * @return bool
     */
    public function session_status()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') ) {
            return (session_status() !== PHP_SESSION_ACTIVE) ? false : true;
        }

        return (session_id() === '') ? false : true;
    }

    /**
     * Set session value
     *
     * @param $name
     * @param $value
     */
    public function set($name, $value, $expression = null)
    {
        if($expression != null)
            eval("return (\$_SESSION[\$name] {$expression}= \$value);");

        return ($_SESSION[$name] = $value);
    }

    /**
     * Delete session element
     *
     * @param $name
     * @return bool
     */
    public function delete($name)
    {
        if(self::is($name))
            return session_unset($_SESSION[$name]);
        return false;
    }

    /**
     * Get session value
     *
     * @param $name
     * @return bool
     */
    public function get($name)
    {
        return (self::is($name)) ? $_SESSION[$name] : null;
    }

    /**
     * Is session key set
     *
     * @param $name
     * @return bool
     */
    public function is($name)
    {
        return isset($_SESSION[$name]);
    }

    /**
     * Read secured session
     *
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        return mcrypt_decrypt(MCRYPT_3DES, $this->_key, parent::read($id), MCRYPT_MODE_ECB);
    }

    /**
     * Secure session writes
     *
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data)
    {
        return parent::write($id, mcrypt_encrypt(MCRYPT_3DES, $this->_key, $data, MCRYPT_MODE_ECB));
    }

    /**
     * Print $_SESSION array
     */
    public function debug()
    {
        $data = htmlentities(print_r($_SESSION, true));
        echo '<pre>' . $data . '</pre>';
    }
} 