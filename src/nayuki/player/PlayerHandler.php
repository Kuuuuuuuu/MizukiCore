<?php

declare(strict_types=1);

namespace nayuki\player;

use nayuki\Constants;
use nayuki\entities\FishingHook;
use nayuki\Main;
use nayuki\player\kit\KitRegistry;
use nayuki\player\scoreboard\Scoreboard;
use nayuki\tasks\async\AsyncLoadPlayerData;
use nayuki\tasks\async\AsyncSavePlayerData;
use nayuki\Utils;
use pocketmine\entity\Attribute;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final readonly class PlayerHandler{
	public function __construct(private Main $main){ }

	public function loadPlayerData(Player $player) : void{
		$xuid = $player->getXuid();
		$filePath = $this->main::getPlayerDataPath() . "$xuid.yml";
		$task = new AsyncLoadPlayerData($player, $filePath);

		$this->main->getServer()->getAsyncPool()->submitTask($task);
	}

	public function savePlayerData(Player $player) : void{
		$session = $this->main->getSessionManager()->getSession($player);
		$xuid = $player->getXuid();
		$filePath = $this->main::getPlayerDataPath() . "$xuid.yml";

		if($session->isInitialized()){
			$task = new AsyncSavePlayerData($player, $session, $filePath);
			$this->main->getServer()->getAsyncPool()->submitTask($task);
		}
	}

	public function setFishing(Player $player, ?FishingHook $hook) : void{
		$session = $this->main->getSessionManager()->getSession($player);
		$session->setFishing($hook);
	}

	public function handlePlayerDeath(Player $player) : void{
		$session = $this->main->getSessionManager()->getSession($player);
		$session->incrementDeaths();
		$session->setCurrentKit(null);

		// Reset player
		$player->setHealth(20);

		$player->teleport(new Vector3(
			Constants::LOBBY_COORDS['x'],
			Constants::LOBBY_COORDS['y'],
			Constants::LOBBY_COORDS['z']
		));

		Scoreboard::spawn($player);
		Utils::playSound('game.player.die', $player);

		$this->giveLobbyItems($player);
		$player->getEffects()->clear();
		$player->extinguish();
	}

	public function giveLobbyItems(Player $player) : void{
		$kit = KitRegistry::fromString('lobby');

		if($kit === false){
			return;
		}

		$sessions = $this->main->getSessionManager()->getSession($player);
		$sessions->setCurrentKit($kit);

		$player->getInventory()->clearAll();
		$player->getArmorInventory()->clearAll();
		$player->getCursorInventory()->clearAll();
		$player->getInventory()->setContents($kit->getInventoryItems());
	}

	public function applyKnockBack(Player $player, Player $damager) : void{
		$xzKB = 0.322;
		$yKb = 0.398;

		$diff = $player->getPosition()->subtractVector($damager->getPosition());

		$x = $diff->getX();
		$z = $diff->getZ();
		$f = sqrt($x * $x + $z * $z);

		if($f <= 0){
			return;
		}

		if(mt_rand() / mt_getrandmax() > $player->getAttributeMap()->get(Attribute::KNOCKBACK_RESISTANCE)?->getValue()){
			$f = 1 / $f;
			$motion = clone $player->getMotion();
			$motion->x = ($motion->x / 2) + ($x * $f * $xzKB);
			$motion->y = ($motion->y / 2) + $yKb;
			$motion->z = ($motion->z / 2) + ($z * $f * $xzKB);
			$motion->y = min($motion->y, $yKb);
			$player->setMotion($motion);
		}
	}
}