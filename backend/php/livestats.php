<?php session_start();
// Sanity checks
if (!array_key_exists('sessionId', $_POST)
    || !array_key_exists('state', $_POST))
    die();

require_once(dirname(__FILE__) . '/State.php');
$state = State::getValidState($_POST['state']);
if ($state === NULL)
    die();

$sessionId = session_id();

// TODO(ssaunier): store data.
echo $sessionId;
?>