<?php

declare(strict_types=1);

namespace nayuki\entities;

use nayuki\entities\Base\Hologram;
use pocketmine\color\Color;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\particle\DustParticle;

final class Marker extends Hologram{
	private string $text = '';

	protected function loadFromNBT(CompoundTag $nbt) : void{
		$text = $nbt->getString('text', '');
		$this->setNameTag($text);
		$this->text = $text;
	}

	public function getText() : string{
		return $this->text;
	}

	public function onUpdate(int $currentTick) : bool{
		if($currentTick % 20 === 0){
			$location = $this->getLocation();
			$world = $location->getWorld();

			for($i = 0; $i < 360; $i += 30){
				$rad = deg2rad($i);
				$x = $location->x + 1.5 * cos($rad);
				$y = $location->y + 1.5;
				$z = $location->z + 1.5 * sin($rad);

				$world->addParticle(new Vector3($x, $y, $z), new DustParticle(new Color(255, 255, 255))); // White particle
			}
		}
		return parent::onUpdate($currentTick);
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		if($this->text !== ''){
			$nbt->setString('text', $this->text);
		}
		return $nbt;
	}
}
