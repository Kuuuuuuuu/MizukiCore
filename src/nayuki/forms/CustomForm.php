<?php

declare(strict_types=1);

namespace nayuki\forms;

final class CustomForm extends BaseForm{
	/** @var array<int|string> */
	private array $labelMap = [];

	/**
	 * @param callable|null $handler Callback function for handling the form response
	 * @param string        $title Form title
	 */
	public function __construct(
		?callable $handler = null,
		string $title = ''
	){
		parent::__construct($handler);

		$this->data = [
			'type' => 'custom_form',
			'title' => $title,
			'content' => []
		];
	}

	protected function processData(mixed &$data) : void{
		if(is_array($data)){
			$processed = [];
			foreach($data as $index => $value){
				$processed[$this->labelMap[$index]] = $value;
			}
			$data = $processed;
		}
	}

	public function getTitle() : string{
		// @phpstan-ignore-next-line
		return $this->data['title'];
	}

	public function setTitle(string $title) : void{
		$this->data['title'] = $title;
	}

	/**
	 * @param array<string, mixed> $content
	 */
	private function addContent(array $content) : void{
		// @phpstan-ignore-next-line
		$this->data['content'][] = $content;
	}

	public function addLabel(string $text, ?string $label = null) : void{
		$this->addContent([
			'type' => 'label',
			'text' => $text
		]);
		$this->labelMap[] = $label ?? count($this->labelMap);
	}

	public function addToggle(string $text, ?bool $default = null, ?string $label = null) : void{
		$content = [
			'type' => 'toggle',
			'text' => $text
		];

		if($default !== null){
			$content['default'] = $default;
		}

		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
	}

	public function addSlider(
		string $text,
		int $min,
		int $max,
		int $step = -1,
		int $default = -1,
		?string $label = null
	) : void{
		$content = [
			'type' => 'slider',
			'text' => $text,
			'min' => $min,
			'max' => $max
		];

		if($step !== -1){
			$content['step'] = $step;
		}

		if($default !== -1){
			$content['default'] = $default;
		}

		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
	}

	/**
	 * @param array<int|string> $steps
	 */
	public function addStepSlider(
		string $text,
		array $steps,
		int $defaultIndex = -1,
		?string $label = null
	) : void{
		$content = [
			'type' => 'step_slider',
			'text' => $text,
			'steps' => $steps
		];

		if($defaultIndex !== -1){
			$content['default'] = $defaultIndex;
		}

		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
	}

	/**
	 * @param array<int|string> $options
	 */
	public function addDropdown(
		string $text,
		array $options,
		?int $default = null,
		?string $label = null
	) : void{
		$content = [
			'type' => 'dropdown',
			'text' => $text,
			'options' => $options
		];

		if($default !== null){
			$content['default'] = $default;
		}

		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
	}

	public function addInput(
		string $text,
		string $placeholder = '',
		?string $default = null,
		?string $label = null
	) : void{
		$content = [
			'type' => 'input',
			'text' => $text,
			'placeholder' => $placeholder
		];

		if($default !== null){
			$content['default'] = $default;
		}

		$this->addContent($content);
		$this->labelMap[] = $label ?? count($this->labelMap);
	}
}