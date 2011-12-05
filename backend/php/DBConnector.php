<?php
/**
 * Livestats PHP Backend library
 * https://github.com/ssaunier/livestats
 *
 * Copyright 2011, Sébastien Saunier <sebastien.saunier@gmail.com>
 * MIT License
 *
 * Date: 12/05/2011
 * 
 * 
 * This class provides some boilerplate code to help livestats
 * query the DB. It relies on PHP PDO feature, so you need to make
 * sure your server is correctly setup with the driver you need.
 * 
 * More details: http://php.net/manual/pdo.drivers.php
 */
class DBConnector {
    
    /**
     * The instance of the PDO link.
     */
    private $pdo;
    
    /**
     * Configuration given to the constructor.
     */
    private $config;
    
    public function __construct($livestats_db_config)
    {
        $this->config = $livestats_db_config;
        
        // Default setup (SQLite with db file at the default location).
        $dsn = trim($livestats_db_config['dsn']);
        if (empty($dsn) || $dsn == 'sqlite:')
        {
            $dsn = self::_getDefaultDB();
        }
        
        $this->pdo = new PDO(
            $dsn,
            $this->_getConfig('user'),
            $this->_getConfig('password'),
            $this->_getConfig('driver_options'));   
    }
    
    /**
     * Escape the given string to be put in a SQL query.
     */
    public function quote($dirtyString)
    {
        return $this->pdo->quote($dirtyString);
    }
    
    /**
     * Execute the given query.
     */
    public function exec($query)
    {
        return $this->pdo->exec($query);
    }
    
    /**
     * Fetch the results of the given query.
     */
    public function fetch($query)
    {
        return $this->pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    }        
    
    /**
     * Get the default path to the SQLite db file.
     */ 
    private static function _getDefaultDB()
    {
        return sprintf("sqlite:%s/../db/livestats.sqlite", dirname(__FILE__));
    }
    
    /**
     * Reads configuration given key.
     * @returns NULL if the key does not exist in the config.
     */
    private function _getConfig($key)
    {
        if (array_key_exists($key, $this->config))
            return $this->config[$key];
        return NULL;
    }
}
?>