<?php

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
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
			VanillaItems::IRON_HELMET()->setUnbreakable(false),
			VanillaItems::IRON_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::IRON_LEGGINGS()->setUnbreakable(false),
			VanillaItems::IRON_BOOTS()->setUnbreakable(false)
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::DIAMOND_SWORD()->setUnbreakable(false),
			VanillaItems::FISHING_ROD()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 5)),
		];
	}

	public function setEffect(Player $player) : void{

	}
}