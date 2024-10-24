<?php

declare(strict_types=1);

namespace nayuki\tasks\async;

use Error;
use nayuki\Main;
use nayuki\player\session\Session;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;

final class AsyncSavePlayerData extends AsyncTask{
	private readonly string $path;
	private readonly string $serializedPlayerData;

	public function __construct(Player $player, Session $session, string $path){
		$this->path = $path;
		$this->serializedPlayerData = serialize([
			'kills' => $session->getKills(),
			'deaths' => $session->getDeaths(),
			'killStreak' => $session->getStreak(),
			'coins' => $session->getCoins(),
			'scoreboard' => $session->isScoreboardEnabled(),
			'cps' => $session->isCpsCounterEnabled()
		]);

		Main::getInstance()->getSessionManager()->removeSession($player);
	}

	public function onRun() : void{
		$playerData = unserialize($this->serializedPlayerData);
		if(!is_array($playerData)){
			return;
		}

		$existingData = [];
		if(file_exists($this->path)){
			$parsed = yaml_parse_file($this->path);
			if(is_array($parsed)){
				$existingData = $parsed;
			}
		}

		if(!yaml_emit_file($this->path, array_merge($existingData, $playerData))){
			$this->setResult(new Error("Failed to save player data for $this->path"));
		}
	}

	public function onCompletion() : void{
		if($this->getResult() instanceof Error){
			Main::getInstance()->getLogger()->error($this->getResult()->getMessage());
		}
	}
}