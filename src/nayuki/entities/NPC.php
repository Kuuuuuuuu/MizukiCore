<?php

declare(strict_types=1);

namespace nayuki\entities;

use nayuki\Main;
use nayuki\player\kit\BaseKit;
use nayuki\player\kit\KitRegistry;
use nayuki\player\scoreboard\Scoreboard;
use nayuki\Utils;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use UnexpectedValueException;

final class NPC extends Human{
	protected float $gravity = 0.0;
	private BaseKit $kit;

	public function __construct(Location $loc, Skin $skin, CompoundTag $nbt){
		parent::__construct($loc, $skin, $nbt);
		$kit = KitRegistry::fromString($nbt->getString('kit'));

		if($kit === false){
			$this->destroy();
			return;
		}

		$this->kit = $kit;
		$this->setCanSaveWithChunk(true);
		$this->setNoClientPredictions();
		$this->getInventory()->setContents($kit->getInventoryItems());
		$this->getArmorInventory()->setContents($kit->getArmorItems());
		$this->setNameTag(TextFormat::AQUA . $kit->getName() . TextFormat::RESET . "\n" . TextFormat::GRAY . "Click to Select");
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		$nbt->setString('kit', $this->kit->getName());
		return $nbt;
	}

	public function attack(EntityDamageEvent $source) : void{
		if(!($source instanceof EntityDamageByEntityEvent)){
			return;
		}

		$damager = $source->getDamager();
		if(!($damager instanceof Player)){
			return;
		}

		$this->interact($damager);
		$source->cancel();
	}

	private function interact(Player $player) : void{
		$session = Main::getInstance()->getSessionManager()->getSession($player);
		$player->getEffects()->clear();
		$player->setGamemode(GameMode::SURVIVAL());

		$this->kit->setEffect($player);
		$player->getInventory()->setContents($this->kit->getInventoryItems());
		$player->getArmorInventory()->setContents($this->kit->getArmorItems());
		$player->teleport(Utils::randomArenaSpawnCoords());
		Scoreboard::inArena($player);

		$session->setCurrentKit($this->kit);
	}

	public function destroy() : void{
		if(!$this->isFlaggedForDespawn()){
			$this->flagForDespawn();
		}
	}

	public function getKit() : BaseKit{
		return $this->kit;
	}

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$skinTag = $nbt->getCompoundTag('Skin');
		if($skinTag === null){
			throw new UnexpectedValueException('Missing skin data');
		}
		$this->setNameTagVisible();
		$this->setNameTagAlwaysVisible();
		$this->setScale(1.0);
		$this->setNoClientPredictions();
	}
}