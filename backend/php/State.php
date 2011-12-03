<?php
/*
 * Livestats PHP Backend library
 * https://github.com/ssaunier/livestats
 *
 * Copyright 2011, Sébastien Saunier
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Date: 12/03/2011
 */
class State {
    
    const IDLE = 0;
    const READING = 1;
    const WRITING = 2;
    
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
        $query = 
            sprintf(
                'DELETE FROM %4$s WHERE session_id = \'%1$s\'; '
                . 'INSERT INTO %4$s VALUES (\'%1$s\', \'%2$s\', %3$s)',
                sqlite_escape_string($this->sessionId),
                date("Y-m-d h:i:s"),
                sqlite_escape_string($this->state),
                self::TABLE);
        $handle = sqlite_open($db_file);
        sqlite_exec($handle, $query);
    }

    /**
     * Count the number of IDLE, READING, WRITING visitors in realtime
     * 
     * @param $db_file containing the Sqlite database
     * @param $timeout after which an entry is removed from the DB (relatively to last_seen column)
     * @return an array('total', 'idle', 'reading', 'writing')
     */ 
    public static function countStates($db_file, $timeout = '-15 minutes') {
        $handle = sqlite_open($db_file);
        self::_clearTimeout($handle, $timeout);
        
        $query = sprintf(
            'SELECT COUNT(*) as c, state FROM %s GROUP BY state', self::TABLE);
        $entries = sqlite_fetch_all(sqlite_query($handle, $query), SQLITE_ASSOC);
        
        $idle = 0; $reading = 0; $writing = 0;
        foreach ($entries as $entry) {
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
                
        return array('total' => $idle + $reading + $writing,
                     'idle' => $idle,
                     'reading' => $reading,
                     'writing' => $writing);
    }
    
    /**
     * Debugging method which prints the content of the livestats table.
     */ 
    public static function printStates($db_file) {
        $query = 'SELECT * FROM ' . self::TABLE;
        $dbhandle = sqlite_open($db_file);
        $handle = sqlite_fetch_all(sqlite_query($handle, $query), SQLITE_ASSOC);
        foreach ($entries as $entry) {
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
            "DELETE FROM %s WHERE last_seen < '%s'", self::TABLE, $timeout_date);
        sqlite_exec($handle, $query);
    }
}
?>