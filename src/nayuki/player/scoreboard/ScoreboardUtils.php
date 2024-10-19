<?php

declare(strict_types=1);

namespace nayuki\player\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

final class ScoreboardUtils{

	public static function new(Player $player, string $displayName) : void{
		self::remove($player);

		$pk = SetDisplayObjectivePacket::create(
			'sidebar',
			'cunny',
			$displayName,
			'cunny',
			0
		);

		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public static function remove(Player $player) : void{
		$pk = RemoveObjectivePacket::create('cunny');
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public static function setLine(Player $player, int $score, string $message) : void{
		$entry = new ScorePacketEntry();
		$entry->objectiveName = 'cunny';
		$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;

		$pk = SetScorePacket::create(SetScorePacket::TYPE_CHANGE, [$entry]);
		$player->getNetworkSession()->sendDataPacket($pk);
	}
}