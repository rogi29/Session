<?php

namespace Ruddy\Session\Drivers\Handlers;
use Ruddy\DAO\DAO;

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
    private $table = null;

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
        $this->sql = new DAO();
    }

    /**
     * Connect Database
     *
     * @param $driver
     * @param $host
     * @param $database
     * @param $username
     * @param $password
     * @param null $port
     */
    public function connect($driver, $host, $database, $username, $password, $port)
    {
        if($this->sql != false) {
            try {
                $this->sql->connect($driver, $host, $database, $username, $password, $port);
            } catch(\PDOException $e){
                die('Error!:'. $e);
            }
        }
    }

    /**
     * @param string $table
     * @param string $sessionName
     * @return bool
     */
    public function open($table, $sessionName)
    {
        $this->table = $table;

        $query = "
        CREATE TABLE IF NOT EXISTS {$table} (
        id varchar(32) NOT NULL PRIMARY KEY,
        access int(10) unsigned DEFAULT NULL,
        data LONGTEXT
        )
        ";

        $this->sql->prepare($query);
        try {
            $this->sql->execute();
        } catch(\PDOException $e) {
            echo $e->getMessage();
        }
        return true;
    }

    /**
     * @return bool
     */
    public function close()
    {
        $this->sql->disconnect();
        return true;
    }

    /**
     * @param string $id
     * @return string
     */
    public function read($id)
    {
        $this->sql->prepare("SELECT data FROM {$this->table} WHERE id = :id");
        $this->sql->bind(':id', $id);

        if($this->sql->execute()){
            $row = $this->sql->fetch();
            return $row['data'];
        }

        return '';
    }

    /**
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data)
    {
        $access = time();
        $this->sql->prepare("REPLACE INTO {$this->table} (id, access, data) VALUES (:id, :access, :data)");
        $this->sql->bind(':id', $id);
        $this->sql->bind(':access', $access);
        $this->sql->bind(':data', $data);

        if($this->sql->execute()){
            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function destroy($id)
    {
        $this->sql->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $this->sql->bind(':id', $id);

        if($this->sql->execute()){
            return true;
        }

        return false;
    }

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        $oldTime = time() - $maxlifetime;

        $this->sql->prepare("DELETE * FROM {$this->table} WHERE access < :oldTime");
        $this->sql->bind(':oldTime', $oldTime);

        if($this->sql->execute()){
            return true;
        }

        return false;
    }
} 