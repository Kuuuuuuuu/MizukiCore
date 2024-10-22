<?php

declare(strict_types=1);

namespace nayuki\tasks\async;

use Exception;
use nayuki\Main;
use nayuki\player\scoreboard\Scoreboard;
use pocketmine\player\Player;
use pocketmine\scheduler\AsyncTask;
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

	public function __construct(Player $player, string $path){
		$this->playerName = $player->getName();
		$this->path = $path;
	}

	public function onRun() : void{
		$playerData = $this->loadFromYaml();
		$this->setResult(['data' => $playerData, 'player' => $this->playerName]);
	}

	/**
	 * @return array<string, string|int|bool>
	 */
	private function loadFromYaml() : array{
		$playerData = self::DEFAULT_DATA;
		$playerData['name'] = $this->playerName;

		try{
			if(file_exists($this->path)){
				$parsed = yaml_parse_file($this->path) ?? [];
				assert(is_array($parsed), 'Expected array, got ' . gettype($parsed));
				$playerData = array_merge($playerData, $parsed);
			}

			yaml_emit_file($this->path, $playerData);
		}catch(Exception){
		}

		return $playerData;
	}

	public function onCompletion() : void{
		$core = Main::getInstance();
		if(!$core->isEnabled()){
			return;
		}

		/** @var array<string, string|int|bool>|null $result */
		$result = $this->getResult();
		if($result === null){
			return;
		}

		$player = $core->getServer()->getPlayerExact((string) $result['player']);
		if(!$player instanceof Player || !$player->isOnline()){
			return;
		}

		$session = $core->getSessionManager()->getSession($player);

		// @phpstan-ignore-next-line
		$session->loadData($result['data']);
		Scoreboard::spawn($player);

		$player->sendMessage(Main::PREFIX . 'Your data has been loaded.');
	}
}