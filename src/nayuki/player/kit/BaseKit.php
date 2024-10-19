<?php

declare(strict_types=1);

namespace nayuki\player\kit;

use pocketmine\item\Item;
use pocketmine\player\Player;

abstract class BaseKit{

	public function __construct(private readonly string $kitName){ }

	public function getName() : string{
		return $this->kitName;
	}

	/**
	 * @return Item[] The list of armor items.
	 */
	abstract public function getArmorItems() : array;

	/**
	 * @return Item[] The list of inventory items.
	 */
	abstract public function getInventoryItems() : array;

	/**
	 * @param Player $player The player to apply effects to.
	 */
	abstract public function setEffect(Player $player) : void;

	/**
	 * @param Player                  $player The player to handle the skill for.
	 * @param array<string|int|float> $args The arguments for the skill.
	 */
	abstract public function handleBlockSkill(Player $player, array $args) : void;

	/**
	 * @param Player                  $player The player to handle the skill for.
	 * @param array<string|int|float> $args The arguments for the skill.
	 */
	abstract public function handleItemSkill(Player $player, array $args) : void;
}
