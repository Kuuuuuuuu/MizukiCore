<?php

declare(strict_types=1);

namespace nayuki\player\kit\items;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class ReaperScythe extends Item{
	public function __construct(){
		parent::__construct(new ItemIdentifier(VanillaItems::NETHERITE_HOE()->getTypeId()), TextFormat::DARK_RED . "Reaper Scythe", ToolTier::NETHERITE());
	}

	public function onAttackEntity(Entity $victim, array &$returnedItems) : bool{
		if($victim instanceof Player){
			$victim->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 3, 1, false));
			$victim->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 3, 1, false));
			$victim->getEffects()->add(new EffectInstance(VanillaEffects::WEAKNESS(), 3, 1, false));
		}

		return parent::onAttackEntity($victim, $returnedItems);
	}
}