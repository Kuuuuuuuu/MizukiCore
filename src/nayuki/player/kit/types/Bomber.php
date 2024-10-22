<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\entities\BomberTNT;
use nayuki\player\kit\BaseKit;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Bomber extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::CHAINMAIL_HELMET()->setUnbreakable(),
			VanillaItems::CHAINMAIL_CHESTPLATE()->setUnbreakable()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2)),
			VanillaItems::CHAINMAIL_LEGGINGS()->setUnbreakable(),
			VanillaItems::CHAINMAIL_BOOTS()->setUnbreakable()
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(),
			VanillaBlocks::TNT()->asItem()->setCount(8)->setCustomName(TextFormat::RESET . TextFormat::RED . "Bomber TNT" . TextFormat::RESET . TextFormat::WHITE . " (กดวางที่พื้นเพื่อใช้งาน)"),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 1, false));
	}

	public function handleBlockSkill(Player $player, Block $blockAgainst, Item $itemOnHand) : void{
		if($itemOnHand->getTypeId() !== VanillaBlocks::TNT()->asItem()->getTypeId() || $itemOnHand->getCustomName() !== TextFormat::RESET . TextFormat::RED . "Bomber TNT" . TextFormat::RESET . TextFormat::WHITE . " (กดวางที่พื้นเพื่อใช้งาน)"){
			return;
		}

		$bomb = new BomberTNT($player, Location::fromObject($blockAgainst->getPosition()->add(0.5, 1, 0.5), $player->getWorld()));
		$bomb->spawnToAll();

		$player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
	}

	public function handleItemSkill(Player $player, Item $itemOnHand) : void{
		// TODO: Implement handleItemSkill() method.
	}
}