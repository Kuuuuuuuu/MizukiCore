<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Tank extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::DIAMOND_HELMET()->setUnbreakable(false),
			VanillaItems::DIAMOND_CHESTPLATE()->setUnbreakable(false),
			VanillaItems::DIAMOND_LEGGINGS()->setUnbreakable(false),
			VanillaItems::DIAMOND_BOOTS()->setUnbreakable(false)
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 999999, 2, false));
	}

	public function handleBlockSkill(Player $player, array $args) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, array $args) : void{
		// TODO: Implement handleItemSkill() method.
	}
}