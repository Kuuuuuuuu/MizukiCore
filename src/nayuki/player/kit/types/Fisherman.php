<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\Block;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Fisherman extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::IRON_HELMET()->setUnbreakable(),
			VanillaItems::IRON_CHESTPLATE()->setUnbreakable(),
			VanillaItems::LEATHER_PANTS()->setUnbreakable(),
			VanillaItems::LEATHER_BOOTS()->setUnbreakable()
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(),
			VanillaItems::FISHING_ROD()->setUnbreakable()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 5)),
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