<?php

declare(strict_types=1);

namespace nayuki\entities;

use nayuki\Main;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use function is_array;

final class Hologram extends Entity{
	private int $countdown = 1;
	private ?string $type = null;
	private float $height = 0.1;
	private float $width = 0.1;
	private int $tick = 0;
	private string $subtitle = '';

	public function __construct(Location $location, CompoundTag $nbt){
		parent::__construct($location, $nbt);
		$this->forceMovementUpdate = false;
		$this->gravity = 0;
		$this->setScale(0.1);
		$this->setNameTagAlwaysVisible();
		$this->loadFromNBT($nbt);
		$this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0);
		$this->getNetworkProperties()->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 0);
	}

	private function loadFromNBT(CompoundTag $nbt) : void{
		$this->type = $nbt->getString('type');
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::PLAYER;
	}

	public function onUpdate(int $currentTick) : bool{
		++$this->tick;
		if($this->tick % 20 === 0){
			--$this->countdown;
			$this->setNameTag($this->subtitle . "\n§eUpdate In: §f" . $this->countdown);
			if($this->countdown < 1){
				$this->subtitle = $this->getSubtitleType();
				$this->countdown = 15;
			}
		}
		return parent::onUpdate($currentTick);
	}

	private function getSubtitleType() : string{
		if($this->type === null){
			return '';
		}

		$isKills = ($this->type === 'kills');
		$subtitle = $isKills ? "§	§b§lTop Kills\n" : "§b§lTop Deaths\n";

		$array = [];
		foreach(Main::getInstance()->getSessionManager()->getSessions() as $session){
			$player = $session->getPlayer();
			$array[$player->getName()] = $isKills ? $session->getKills() : $session->getDeaths();
		}

		$files = glob(Main::getInstance()->getDataFolder() . 'players/*.yml');
		if($files === false){
			return 'Error Loading Data';
		}

		foreach($files as $file){
			$parsed = yaml_parse_file($file);

			if(is_array($parsed) && isset($parsed[$this->type])){
				$playerName = $parsed['name'] ?? 'Loading...';

				if(!isset($array[$playerName])){
					$array[$playerName] = $parsed[$this->type];
				}
			}
		}

		arsort($array);
		$topPlayers = array_slice($array, 0, 10, true);

		$pos = 0;
		foreach($topPlayers as $name => $data){
			$prefix = ($pos < 3) ? '§6[' . ($pos + 1) . '] §r§a' : '§7[' . ($pos + 1) . '] §r§a';
			$suffix = ($pos < 3) ? ' §e' : ' §f';
			$subtitle .= $prefix . $name . $suffix . (int) $data . "\n";
			$pos++;
		}

		return $subtitle;
	}

	public function attack(EntityDamageEvent $source) : void{
		$source->cancel();
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		if($this->type === null){
			return $nbt;
		}
		$nbt->setString('type', $this->type);
		return $nbt;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo($this->height, $this->width);
	}

	protected function getInitialDragMultiplier() : float{
		return 0.02;
	}

	protected function getInitialGravity() : float{
		return 0.08;
	}
}