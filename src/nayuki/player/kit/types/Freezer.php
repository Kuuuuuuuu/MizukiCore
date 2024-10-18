<?php

namespace MizukiCore\nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Freezer extends BaseKit{

	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(255))),
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(255))),
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(255))),
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(255))),
		];
	}

	public function getInventoryItems() : array{
		return [
			VanillaItems::STONE_SWORD()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1))->onAttackEntity(function(Entity $target) : void{
				$target->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 3, 1, false));
			}),
		];
	}

	public function setEffect(Player $player) : void{

	}
}