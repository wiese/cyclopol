<?php

namespace Cyclopol;

class ArticleTeaser {
	public string $link;

	public function __construct( string $link ) {
		$this->link = $link;
	}

	public function getLink() {
		return $this->link;
	}
}

