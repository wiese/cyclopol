<?php

namespace Cyclopol;

class ArticleTeaser {
	public $link;

	public function __construct( string $link ) {
		$this->link = $link;
	}

	public function getLink() {
		return $this->link;
	}
}

