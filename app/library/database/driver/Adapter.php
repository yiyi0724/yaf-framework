<?php

namespace database\driver;

abstract class Adapter {
	protected final function __clone() {}
	abstract public static function getInstance($type, $host, $port, $dbname, $charset, $username, $password);
	abstract public function query($sql, array $params = array());
	abstract public function debug($sql, array $params = array());
}