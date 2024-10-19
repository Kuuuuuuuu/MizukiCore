<?php

declare(strict_types=1);

namespace nayuki;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\Server;

final class Utils{

	public static function playSound(string $soundName, Player $player) : void{
		$location = $player->getLocation();
		$pk = PlaySoundPacket::create($soundName, $location->x, $location->y, $location->z, 1, 1);
		$player->getNetworkSession()->sendDataPacket($pk, true);
	}

	public static function createBaseNBT(Vector3 $pos, ?Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0) : CompoundTag{
		return CompoundTag::create()
			->setTag('Pos', new ListTag([
				new DoubleTag($pos->x),
				new DoubleTag($pos->y),
				new DoubleTag($pos->z),
			]))
			->setTag('Motion', new ListTag([
				new DoubleTag($motion !== null ? $motion->x : 0.0),
				new DoubleTag($motion !== null ? $motion->y : 0.0),
				new DoubleTag($motion !== null ? $motion->z : 0.0),
			]))
			->setTag('Rotation', new ListTag([
				new FloatTag($yaw),
				new FloatTag($pitch),
			]));
	}

	public static function vector3ToString(Vector3 $vector) : string{
		return $vector->x . ':' . $vector->y . ':' . $vector->z;
	}

	public static function sendWorldMessage(string $msg) : void{
		Server::getInstance()->broadcastMessage($msg);
	}

	public static function randomArenaSpawnCoords() : Vector3{
		$coords = Main::ARENA_SPAWN_COORDS[array_rand(Main::ARENA_SPAWN_COORDS)];
		$coords = explode(':', $coords);
		return new Vector3((int) $coords[0], (int) $coords[1] + 1, (int) $coords[2]);
	}
}