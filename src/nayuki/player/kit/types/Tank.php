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

final class Tank extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::DIAMOND_HELMET()->setUnbreakable(false),
			VanillaItems::DIAMOND_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::DIAMOND_LEGGINGS()->setUnbreakable(false),
			VanillaItems::DIAMOND_BOOTS()->setUnbreakable(false)
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 999999, 2, false));
	}
}