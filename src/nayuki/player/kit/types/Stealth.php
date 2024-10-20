<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\Main;
use nayuki\player\kit\BaseKit;
use nayuki\Utils;
use pocketmine\block\Block;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

final class Stealth extends BaseKit{

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable(false)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
			VanillaItems::EMERALD()->setCustomName(TextFormat::GREEN . "Invisible Gem" . TextFormat::RESET . TextFormat::WHITE . " (กดค้างเพื่อใช้งาน)"),
		];
	}

	public function setEffect(Player $player) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 999999, 1, false));
		$player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 999999, 2, false));
	}

	public function handleBlockSkill(Player $player, Block $blockAgainst, Item $itemOnHand) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, Item $itemOnHand) : void{
		if(!str_contains($itemOnHand->getName(), "Invisible Gem")){
			return;
		}

		Main::getInstance()->getScheduler()->scheduleDelayedTask(new class($player) extends Task{
			public function __construct(private readonly Player $player){
				foreach($player->getWorld()->getPlayers() as $player){
					$player->hidePlayer($this->player);
				}
			}

			public function onRun() : void{
				foreach($this->player->getWorld()->getPlayers() as $player){
					$player->showPlayer($this->player);
				}
			}
		}, Utils::secondsToTicks(5));

		$itemOnHand->setCount($itemOnHand->getCount() - 1);
	}
}