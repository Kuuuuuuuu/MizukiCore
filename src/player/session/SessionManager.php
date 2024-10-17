<?php

declare(strict_types=1);

namespace MizukiCore\player\session;

use pocketmine\player\Player;
use WeakMap;

final class SessionManager{
	private WeakMap $sessions;

	public function __construct(){
		$this->sessions = new WeakMap();
	}

	public function getSession(Player $player) : Session{
		return $this->sessions[$player] ??= new Session($player);
	}

	public function removeSession(Player $player) : void{
		unset($this->sessions[$player]);
	}

	/**
	 * @return Session[]
	 */
	public function getSessions() : array{
		return iterator_to_array($this->sessions);
	}

	public function getPlayerInSessionByPrefix(string $name) : ?Player{
		$name = strtolower($name);
		$closestPlayer = null;
		$closestDelta = PHP_INT_MAX;
		$nameLength = strlen($name);

		foreach($this->sessions as $player => $session){
			$playerName = strtolower($player->getName());

			if(str_starts_with($playerName, $name)){
				$curDelta = strlen($playerName) - $nameLength;

				if($curDelta < $closestDelta){
					$closestPlayer = $player;
					$closestDelta = $curDelta;

					if($curDelta === 0){
						return $closestPlayer;
					}
				}
			}
		}

		return $closestPlayer;
	}
}