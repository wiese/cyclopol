<?php
declare( strict_types = 1 );

namespace Cyclopol;

use DateTimeInterface;

class Article {

	public $link;
	public $id;
	public $previousIds;
	public $title;
	public $text;
	public $date;
	public $categories;

	public function __construct(
		string $link,
		?int $id,
		array $previousIds,
		string $title,
		string $text,
		DateTimeInterface $date,
		string $categories
	) {
		$this->link = $link;
		$this->id = $id;
		$this->previousIds = $previousIds;
		$this->title = $title;
		$this->text = $text;
		$this->date = $date;
		$this->categories = $categories;
	}

}

