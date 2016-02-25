<?php

namespace Ruddy\Session\Drivers;

/**
 * Ruddy Framework Session
 *
 * @author Gil Nimer <gil@ruddy.nl>
 */

class File extends Handlers\FileHandler
{
    const time = 2;

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
    private $_path = null;

    /**
     * @var array
     */
    private $_cookie = array( );

    /**
     * Setup session
     */
    public function setup($name = null, $key = null, $path = null, $prefix = 'sess')
    {
        if($name != null) self::$name = $name;
        $this->_key     = ($key == null) ? substr(md5($name),0,24) : $key;
        $this->_path = $path;
        $this->prefix = $prefix;

        $this->_cookie = [
            'lifetime' => 0,
            'path'     => ini_get('session.cookie_path'),
            'domain'   => ini_get('session.cookie_domain'),
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true
        ];

        ini_set('session.use_trans_sid', 0);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);

        session_name(self::$name);

        session_set_cookie_params(
            $this->_cookie['lifetime'], $this->_cookie['path'],
            $this->_cookie['domain'], $this->_cookie['secure'],
            $this->_cookie['httponly']
        );

        if($this->_path != null){
            if (!file_exists($this->_path)) {
                mkdir($this->_path, 0777, true);
            }

            $this->_path = realpath($this->_path);
            ini_set('session.gc_probability', 1);
            ini_set('session.save_path', $this->_path);
        }
    }

    /**
     * Start Session
     *
     * @return bool
     */
    public function start()
    {
        $id = false;
        if (!$this->session_status()) {
            if (session_start()) {
                $id = (mt_rand(0, 20) === 0) ? session_regenerate_id(true) : true;
            }
        }

        return $id;
    }

    /**
     * Destroy session
     *
     * @return bool
     */
    public function destroy($id)
    {
        if (!$this->session_status()) {
            return false;
        }

        $_SESSION = [];
        session_unset();

        setcookie(
            self::$name, '', time() - 42000,
            $this->_cookie['path'], $this->_cookie['domain'],
            $this->_cookie['secure'], $this->_cookie['httponly']
        );

        unlink("{$this->_path}\\{$this->prefix}_{$id}");

        return true;
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