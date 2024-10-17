<?php

declare(strict_types=1);

namespace nayuki\tasks\async;

use nayuki\Main;
use nayuki\player\session\Session;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;

final class AsyncSavePlayerData extends AsyncTask{
	private string $path;
	private string $serializedPlayerData;

	public function __construct(Player $player, Session $session, string $path){
		$this->path = $path;
		$playerData = [
			'kills' => $session->getKills(),
			'deaths' => $session->getDeaths(),
			'killStreak' => $session->getStreak(),
			'coins' => $session->getCoins(),
			'scoreboard' => $session->isScoreboardEnabled(),
			'cps' => $session->isCpsCounterEnabled()
		];
		$this->serializedPlayerData = serialize($playerData);
		Main::getInstance()->getSessionManager()->removeSession($player);
	}

	public function onRun() : void{
		$playerData = unserialize($this->serializedPlayerData);
		$parsed = yaml_parse_file($this->path) ?: [];

		yaml_emit_file($this->path, array_merge($parsed, $playerData));
	}
}