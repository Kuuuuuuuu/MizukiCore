<?php

namespace MizukiCore\nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Mage extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::GOLDEN_HELMET()->setUnbreakable(false),
			VanillaItems::GOLDEN_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::GOLDEN_LEGGINGS()->setUnbreakable(false),
			VanillaItems::GOLDEN_BOOTS()->setUnbreakable(false)
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::WOODEN_SWORD()->setUnbreakable(false),
			VanillaItems::SPLASH_POTION()->setType(PotionType::HARMING)->setCount(8)
		];
	}

	public function setEffect(Player $player) : void{

	}
}