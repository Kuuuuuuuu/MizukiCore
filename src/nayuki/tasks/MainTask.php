<?php

declare(strict_types=1);

namespace nayuki\tasks;

use nayuki\Main;
use nayuki\misc\AbstractTask;
use nayuki\Utils;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;

final class MainTask extends AbstractTask{
	private const ENTITY_CLEANUP_INTERVAL = 40;
	private const GOLDEN_APPLE_SPAWN_INTERVAL = 10;

	public function __construct(private readonly Main $main, int $period = 1){ parent::__construct($period); }

	protected function onUpdate(int $tick) : void{
		$this->updateSessions();

		if($tick % Utils::secondsToTicks(self::GOLDEN_APPLE_SPAWN_INTERVAL) === 0){
			$this->spawnGoldenApples();

			if($tick % Utils::secondsToTicks(self::ENTITY_CLEANUP_INTERVAL) === 0){
				$this->cleanupItemEntities();
			}
		}
	}

	private function updateSessions() : void{
		foreach($this->main->getSessionManager()->getSessions() as $session){
			$session->update();
		}
	}

	private function cleanupItemEntities() : void{
		$defaultWorld = $this->main->getServer()->getWorldManager()->getDefaultWorld();

		if($defaultWorld === null){
			return;
		}

		$entities = $defaultWorld->getEntities();
		foreach($entities as $entity){
			if($entity instanceof ItemEntity){
				$entity->flagForDespawn();
			}
		}
	}

	private function spawnGoldenApples() : void{
		$defaultWorld = $this->main->getServer()->getWorldManager()->getDefaultWorld();

		if($defaultWorld === null){
			return;
		}

		foreach($this->main::GOLDEN_APPLE_DROP_COORDS as $coord){
			[$x, $y, $z] = explode(':', $coord);
			$location = new Vector3((int) $x, (int) $y + 2, (int) $z);
			$defaultWorld->dropItem($location, VanillaItems::GOLDEN_APPLE()->setCount(1));
		}
	}
}