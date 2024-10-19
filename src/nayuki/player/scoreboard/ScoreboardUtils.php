<?php

declare(strict_types=1);

namespace nayuki\player\scoreboard;

use nayuki\Main;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

final class ScoreboardUtils{

	public static function new(Player $player, string $displayName) : void{
		$session = Main::getInstance()->getSessionManager()->getSession($player);

		if(!$session->currentScoreboard !== null){
			self::remove($player);
		}

		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = 'sidebar';
		$pk->objectiveName = 'dummy';
		$pk->displayName = $displayName;
		$pk->criteriaName = 'dummy';
		$pk->sortOrder = SORT_ASC;

		$session->currentScoreboard = $displayName;

		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public static function remove(Player $player) : void{
		$pk = RemoveObjectivePacket::create('dummy');
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public static function setLine(Player $player, int $score, string $message) : void{
		$entry = new ScorePacketEntry();
		$entry->objectiveName = 'dummy';
		$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;

		$pk = SetScorePacket::create(SetScorePacket::TYPE_CHANGE, [$entry]);
		$player->getNetworkSession()->sendDataPacket($pk);
	}
}