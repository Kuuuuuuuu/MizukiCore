<?php

namespace nayuki\player\kit\items;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Sword;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class FreezerSword extends Sword{
	public function __construct(){
		parent::__construct(new ItemIdentifier(VanillaItems::STONE_SWORD()->getTypeId()), TextFormat::AQUA . "Freezer Sword", ToolTier::STONE());
	}

	public function onAttackEntity(Entity $victim, array &$returnedItems) : bool{
		if($victim instanceof Player){
			$victim->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 5, 1, false));
		}

		return parent::onAttackEntity($victim, $returnedItems);
	}
}