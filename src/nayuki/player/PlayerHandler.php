<?php

namespace nayuki\player;

use nayuki\Main;
use nayuki\tasks\async\AsyncLoadPlayerData;
use nayuki\tasks\async\AsyncSavePlayerData;
use pocketmine\player\Player;

final readonly class PlayerHandler{
	public function __construct(private Main $main){ }

	public function loadPlayerData(Player $player) : void{
		$xuid = $player->getXuid();
		$filePath = $this->main->getDataFolder() . 'player/' . "$xuid.yml";
		$task = new AsyncLoadPlayerData($player, $filePath);

		$this->main->getServer()->getAsyncPool()->submitTask($task);
	}

	public function savePlayerData(Player $player) : void{
		$session = $this->main->getSessionManager()->getSession($player);
		$xuid = $player->getXuid();
		$filePath = $this->main->getDataFolder() . 'player/' . "$xuid.yml";

		if($session->isInitialized()){
			$task = new AsyncSavePlayerData($player, $session, $filePath);
			$this->main->getServer()->getAsyncPool()->submitTask($task);
		}
	}
}