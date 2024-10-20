<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Mage extends BaseKit{

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
			VanillaItems::WOODEN_SWORD()->setUnbreakable(false),
			VanillaItems::SPLASH_POTION()->setType(PotionType::HARMING)->setCount(8)
		];
	}

	public function setEffect(Player $player) : void{

	}

	public function handleBlockSkill(Player $player, Block $blockAgainst, Item $itemOnHand) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, Item $itemOnHand) : void{
		// TODO: Implement handleItemSkill() method.
	}
}