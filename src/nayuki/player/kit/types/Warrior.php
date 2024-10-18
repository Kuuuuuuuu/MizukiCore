<?php

namespace MizukiCore\nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Warrior extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::IRON_HELMET()->setUnbreakable(false),
			VanillaItems::IRON_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::IRON_LEGGINGS()->setUnbreakable(false),
			VanillaItems::IRON_BOOTS()->setUnbreakable(false)
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::DIAMOND_SWORD()->setUnbreakable(false)
		];
	}

	public function setEffect(Player $player) : void{

	}
}