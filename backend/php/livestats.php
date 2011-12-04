<?php session_start();

define('LIVESTATS_DB', dirname(__FILE__) .'/../db/livestats.sqlite');

if (!array_key_exists('sessionId', $_POST) || !array_key_exists('state', $_POST))
    die();  // Sanity check. Don't go further if the POST request is invalid.

// Create a State object for this request.
require_once(dirname(__FILE__) . '/State.php');
$state = new State($_POST['state'], session_id());
if (!$state->isValid())
    die();

// Store the state into the DB.
$state->store(LIVESTATS_DB);
?>