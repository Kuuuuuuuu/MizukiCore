<?php

declare(strict_types=1);

namespace nayuki\player\session;

use pocketmine\player\Player;
use WeakMap;

final class SessionManager{

	/** @var WeakMap<Player, Session> */
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
	 * @return WeakMap<Player, Session>
	 */
	public function getSessions() : WeakMap{
		return $this->sessions;
	}
}