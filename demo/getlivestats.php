<?php
require_once(dirname(__FILE__) . '/../backend/php/State.php');

header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');
echo json_encode(State::countStates());
?>