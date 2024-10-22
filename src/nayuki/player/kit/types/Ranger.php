<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\block\Block;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Ranger extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(),
			VanillaItems::CHAINMAIL_CHESTPLATE()->setUnbreakable(),
			VanillaItems::LEATHER_PANTS()->setUnbreakable(),
			VanillaItems::LEATHER_BOOTS()->setUnbreakable()
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::STONE_SWORD()->setUnbreakable()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::KNOCKBACK(), 1)),
			VanillaItems::BOW()->setUnbreakable()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1))->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 2)),
			VanillaItems::ARROW()->setCount(1)
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 1, false));
	}

	public function handleBlockSkill(Player $player, Block $blockAgainst, Item $itemOnHand) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, Item $itemOnHand) : void{
		// TODO: Implement handleItemSkill() method.
	}
}