<?php

declare(strict_types=1);

namespace MizukiCore\player\kit;

use pocketmine\player\Player;

abstract class BaseKit{
	private string $kitName;

	public function __construct(string $kitName){
		$this->kitName = $kitName;
	}

	public function getName() : string{
		return $this->kitName;
	}

	/**
	 * @return array The list of armor items.
	 */
	abstract public function getArmorItems() : array;

	/**
	 * @return array The list of inventory items.
	 */
	abstract public function getInventoryItems() : array;

	/**
	 * @param Player $player The player to apply effects to.
	 */
	abstract public function setEffect(Player $player) : void;
}
