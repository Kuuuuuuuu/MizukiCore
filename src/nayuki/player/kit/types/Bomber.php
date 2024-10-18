<?php

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Bomber extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::CHAINMAIL_HELMET()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_CHESTPLATE()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2)),
			VanillaItems::CHAINMAIL_LEGGINGS()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_BOOTS()->setUnbreakable(false)
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(false),
			VanillaBlocks::TNT()->asItem()->setCount(16)->setCustomName(TextFormat::RESET . TextFormat::RED . "TNT" . TextFormat::RESET . TextFormat::WHITE . " (กดวางที่พื้นเพื่อใช้งาน)"),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 1, false));
	}
}