<?php
class State {
    const IDLE = 0;
    const READING = 1;
    const WRITING = 2;
    
    public static function getValidState($dirtyState) { 
        $dirtyState = intval($dirtyState);
        switch ($dirtyState) {
            case self::IDLE:
                return self::IDLE;
            case self::READING:
                return self::READING;
            case self::WRITING:
                return self::WRITING;
            default:
                return NULL;
        }
    }
    
    public static function storeState() {
        
    }
}
?>