<?php
// Sanity checks
if (!array_key_exists('sessionId', $_POST)
    || !array_key_exists('state', $_POST))
    die();

require_once(dirname(__FILE__) . '/State.php');
$state = State::getValidState($_POST['state']);
if ($state === NULL)
    die();
    
$sessionId = $_POST['sessionId'];
if (!preg_match('/^\{?[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}\}?$/', $sessionId))
    die();

// TODO(ssaunier): store data.

?>