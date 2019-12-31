<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use DateTimeInterface;

class Article {

	public string $link;
	public ?int $id;
	public array $previousIds;
	public string $title;
	public string $text;
	public DateTimeInterface $date;
	public string $categories;

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

