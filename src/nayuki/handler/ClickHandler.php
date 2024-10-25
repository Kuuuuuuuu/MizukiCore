<?php

declare(strict_types=1);

namespace nayuki\handler;

use nayuki\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use WeakMap;

final class ClickHandler{

	/** @var WeakMap<Player, float[]> */
	private WeakMap $clickData;

	public function __construct(private readonly Main $main){
		$this->clickData = new WeakMap();
	}

	public function addClick(Player $player) : void{
		$playerCps = $this->getClicks($player);

		$clickData = $this->clickData[$player] ?? [];
		array_unshift($clickData, microtime(true));
		$this->clickData[$player] = array_slice($clickData, 0, 50);

		if($this->main->getSessionManager()->getSession($player)->isCpsCounterEnabled()){
			$player->sendTip(TextFormat::AQUA . 'CPS: ' . TextFormat::WHITE . $playerCps);
		}
	}

	public function getClicks(Player $player) : int{
		$clickData = $this->clickData[$player] ?? [];
		if(count($clickData) === 0){
			return 0;
		}

		$currentTime = microtime(true);
		return count(array_filter($clickData, static fn($clickTime) => ($currentTime - $clickTime) <= 1.0));
	}

	public function removePlayerClickData(Player $player) : void{
		unset($this->clickData[$player]);
	}
}