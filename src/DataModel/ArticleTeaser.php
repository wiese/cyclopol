<?php

namespace Cyclopol\DataModel;

use DateTimeInterface;

class ArticleTeaser {
	public string $link;

	public function __construct( string $link, DateTimeInterface $date ) {
		$this->link = $link;
		$this->date = $date;
	}

	public function getLink() {
		return $this->link;
	}

	public function getDate(): DateTimeInterface {
		return $this->date;
	}
}
