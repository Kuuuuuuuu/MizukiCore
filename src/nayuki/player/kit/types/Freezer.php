<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use nayuki\player\kit\items\FreezerSword;
use pocketmine\block\Block;
use pocketmine\color\Color;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Freezer extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable()->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(255))),
			VanillaItems::LEATHER_TUNIC()->setUnbreakable()->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(255))),
			VanillaItems::LEATHER_PANTS()->setUnbreakable()->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(255))),
			VanillaItems::LEATHER_BOOTS()->setUnbreakable()->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(255))),
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			(new FreezerSword())->setUnbreakable()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1)),
		];
	}

	public function setEffect(Player $player) : void{

	}

	public function handleBlockSkill(Player $player, Block $blockAgainst, Item $itemOnHand) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, Item $itemOnHand) : void{
		// TODO: Implement handleItemSkill() method.
	}
}