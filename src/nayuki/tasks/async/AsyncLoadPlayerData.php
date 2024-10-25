<?php

declare(strict_types=1);

namespace nayuki\tasks\async;

use Exception;
use nayuki\Main;
use nayuki\player\scoreboard\Scoreboard;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
use function array_merge;
use function file_exists;
use function microtime;
use function round;
use function yaml_emit_file;
use function yaml_parse_file;

final class AsyncLoadPlayerData extends AsyncTask{
	private const DEFAULT_DATA = [
		'kills' => 0,
		'deaths' => 0,
		'killStreak' => 0,
		'coins' => 0,
		'scoreboard' => true,
		'cps' => true
	];

	private string $playerName;
	private string $path;
	private float $initialTime;

	public function __construct(
		Player $player,
		string $path
	){
		$this->playerName = $player->getName();
		$this->path = $path;
		$this->initialTime = microtime(true);
	}

	public function onRun() : void{
		$this->setResult($this->loadFromYaml());
	}

	/**
	 * @return array{data: array<string, string|int|bool>, time: float}
	 */
	private function loadFromYaml() : array{
		$playerData = ['name' => $this->playerName];

		try{
			if(file_exists($this->path)){
				$parsed = yaml_parse_file($this->path);
				if(is_array($parsed)){
					$playerData = array_merge($playerData, $parsed);
				}
			}

			yaml_emit_file($this->path, $playerData);

			return [
				'data' => $playerData,
				'time' => microtime(true) - $this->initialTime
			];
		}catch(Exception){
			return [
				'data' => self::DEFAULT_DATA,
				'time' => 0,
			];
		}
	}

	public function onCompletion() : void{
		$core = Main::getInstance();
		if(!$core->isEnabled()){
			return;
		}

		$result = $this->getResult();
		if(!is_array($result)){
			return;
		}

		$player = $core->getServer()->getPlayerExact($result['data']['name']);
		if(!$player instanceof Player || !$player->isOnline()){
			return;
		}

		$session = $core->getSessionManager()->getSession($player);
		$session->loadData($result['data']);
		Scoreboard::spawn($player);

		$loadTime = round($result['time'], 3);
		$player->sendMessage(Main::PREFIX . "Your data has been loaded. ({$loadTime}s)");
	}
}