<?php
class PoolTestPool extends Pool {
	public function getSize() {
		return $this->size;
	}
}

class PoolTestWorker extends Worker {
	public function __construct($std, Threaded $threaded) {
		$this->std = $std;
		$this->threaded = $threaded;
	}
}

class PoolTestWork extends Threaded implements Collectable {
	public function run() {
		$this->hasWorker = 
			$this->worker instanceof Worker;
		$this->hasWorkerStd =
			$this->worker->std instanceof stdClass;
		$this->hasWorkerThreaded =
			$this->worker->threaded instanceof Threaded;
		$this->setGarbage();
	}

	public function isGarbage() : bool { return true; }
	private function setGarbage() { $this->garbage = true; }
	private $garbage = false;
}

class PoolTestSync extends Threaded implements Collectable {
	public function run() {
		$this->synchronized(function(){
			$this->finished = true;
			$this->notify();
		});
		$this->setGarbage();
	}

	public function isGarbage() : bool { return true; }
	private function setGarbage() { $this->garbage = true; }
	private $garbage = false;
}

class PoolTest extends PHPUnit_Framework_TestCase {

	public function testPool() {
		$pool = new Pool(1, PoolTestWorker::class, [new stdClass, new Threaded]);
		$work = new PoolTestWork();
		$pool->submit($work);
		while (@$i++<2) {
			$pool->submit(new PoolTestWork()); # nothing to assert, no exceptions please
		}
		$pool->submitTo(0, new PoolTestWork()); # nothing to assert, no exceptions please
		$pool->shutdown();

		$this->assertEquals($work->hasWorker, true);
		$this->assertEquals($work->hasWorkerStd, true);
		$this->assertEquals($work->hasWorkerThreaded, true);		
	}
	
	
	public function testPoolGc() {
		$pool = new Pool(1, PoolTestWorker::class, [new stdClass, new Threaded]);
		$work = new PoolTestWork();
		$pool->submit($work);
		while (@$i++<2) {
			$pool->submit(new PoolTestWork()); # nothing to assert, no exceptions please
		}
		$pool->submitTo(0, new PoolTestWork()); # nothing to assert, no exceptions please
		
		/* synchronize with pool */
		$sync = new PoolTestSync();
		$pool->submit($sync);
		$sync->synchronized(function($sync){
			if (!$sync->finished)
				$sync->wait();
		}, $sync);

		$pool->collect(function($task){
			$this->assertEquals($task->isGarbage(), true);
			return true;
		});
		$pool->shutdown();
	}

	public function testPoolResize() {
		$pool = new PoolTestPool(2, PoolTestWorker::class, [new stdClass, new Threaded]);
		$pool->submit(new PoolTestWork());
		$pool->submit(new PoolTestWork());
		$pool->resize(1);
		$this->assertEquals(1, $pool->getSize());
	}
}
