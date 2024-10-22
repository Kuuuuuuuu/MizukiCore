<?php

declare(strict_types=1);

namespace nayuki\forms;

final class ModalForm extends BaseForm{
	/**
	 * @param callable|null $handler Callback function for handling the form response
	 * @param string        $title Form title
	 * @param string        $content Form content
	 * @param string        $button1 Text for first button
	 * @param string        $button2 Text for second button
	 */
	public function __construct(
		?callable $handler = null,
		string $title = '',
		private readonly string $content = '',
		string $button1 = '',
		string $button2 = ''
	){
		parent::__construct($handler);

		$this->data = [
			'type' => 'modal',
			'title' => $title,
			'content' => $this->content,
			'button1' => $button1,
			'button2' => $button2
		];
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

	public function getButton1() : string{
		// @phpstan-ignore-next-line
		return $this->data['button1'];
	}

	public function setButton1(string $text) : void{
		$this->data['button1'] = $text;
	}

	public function getButton2() : string{
		// @phpstan-ignore-next-line
		return $this->data['button2'];
	}

	public function setButton2(string $text) : void{
		$this->data['button2'] = $text;
	}
}