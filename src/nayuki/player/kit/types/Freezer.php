<?php

namespace nayuki\player\kit\types;

use nayuki\player\kit\BaseKit;
use pocketmine\color\Color;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\Sword;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

final class Freezer extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::LEATHER_CAP()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(255))),
			VanillaItems::LEATHER_TUNIC()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(255))),
			VanillaItems::LEATHER_PANTS()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(0), color::fromRGB(0), color::fromRGB(255))),
			VanillaItems::LEATHER_BOOTS()->setUnbreakable(false)->setCustomColor(Color::mix(color::fromRGB(255), color::fromRGB(255), color::fromRGB(255))),
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			(new class(VanillaItems::STONE_SWORD()) extends Sword{
				public function onAttackEntity(Entity $victim, array &$returnedItems) : bool{
					if($victim instanceof Player){
						$victim->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 5, 1, false));
					}

					return parent::onAttackEntity($victim, $returnedItems);
				}
			})->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 1)),
		];
	}

	public function setEffect(Player $player) : void{

	}
}