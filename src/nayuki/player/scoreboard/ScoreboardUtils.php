<?php

declare(strict_types=1);

namespace nayuki\player\scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;

final class ScoreboardUtils{
	private const OBJECTIVE_NAME = 'cunny';
	private const DISPLAY_SLOT = 'sidebar';

	public function new(Player $player, string $displayName) : void{
		$this->remove($player);

		$pk = SetDisplayObjectivePacket::create(
			self::DISPLAY_SLOT,
			self::OBJECTIVE_NAME,
			$displayName,
			'cunny',
			0
		);

		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function remove(Player $player) : void{
		$pk = RemoveObjectivePacket::create(self::OBJECTIVE_NAME);
		$player->getNetworkSession()->sendDataPacket($pk);
	}

	public function setLine(Player $player, int $score, string $message) : void{
		$entry = new ScorePacketEntry();
		$entry->objectiveName = self::OBJECTIVE_NAME;
		$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
		$entry->customName = $message;
		$entry->score = $score;
		$entry->scoreboardId = $score;

		$pk = SetScorePacket::create(SetScorePacket::TYPE_CHANGE, [$entry]);
		$player->getNetworkSession()->sendDataPacket($pk);
	}
}