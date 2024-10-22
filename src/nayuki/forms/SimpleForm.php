<?php

declare(strict_types=1);

namespace nayuki\forms;

final class SimpleForm extends BaseForm{
	/** @var array<int|string> */
	private array $labelMap = [];

	/**
	 * @param callable|null $handler Callback function for handling the form response
	 * @param string        $title Form title
	 * @param string        $content Form content
	 */
	public function __construct(
		?callable $handler = null,
		string $title = '',
		private readonly string $content = ''
	){
		parent::__construct($handler);

		$this->data = [
			'type' => 'form',
			'title' => $title,
			'content' => $this->content,
			'buttons' => []
		];
	}

	protected function processData(mixed &$data) : void{
		$data = $this->labelMap[$data] ?? null;
	}

	public function getTitle() : string{
		// @phpstan-ignore-next-line
		return $this->data['title'];
	}

	public function setTitle(string $title) : void{
		$this->data['title'] = $title;
	}

	public function getContent() : string{
		// @phpstan-ignore-next-line
		return $this->data['content'];
	}

	public function setContent(string $content) : void{
		$this->data['content'] = $content;
	}

	/**
	 * @param string      $text Button text
	 * @param int         $imageType Image type (-1: none, 0: path, 1: url)
	 * @param string      $imagePath Path or URL to the image
	 * @param string|null $label Optional label for the button
	 */
	public function addButton(
		string $text,
		int $imageType = -1,
		string $imagePath = '',
		?string $label = null
	) : void{
		$content = ['text' => $text];

		if($imageType !== -1){
			$content['image'] = [
				'type' => $imageType === 0 ? 'path' : 'url',
				'data' => $imagePath
			];
		}

		// @phpstan-ignore-next-line
		$this->data['buttons'][] = $content;
		$this->labelMap[] = $label ?? count($this->labelMap);
	}
}