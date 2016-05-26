<?php

namespace storage;

abstract class Adapter {

	abstract public function get($key, $default = NULL);

	abstract public function del($key);

	abstract public function set($key, $value, $expire = 0);

	protected final function __clone() {
	}
}