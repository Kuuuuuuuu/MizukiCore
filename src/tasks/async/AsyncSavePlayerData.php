<?php

declare(strict_types=1);

namespace MizukiCore\tasks\async;

use Exception;
use MizukiCore\Main;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use function yaml_emit_file;

final class AsyncSavePlayerData extends AsyncTask{
	private string $path;
	private array $data;

	public function __construct(Player $player, string $path){
		$this->path = $path;
		$this->data = $this->getPlayerData($player);
	}

	private function getPlayerData(Player $player) : array{
		$session = Main::getInstance()->getSessionManager()->getSession($player);
		$playerData = [
			'kills' => $session->getKills(),
			'deaths' => $session->getDeaths(),
			'killStreak' => $session->getStreak(),
			'coins' => $session->getCoins(),
			'scoreboard' => $session->isScoreboardEnabled(),
			'cps' => $session->isCpsCounterEnabled()
		];
		$playerData['name'] = $player->getName();
		return $playerData;
	}

	public function onRun() : void{
		try{
			yaml_emit_file($this->path, $this->data);
		}catch(Exception $e){
			Main::getInstance()->getLogger()->logException($e);
		}
	}
}