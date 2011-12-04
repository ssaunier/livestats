<?php
require_once(dirname(__FILE__) . '/../backend/php/State.php');
print_r(State::countStates(dirname(__FILE__) . '/../backend/db/livestats.sqlite'));
State::printStates(dirname(__FILE__) . '/../backend/db/livestats.sqlite');
?>