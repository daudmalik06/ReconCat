<?php
if (!extension_loaded("pthreads")) {

	class Collectable extends Threaded {

		public function isGarbage() { return $this->garbage; }
		public function setGarbage() {
			$this->garbage = true;
		}

		protected $garbage = false;
	}
}
