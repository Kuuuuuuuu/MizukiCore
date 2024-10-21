<?php

declare(strict_types=1);

namespace nayuki;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;

final class Utils{

	public static function playSound(string $soundName, Player $player) : void{
		$location = $player->getLocation();
		$pk = PlaySoundPacket::create($soundName, $location->x, $location->y, $location->z, 1, 1);
		$player->getNetworkSession()->sendDataPacket($pk, true);
	}

	public static function secondsToTicks(int $seconds) : int{
		return $seconds * 20;
	}

	public static function minutesToTicks(int $minutes) : int{
		return self::secondsToTicks($minutes * 60);
	}

	public static function hoursToTicks(int $hours) : int{
		return self::minutesToTicks($hours * 60);
	}

	public static function randomArenaSpawnCoords() : Vector3{
		$coords = Main::ARENA_SPAWN_COORDS[array_rand(Main::ARENA_SPAWN_COORDS)];
		$coords = explode(':', $coords);
		return new Vector3((int) $coords[0], (int) $coords[1] + 2, (int) $coords[2]);
	}
}