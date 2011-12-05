# livestats.sql
#
# If you want to test with SQLite, you have nothing to do. The default
# setup will use the empty DB livestats.sqlite.
#
# If you want to use livestats with another DB like MySQL, execute this 
# script to create the table neede by livestats.
# The 'MyISAM' engine declaration should be removed id you don't use MySQL.

CREATE TABLE `livestats` (
  `session_id` varchar(255) NOT NULL,
  `last_seen` datetime DEFAULT NULL,
  `state` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM;