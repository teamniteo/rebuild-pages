<?php
namespace Niteoweb\RebuildPages;


class Post {

	private $title;
	private $url;

	public function __construct( $title, $url ) {
		$this->title = $title;
		$this->url   = $url;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getUrl() {
		return $this->url;
	}

}