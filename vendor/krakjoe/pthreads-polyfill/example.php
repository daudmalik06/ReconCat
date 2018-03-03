<?php

require_once("vendor/autoload.php");

/*
* This example serves to show that pthreads is not needed for execution to take place.
*
* The tests included with this package serve to verify that behaviour between the polyfill and
* pthreads is consistent.
*/
$pool = new Pool(4);
$pool->submit(new class extends Threaded implements Collectable {
	private $garbage = false;
	
	public function run() {
		echo "Hello World\n";
		$this->garbage = true;
	}
	
	public function isGarbage(): bool {
		return $this->garbage;
	}
});

while ($pool->collect(function($task){
	return $task->isGarbage();
})) continue;

$pool->shutdown();
