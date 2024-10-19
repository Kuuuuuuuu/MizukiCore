<?php

declare(strict_types=1);

namespace MizukiCore\nayuki\player\kit\types;

use nayuki\entities\BomberTNT;
use nayuki\player\kit\BaseKit;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class Stealth extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
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
		$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 999999, 2, false));
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 2, false));
	}

	public function handleBlockSkill(Player $player, array $args) : void{
		$itemInHand = (string) $args['itemInHand'];

		if(!str_contains($itemInHand, "Invisible Bomb")){
			return;
		}

		$player->getEffects()->add(new EffectInstance(VanillaEffects::INVISIBILITY(), 8, 1, false));
		$player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));

	}

	public function handleItemSkill(Player $player, array $args) : void{
		// TODO: Implement handleItemSkill() method.
	}
}