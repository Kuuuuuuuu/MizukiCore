<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\item\Item;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Priest extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::GOLDEN_HELMET()->setUnbreakable(false),
			VanillaItems::GOLDEN_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::GOLDEN_LEGGINGS()->setUnbreakable(false),
			VanillaItems::GOLDEN_BOOTS()->setUnbreakable(false)
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(false),
			VanillaItems::POTION()->setType(PotionType::HEALING)->setCount(8)
		];
	}

	public function setEffect(Player $player) : void{

	}

	public function handleBlockSkill(Player $player, array $args) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, array $args) : void{
		// TODO: Implement handleItemSkill() method.
	}
}