<?php

declare(strict_types=1);

namespace nayuki\entities;

use pocketmine\color\Color;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\world\particle\DustParticle;

final class Marker extends Entity{
	private float $height = 0.1;
	private float $width = 0.1;
	private string $text = '';

	public function __construct(Location $location, CompoundTag $nbt){
		parent::__construct($location, $nbt);
		$this->initializeEntity();
		$this->loadFromNBT($nbt);
	}

	private function initializeEntity() : void{
		$this->forceMovementUpdate = false;
		$this->gravity = 0;
		$this->setScale(0.1);
		$this->setNameTagAlwaysVisible();
		$this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0);
		$this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, 0); // Assuming BOUNDING_BOX_WIDTH was intended
	}

	private function loadFromNBT(CompoundTag $nbt) : void{
		$this->text = $nbt->getString('text', '');
		$this->setNameTag($this->text);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::PLAYER;
	}

	public function getText() : string{
		return $this->text;
	}

	public function onUpdate(int $currentTick) : bool{
		if($currentTick % 20 === 0){
			$this->spawnParticles();
		}
		return parent::onUpdate($currentTick);
	}

	private function spawnParticles() : void{
		$location = $this->getLocation();
		$world = $location->getWorld();

		for($i = 0; $i < 360; $i += 10){
			$rad = deg2rad($i);
			$x = $location->x + 1.5 * cos($rad);
			$y = $location->y + 1.5;
			$z = $location->z + 1.5 * sin($rad);

			$world->addParticle(new Vector3($x, $y, $z), new DustParticle(new Color(255, 255, 255))); // White particle
		}
	}

	public function attack(EntityDamageEvent $source) : void{
		$source->cancel();
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		if($this->text !== ''){
			$nbt->setString('text', $this->text);
		}
		return $nbt;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo($this->height, $this->width);
	}

	protected function getInitialDragMultiplier() : float{
		return 0.0;
	}

	protected function getInitialGravity() : float{
		return 0.0;
	}
}
