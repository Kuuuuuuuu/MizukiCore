<?php

declare(strict_types=1);

namespace MizukiCore\nayuki\player\kit\types;

use MizukiCore\nayuki\player\kit\items\ReaperScythe;
use nayuki\player\kit\BaseKit;
use nayuki\player\kit\items\FreezerSword;
use pocketmine\color\Color;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Reaper extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(0))),
			VanillaItems::LEATHER_TUNIC()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(0))),
			VanillaItems::LEATHER_PANTS()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(0))),
			VanillaItems::LEATHER_BOOTS()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(0))),
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			(new ReaperScythe())->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 3)),
		];
	}

	public function setEffect(Player $player) : void{

	}

	public function handleBlockSkill(Player $player, array $args) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, array $args) : void{
		// TODO: Implement handleItemSkill() method.
	}
}