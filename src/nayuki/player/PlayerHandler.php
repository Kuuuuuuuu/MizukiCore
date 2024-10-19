<?php

declare(strict_types=1);

namespace nayuki\player;

use nayuki\Main;
use nayuki\tasks\async\AsyncLoadPlayerData;
use nayuki\tasks\async\AsyncSavePlayerData;
use pocketmine\entity\Attribute;
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