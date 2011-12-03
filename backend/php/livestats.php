<?php session_start();
// Sanity checks
if (!array_key_exists('sessionId', $_POST)
    || !array_key_exists('state', $_POST))
    die();

require_once(dirname(__FILE__) . '/State.php');
$state = new State($_POST['state'], session_id());
if (!$state->isValid())
    die();

$state->store(dirname(__FILE__) .'/../db/livestats.sqlite');
?>