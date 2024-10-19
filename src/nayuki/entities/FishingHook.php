<?php

declare(strict_types=1);

namespace nayuki\entities;

use nayuki\Main;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\Random;

final class FishingHook extends Projectile{

	private const GRAVITY = 0.05;
	private const DRAG = 0.04;
	private const MOTION_MULTIPLIER = 0.35;

	public static function getNetworkTypeId() : string{
		return EntityIds::FISHING_HOOK;
	}

	protected function getInitialDragMultiplier() : float{
		return self::DRAG;
	}

	protected function getInitialGravity() : float{
		return self::GRAVITY;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.25, 0.25);
	}

	public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null){
		parent::__construct($location, $shootingEntity, $nbt);
		$this->setBaseDamage(1.5);

		if($shootingEntity instanceof Player){
			$this->initializePlayer($shootingEntity);
		}else{
			$this->flagForDespawn();
		}
	}

	private function initializePlayer(Player $player) : void{
		$direction = $player->getDirectionVector();
		$this->setMotion($direction->multiply(self::MOTION_MULTIPLIER));
		$this->handleMotion($this->getMotion());

		Main::getInstance()->getPlayerHandler()->setFishing($player, $this);
	}

	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
		$damage = $this->getResultDamage();
		$owner = $this->getOwningEntity();

		if($owner !== null){
			$event = new EntityDamageByChildEntityEvent($owner, $this, $entityHit, EntityDamageEvent::CAUSE_PROJECTILE, $damage);
			if(!$event->isCancelled()){
				$entityHit->attack($event);
				$entityHit->setMotion($owner->getPosition()->subtractVector($entityHit->getPosition())->normalize()->multiply(3));
			}
		}

		$this->isCollided = true;
		$this->flagForDespawn();
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		$hasUpdate = parent::entityBaseTick($tickDiff);
		$player = $this->getOwningEntity();

		if($this->shouldDespawn($player)){
			$this->flagForDespawn();
			$hasUpdate = true;
		}

		return $hasUpdate;
	}

	private function shouldDespawn(?Entity $player) : bool{
		if(!$player instanceof Player){
			return true;
		}

		return !$player->isAlive() ||
			$player->isClosed() ||
			$player->getInventory()->getItemInHand()->getTypeId() !== ItemTypeIds::FISHING_ROD ||
			$player->getWorld()->getFolderName() !== $this->getWorld()->getFolderName()
			|| $player->getPosition()->distance($this->getPosition()) > 15;
	}

	public function flagForDespawn() : void{
		$owner = $this->getOwningEntity();
		if($owner instanceof Player){
			Main::getInstance()->getPlayerHandler()->setFishing($owner, null);
		}
		parent::flagForDespawn();
	}

	// my brain stop working after I wrote this
	private function handleMotion(Vector3 $direction) : void{
		$hFactor = 3.784;
		$iFactor = 0.1;
		$randomness = 0.037549;

		$random = new Random();

		$randomized = $direction->normalize()->addVector(new Vector3(
			$random->nextSignedFloat() * $randomness,
			$random->nextSignedFloat() * $randomness * $hFactor,
			$random->nextSignedFloat() * $randomness * $hFactor
		));

		$this->motion = $this->motion->multiply(1 - $iFactor)->addVector((new Vector3(
			$randomized->x * $hFactor,
			$randomized->y + 3.114,
			$randomized->z * $hFactor
		))->multiply($iFactor));
	}
}