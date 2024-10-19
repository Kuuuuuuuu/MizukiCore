<?php

declare(strict_types=1);

namespace nayuki\player\scoreboard;

use nayuki\Main;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Scoreboard{
	public static function spawn(Player $player) : void{
		$session = Main::getInstance()->getSessionManager()->getSession($player);
		$lines = [
			"§fKills: §b" . $session->getKills(),
			"§fDeaths: §b" . $session->getDeaths(),
			"§fKDR: §b" . $session->getKdr(),
			"§fCoins: §b" . $session->getCoins(),
		];

		ScoreboardUtils::new($player, TextFormat::AQUA . "Kit" . TextFormat::WHITE . "PvP");

		foreach($lines as $index => $line){
			ScoreboardUtils::setLine($player, $index, $line);
		}
	}

	public static function inArena(Player $player) : void{
		$session = Main::getInstance()->getSessionManager()->getSession($player);
		$lines = [
			"§fKits: §b" . ($session->getCurrentKit()?->getName() ?? "Unknown"),
			"§fStreak: §b" . $session->getStreak(),
		];

		ScoreboardUtils::new($player, TextFormat::AQUA . "Kit" . TextFormat::WHITE . "PvP");

		foreach($lines as $index => $line){
			ScoreboardUtils::setLine($player, $index, $line);
		}
	}
}