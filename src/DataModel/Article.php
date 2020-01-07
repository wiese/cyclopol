<?php
declare( strict_types = 1 );

namespace Cyclopol\DataModel;

use DateTimeInterface;

class Article {

	private string $link;
	private ?int $id;
	private array $previousIds;
	private string $title;
	private string $text;
	private DateTimeInterface $date;
	private string $categories;

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

	public function getLink(): string {
	    return $this->link;
	}

	public function getDistrict(): string {
	    return $this->categories;
	}
}

