<?php
class VolatileTest extends PHPUnit_Framework_TestCase {

	public function testVolatileObjects() {
		$volatile = new Volatile();

		# expect no exception
		$volatile->member = new Threaded();
		$volatile->member = new Threaded();
	}

	public function testVolatileArrays() {
		$threaded = new Threaded();
		$threaded->test = [
			"hello" => ["world"]];

		$this->assertEquals($threaded["test"] instanceof Volatile, true);
		$this->assertEquals($threaded["test"]["hello"] instanceof Volatile, true);
	}
}
