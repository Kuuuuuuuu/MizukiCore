<?php

declare(strict_types=1);

namespace nayuki\player\session;

use nayuki\player\kit\BaseKit;
use pocketmine\player\Player;

final class Session{
	private int $kills = 0;
	private int $deaths = 0;
	private int $killStreak = 0;
	private int $coins = 0;
	private ?BaseKit $currentKit = null;
	private bool $scoreboard = true;
	private bool $cps = true;
	private bool $initialized = false;

	public function __construct(private readonly Player $player){ }

	public function getPlayer() : Player{
		return $this->player;
	}

	public function loadData(array $data) : void{
		$this->kills = (int) ($data['kills'] ?? 0);
		$this->deaths = (int) ($data['deaths'] ?? 0);
		$this->killStreak = (int) ($data['killStreak'] ?? 0);
		$this->coins = (int) ($data['coins'] ?? 0);
		$this->scoreboard = (bool) ($data['scoreboard'] ?? true);
		$this->cps = (bool) ($data['cps'] ?? true);

		$this->initialized = true;
	}

	public function getStreak() : int{
		return $this->killStreak;
	}

	public function getKills() : int{
		return $this->kills;
	}

	public function getDeaths() : int{
		return $this->deaths;
	}

	public function getKdr() : float{
		return $this->deaths > 0 ? $this->kills / $this->deaths : 1.0;
	}

	public function getCoins() : int{
		return $this->coins;
	}

	public function addCoins(int $amount) : void{
		$this->coins += $amount;
	}

	public function isInitialized() : bool{
		return $this->initialized;
	}

	public function getCurrentKit() : ?BaseKit{
		return $this->currentKit;
	}

	public function setCurrentKit(?BaseKit $kit) : void{
		$this->currentKit = $kit;
	}

	public function isScoreboardEnabled() : bool{
		return $this->scoreboard;
	}

	public function setScoreboardEnabled(bool $value) : void{
		$this->scoreboard = $value;
	}

	public function isCpsCounterEnabled() : bool{
		return $this->cps;
	}

	public function setCpsCounterEnabled(bool $value) : void{
		$this->cps = $value;
	}

	public function incrementKills() : void{
		$this->kills++;
		$this->killStreak++;
	}

	public function incrementDeaths() : void{
		$this->deaths++;
		$this->killStreak = 0;
	}
}