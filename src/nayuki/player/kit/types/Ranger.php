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

final class Ranger extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(false),
			VanillaItems::LEATHER_TUNIC()->setUnbreakable(false),
			VanillaItems::LEATHER_PANTS()->setUnbreakable(false),
			VanillaItems::LEATHER_BOOTS()->setUnbreakable(false)
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::BOW()->setUnbreakable(false),
			VanillaItems::ARROW()->setCount(64)
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 1, false));
	}
}