<?php

	class rebuildPagesMajestic_Post {
		
		private $title;
		private $url;
		private $numOfexternalBacklinks;

		public function __construct($title, $url, $numOfexternalBacklinks) {
			$this->title = $title;
			$this->url = $url;
		}

		public function getTitle() {
			return $this->title;
		}

		public function getUrl() {
			return $this->url;
		}

		public function getNumOfExternalBacklinks() {
			return $this->numOfexternalBacklinks;
		}

	}

?>