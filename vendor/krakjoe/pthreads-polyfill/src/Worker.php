<?php
if (!extension_loaded("pthreads")) {

	class Worker extends Thread {

		public function collect(Closure $collector) {
			foreach ($this->gc as $idx => $collectable) {
				if ($collector($collectable)) {
					unset($this->gc[$idx]);
				}
			}

			return count($this->gc);
		}
		public function shutdown() { return $this->join(); }
		public function isShutdown() { return $this->isJoined(); }
		public function unstack() { return array_shift($this->stack); }
		public function stack(Collectable $collectable) {
			$this->stack[] = $collectable;
			if ($this->isStarted()) {
				$this->runCollectable(count($this->stack)-1, $collectable);
			}
		}

		public function run() {
			foreach ($this->stack as $idx => $collectable) {
				$this
					->runCollectable($idx, $collectable);
			}
		}

		private function runCollectable($idx, Collectable $collectable) {
			$collectable->worker = $this;
			$collectable->state |= THREAD::RUNNING;
			$collectable->run();
			$collectable->state &= ~THREAD::RUNNING;
			$this->gc[] = $collectable;
			unset($this->stack[$idx]);
		}

		private $stack = [];
		private $gc = [];
	}
}


