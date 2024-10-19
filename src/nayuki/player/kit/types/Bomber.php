<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\entities\BomberTNT;
use nayuki\player\kit\BaseKit;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Bomber extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::CHAINMAIL_HELMET()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_CHESTPLATE()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2)),
			VanillaItems::CHAINMAIL_LEGGINGS()->setUnbreakable(false),
			VanillaItems::CHAINMAIL_BOOTS()->setUnbreakable(false)
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(false),
			VanillaBlocks::TNT()->asItem()->setCount(16)->setCustomName(TextFormat::RESET . TextFormat::RED . "Bomber TNT" . TextFormat::RESET . TextFormat::WHITE . " (กดวางที่พื้นเพื่อใช้งาน)"),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 1, false));
	}

	public function handleBlockSkill(Player $player, array $args) : void{
		$itemInHand = (string) $args['itemInHand'];

		if(!str_contains($itemInHand, "Bomber TNT")){
			return;
		}

		$blockAgainst = explode(":", (string) $args['blockAgainst']);

		$bomb = new BomberTNT($player, Location::fromObject((new Vector3((int) $blockAgainst[0], (int) $blockAgainst[1], (int) $blockAgainst[2]))->add(0.5, 2, 0.5), $player->getWorld(), 0, 0));
		$bomb->spawnToAll();

		$player->getInventory()->setItemInHand($player->getInventory()->getItemInHand()->setCount($player->getInventory()->getItemInHand()->getCount() - 1));
	}

	public function handleItemSkill(Player $player, array $args) : void{
		// TODO: Implement handleItemSkill() method.
	}
}