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

final class Berserker extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::IRON_HELMET()->setUnbreakable(false),
			VanillaItems::LEATHER_TUNIC()->setUnbreakable(false),
			VanillaItems::IRON_LEGGINGS()->setUnbreakable(false),
			VanillaItems::LEATHER_BOOTS()->setUnbreakable(false)
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::DIAMOND_AXE()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2)),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 999999, 0, false));
	}
}