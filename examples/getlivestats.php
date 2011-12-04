<?php
/**
 * getlivestats.php
 * 
 * This example shows you how to retrieve the current state as
 * a JSON object (which you can easily use in JS after).
 * 
 * This script will output to your browser a string like:
 * {"total":42,"idle":"30","reading":10,"writing":2}
 */
require_once(dirname(__FILE__) . '/../backend/php/State.php');

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');
echo json_encode(State::countStates());
?>