<?php

declare(strict_types=1);

namespace nayuki\entities\Base;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

abstract class Hologram extends Entity{
	private const DEFAULT_SIZE = 0.1;

	public function __construct(Location $location, CompoundTag $nbt){
		parent::__construct($location, $nbt);

		$this->forceMovementUpdate = false;
		$this->gravity = 0.0;

		$this->setScale(1.0);
		$this->setNameTagAlwaysVisible();

		// bounding box
		$networkProperties = $this->getNetworkProperties();
		$networkProperties->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, self::DEFAULT_SIZE);
		$networkProperties->setFloat(EntityMetadataProperties::BOUNDING_BOX_WIDTH, self::DEFAULT_SIZE);

		$this->loadFromNBT($nbt);
	}

	/**
	 * Load custom data from NBT
	 */
	abstract protected function loadFromNBT(CompoundTag $nbt) : void;

	public static function getNetworkTypeId() : string{
		return EntityIds::PLAYER;
	}

	public function attack(EntityDamageEvent $source) : void{
		$source->cancel();
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(self::DEFAULT_SIZE, self::DEFAULT_SIZE);
	}

	protected function getInitialDragMultiplier() : float{
		return 0.0;
	}

	protected function getInitialGravity() : float{
		return 0.0;
	}
}