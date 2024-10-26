<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\Constants;
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

	private static string $skillName = TextFormat::RESET . TextFormat::GREEN . 'Invisible Gem' . TextFormat::RESET . TextFormat::WHITE . ' (กดค้างเพื่อใช้งาน)';
	private static int $skillCooldown = 10;

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [
			VanillaItems::AIR(),
			VanillaItems::IRON_CHESTPLATE()->setUnbreakable(),
			VanillaItems::AIR(),
			VanillaItems::CHAINMAIL_BOOTS()->setUnbreakable(),
		];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::IRON_SWORD()->setUnbreakable()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2)),
			VanillaItems::EMERALD()->setCustomName(self::$skillName)->setCount(3),
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
		if($itemOnHand->getTypeId() !== VanillaItems::EMERALD()->getTypeId() || $itemOnHand->getCustomName() !== self::$skillName){
			return;
		}

		$session = Main::getInstance()->getSessionManager()->getSession($player);
		$now = microtime(true);
		$previousCooldown = $session->getCooldown($this);

		if($now - $previousCooldown < self::$skillCooldown){
			$remaining = self::$skillCooldown - round($now - $previousCooldown);
			$player->sendMessage(Constants::PREFIX . TextFormat::RED . "กรุณารอสกิลนี้ใช้งานใหม่อีกครั้งในอีก $remaining วินาที");
			return;
		}

		$session->setCooldown($this, $now);

		$world = $player->getWorld();
		foreach($world->getPlayers() as $players){
			$players->hidePlayer($player);
		}

		Main::getInstance()->getScheduler()->scheduleDelayedTask(new class($player) extends Task{
			public function __construct(private readonly Player $player){ }

			public function onRun() : void{
				foreach($this->player->getWorld()->getPlayers() as $players){
					$players->showPlayer($this->player);
				}
			}
		}, Utils::secondsToTicks(3));

		$itemOnHand->setCount($itemOnHand->getCount() - 1);
		$player->getInventory()->setItem($player->getInventory()->getHeldItemIndex(), $itemOnHand);
	}
}