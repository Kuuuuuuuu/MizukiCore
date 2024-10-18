<?php

namespace MizukiCore\nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Pyro extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(0))),
			VanillaItems::CHAINMAIL_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_LEGGINGS()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_BOOTS()->setUnbreakable(false),
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::STONE_SWORD()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FIRE_ASPECT(), 2))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1)),
		];
	}

	public function setEffect(Player $player) : void{

	}
}