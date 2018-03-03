<?php
class TestThread extends Thread { # This should be more than one anon class, PHP5 sucks ass!

	public function run() {
		$this->member = "something";
		$this->running = 
			$this->isRunning();
	}

	public $member;
}

class ThreadTest extends PHPUnit_Framework_TestCase {

	public function testThreadStartAndJoin() {
		$thread = new TestThread();
		$this->assertEquals($thread->start(), true);
		$this->assertEquals($thread->isStarted(), true);
		$this->assertEquals($thread->join(), true);
		$this->assertEquals($thread->isJoined(), true);
		$this->assertEquals($thread->member, "something");
	}

	/**
	* @expectedException RuntimeException
	*/
	public function testThreadAlreadyStarted() {
		$thread = new Thread();
		$this->assertEquals($thread->start(), true);
		$this->assertEquals($thread->start(), false);
	}

	/**
	* @expectedException RuntimeException
	*/
	public function testThreadAlreadyJoined() {
		$thread = new Thread();
		$this->assertEquals($thread->start(), true);
		$this->assertEquals($thread->join(), true);
		$this->assertEquals($thread->join(), false);
	}

	public function testThreadIsRunning() {
		$thread = new TestThread();
		$this->assertEquals($thread->start(), true);
		$this->assertEquals($thread->join(), true);
		$this->assertEquals($thread->running, true);
	}

	public function testThreadIds() {
		$thread = new Thread();
		$this->assertInternalType("int", $thread->getThreadId());
		$this->assertInternalType("int", Thread::getCurrentThreadId());	
	}
}
?>
