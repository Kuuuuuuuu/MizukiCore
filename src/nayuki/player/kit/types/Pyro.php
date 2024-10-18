<?php

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\color\Color;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Pyro extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(0))),
			VanillaItems::CHAINMAIL_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_LEGGINGS()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_BOOTS()->setUnbreakable(false),
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::STONE_SWORD()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1)),
		];
	}

	public function setEffect(Player $player) : void{

	}
}