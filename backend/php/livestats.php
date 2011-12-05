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
 * This file purpose is to be called by the ../../livestats.js AJAX calls
 * reporting the visitor's state. It will store this information to the DB.
 */

// Make sure we use the session (session_id being keys in the DB).
session_start();

// Sanity check. Don't go further if the POST request is invalid.
if (!array_key_exists('sessionId', $_POST) || !array_key_exists('state', $_POST))
    die();

// Create a State object for this request.
require_once(dirname(__FILE__) . '/State.php');
$state = new State($_POST['state'], session_id());
if (!$state->isValid())
    die();

// Store the state into the DB.
require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/DBConnector.php');
$db = new DBConnector($livestats_db_config);
$state->store($db);
?>