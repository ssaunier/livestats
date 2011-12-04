<?php
/**
 * Livestats PHP Backend library
 * https://github.com/ssaunier/livestats
 *
 * Copyright 2011, Sébastien Saunier
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Date: 12/03/2011
 *
 * Requirement: PDO_SQLITE, included in PHP >= 5.1
 * (@see http://php.net/manual/en/ref.pdo-sqlite.php)
 */
class State {
    
    const IDLE = 0;
    const READING = 1;
    const WRITING = 2;
    
    /**
     * TODO: add a 'counter_id' column so that we can track several counters with the same DB.
     * CREATE TABLE livestats (session_id VARCHAR(255), last_seen DATETIME, state INTEGER);
     */ 
    const TABLE = 'livestats';
    
    private $state;
    private $sessionId;
    
    public function __construct($dirtyState, $sessionId) { 
        $this->state = intval($dirtyState);
        $this->sessionId = $sessionId;
    }
    
    /**
     * Checks whether the current state built at the construction
     * of $this is correct, i.e. is either IDLE, READING or WRITING.
     */ 
    public function isValid() {
        switch ($this->state) {
            case self::IDLE:
            case self::READING:
            case self::WRITING:
                return true;
            default:
                return false;
        }
    }
    
    /**
     * Stores the current State to the DB.
     */ 
    public function store($db_file) {
        if (!is_int($this->state))
            throw new Exception('Found a non valid state in current object. Won\'t store it.');
            
        $handle = new PDO('sqlite:' . $db_file);
        $query = 
            sprintf(
                'DELETE FROM %4$s WHERE session_id = %1$s; '
                . 'INSERT INTO %4$s VALUES (%1$s, %2$s, %3$s)',
                $handle->quote($this->sessionId),
                $handle->quote(date("Y-m-d h:i:s")),
                $this->state,
                self::TABLE);
        $handle->exec($query);
    }

    /**
     * Count the number of IDLE, READING, WRITING visitors in realtime
     * 
     * @param $db_file containing the Sqlite database
     * @param $timeout after which an entry is removed from the DB (relatively to last_seen column)
     * @return an array('total', 'idle', 'reading', 'writing')
     */ 
    public static function countStates($db_file, $timeout = '-30 seconds') {
        $handle = new PDO('sqlite:' . $db_file);
        self::_clearTimeout($handle, $timeout);
        $query = sprintf(
            'SELECT COUNT(*) as c, state FROM %s GROUP BY state', self::TABLE);
        $results = $handle->query($query)->fetchAll(PDO::FETCH_ASSOC);
        
        $idle = 0; $reading = 0; $writing = 0;
        if (!empty($results)) {
            foreach ($results as $entry) {
                switch (intval($entry['state'])) {
                    case self::IDLE:
                        $idle = $entry['c'];
                        break;
                    case self::READING:
                        $reading = $entry['c'];
                        break;
                    case self::WRITING:
                        $writing = $entry['c'];
                        break;
                    default:
                        continue;
                }
            }
        }
                
        return array('total' => $idle + $reading + $writing,
                     'idle' => $idle,
                     'reading' => $reading,
                     'writing' => $writing);
    }
    
    /**
     * Debugging method which prints the content of the livestats table.
     */ 
    public static function printStates($db_file) {
        $handle = new PDO('sqlite:' . $db_file);
        $results = $handle->query('SELECT * FROM ' . self::TABLE)->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            echo 'Nothing to display<br />';
            return;
        }
        foreach ($results as $entry) {
            echo '{ session_id: ' . $entry['session_id'] 
                 . ', last_seen: ' . $entry['last_seen'] 
                 . ', state: ' . $entry['state'] . ' } <br  />';
        }
    }
    
    /**
     * Remove entries from the database for which last_seen
     * date is older than NOW - $timeout.
     * 
     * @param $handle to the DB.
     * @param $timeout written as a string, fed to strtotime 
     * @see http://www.php.net/manual/fr/datetime.formats.relative.php
     */
    private static function _clearTimeout($handle, $timeout) {  
        $timeout_date = date("Y-m-d h:i:s", strtotime($timeout));
        $query = sprintf(
            "DELETE FROM %s WHERE last_seen < %s",
            self::TABLE, $handle->quote($timeout_date));
        $handle->exec($query);
    }
}
?>