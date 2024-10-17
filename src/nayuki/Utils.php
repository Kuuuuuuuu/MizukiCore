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
}