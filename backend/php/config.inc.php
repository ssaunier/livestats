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
 * This file holds the configuration for the livestats DB.
 * It uses the PHP abstraction library PDO, you can find more details
 * here: http://php.net/manual/pdo.drivers.php
 * 
 * Example 1 (MySQL):
 *     $livestats_db_config = array(
 *         'dsn' => 'mysql:host=localhost;dbname=livestats',
 *         'user' => 'livestats',
 *         'password' => '',
 *         'driver_options' => array(
 *             PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
 *         )
 *     );
 * 
 * Example 2 (SQLite):
 * 
 *     $livestats_db_config = array(
 *         'dsn' => 'sqlite:'
 *     );
 */

$livestats_db_config = array(
    'dsn' => 'sqlite:'  // By default, it will use the ../db/livestats.sqlite file
);

?>