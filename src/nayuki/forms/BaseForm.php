<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

declare(strict_types=1);

namespace nayuki\forms;

use pocketmine\form\Form;
use pocketmine\player\Player;
use ReturnTypeWillChange;

abstract class BaseForm implements Form{
	/** @var array<string, mixed> */
	protected array $data = [];

	public function __construct(
		/** @var callable|null */
		private $handler = null
	){
	}

	public function handleResponse(Player $player, mixed $data) : void{
		$this->processData($data);
		if($this->handler !== null){
			($this->handler)($player, $data);
		}
	}

	protected function processData(mixed &$data) : void{
	}

	public function getHandler() : ?callable{
		return $this->handler;
	}

	public function setHandler(?callable $handler) : void{
		$this->handler = $handler;
	}

	/**
	 * @return array<string, mixed>
	 */
	#[ReturnTypeWillChange] public function jsonSerialize() : array{
		return $this->data;
	}
}