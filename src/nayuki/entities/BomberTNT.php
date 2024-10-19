<?php

declare(strict_types=1);

namespace nayuki\entities;

use pocketmine\entity\Entity;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\sound\ExplodeSound;

final class BomberTNT extends PrimedTNT{

	public function explode() : void{
		$explosionSize = 7;
		$location = $this->getLocation();
		$explosionBB = $this->calculateExplosionBoundingBox($location, $explosionSize);

		$nearbyEntities = $this->getWorld()->getNearbyEntities($explosionBB, $this);
		foreach($nearbyEntities as $entity){
			if(!($entity instanceof Player)){
				continue; // only apply to players
			}
			$this->processEntityInExplosion($entity, $location, $explosionSize);
		}

		$this->getWorld()->addParticle($location, new HugeExplodeSeedParticle());
		$this->getWorld()->addSound($location, new ExplodeSound());
	}

	private function calculateExplosionBoundingBox(Vector3 $location, float $explosionSize) : AxisAlignedBB{
		$margin = $explosionSize + 1;
		return new AxisAlignedBB(
			floor($location->x - $margin),
			floor($location->y - $margin),
			floor($location->z - $margin),
			ceil($location->x + $margin),
			ceil($location->y + $margin),
			ceil($location->z + $margin)
		);
	}

	private function processEntityInExplosion(Entity $entity, Vector3 $explosionCenter, float $explosionSize) : void{
		$distance = $entity->getPosition()->distance($explosionCenter) / $explosionSize;

		if($distance <= 1){
			$motion = $entity->getPosition()->subtractVector($explosionCenter)->normalize();
			$impact = 1 - $distance;

			$damage = (int) (((($impact * $impact + $impact) / 2) * 6 * $explosionSize) + 1);

			$minFullDamage = 0.5; // distance
			if($distance > $minFullDamage){
				$damageMultiplier = 1 - (($distance - $minFullDamage) / (1 - $minFullDamage));
				$damage = (int) ($damage * $damageMultiplier);
			}

			$damage = max(1, $damage); // Minimum damage

			$ev = new EntityDamageByEntityEvent($this, $entity, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, $damage);
			$entity->attack($ev);

			$entity->setMotion($entity->getMotion()->addVector($motion->multiply($impact * 0.785)));
		}
	}
}