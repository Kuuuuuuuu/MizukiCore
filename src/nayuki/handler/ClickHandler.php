<?php

declare(strict_types=1);

namespace nayuki\handler;

use nayuki\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use WeakMap;

final class ClickHandler{
	private WeakMap $clickData;

	public function __construct(private readonly Main $main){
		$this->clickData = new WeakMap();
	}

	public function addClick(Player $player) : void{
		$session = $this->main->getSessionManager()->getSession($player);
		$playerCps = $this->getClicks($player);

		if($session->isCpsCounterEnabled()){
			$player->sendTip(TextFormat::BLUE . "CPS: " . TextFormat::WHITE . $playerCps);
		}

		$clickData = $this->clickData[$player] ?? [];
		array_unshift($clickData, microtime(true));
		$clickData = array_slice($clickData, 0, 50);
		$this->clickData[$player] = $clickData;
	}

	public function getClicks(Player $player) : int{
		$clickData = $this->clickData[$player] ?? [];
		if(empty($clickData)){
			return 0;
		}

		$currentTime = microtime(true);
		return count(array_filter($clickData, static fn($clickTime) => ($currentTime - $clickTime) <= 1.0));
	}

	public function removePlayerClickData(Player $player) : void{
		unset($this->clickData[$player]);
	}
}