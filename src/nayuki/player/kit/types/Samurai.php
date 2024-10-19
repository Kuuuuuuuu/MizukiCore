<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\color\Color;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Samurai extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(255))),
			VanillaItems::DIAMOND_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::DIAMOND_BOOTS()->setUnbreakable(false)
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::KNOCKBACK(), 1)),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 1, false));
	}

	public function handleBlockSkill(Player $player, array $args) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, array $args) : void{
		// TODO: Implement handleItemSkill() method.
	}
}