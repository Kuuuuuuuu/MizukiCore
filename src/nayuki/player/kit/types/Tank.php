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

final class Tank extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::IRON_HELMET()->setUnbreakable(),
			VanillaItems::DIAMOND_CHESTPLATE()->setUnbreakable(),
			VanillaItems::DIAMOND_LEGGINGS()->setUnbreakable(),
			VanillaItems::IRON_BOOTS()->setUnbreakable()
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::DIAMOND_AXE()->setUnbreakable()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 999999, 2, false));
	}

	public function handleBlockSkill(Player $player, Block $blockAgainst, Item $itemOnHand) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, Item $itemOnHand) : void{
		// TODO: Implement handleItemSkill() method.
	}
}