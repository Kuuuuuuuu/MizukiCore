<?php

declare(strict_types=1);

namespace nayuki;

use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener as PMListener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\NetworkInterfaceRegisterEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\network\query\DedicatedQueryNetworkInterface;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

final readonly class Listener implements PMListener{

	public function __construct(private Main $main){ }


	/**
	 * @priority HIGHEST
	 */
	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$event->setJoinMessage("");

		Server::getInstance()->broadcastMessage(TextFormat::WHITE . "[" . TextFormat::GREEN . "+" . TextFormat::WHITE . "] " . TextFormat::AQUA . $player->getName());

		$this->main->getPlayerHandler()->loadPlayerData($player);
	}

	/**
	 * @priority HIGHEST
	 */
	public function onQuit(PlayerQuitEvent $event) : void{
		$player = $event->getPlayer();
		$event->setQuitMessage("");

		Server::getInstance()->broadcastMessage(TextFormat::WHITE . "[" . TextFormat::RED . "-" . TextFormat::WHITE . "] " . TextFormat::AQUA . $player->getName());

		$this->main->getClickHandler()->removePlayerClickData($player);
		$this->main->getPlayerHandler()->savePlayerData($player);
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerExhaustEvent(PlayerExhaustEvent $event) : void{
		$event->setAmount(0);
	}

	/**
	 * @priority HIGHEST
	 */
	public function onCraftItemEvent(CraftItemEvent $event) : void{
		if(!$event->isCancelled()){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function onWorldLoadEvent(WorldLoadEvent $event) : void{
		$world = $event->getWorld();
		$world->setTime(World::TIME_DAY);
		$world->stopTime();
	}

	/**
	 * @priority LOWEST
	 */
	public function onNetworkInterfaceRegisterEvent(NetworkInterfaceRegisterEvent $event) : void{
		$interface = $event->getInterface();
		if($interface instanceof RakLibInterface){
			$interface->setPacketLimit(PHP_INT_MAX);
		}elseif($interface instanceof DedicatedQueryNetworkInterface){
			$event->cancel();
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event) : void{
		$player = $event->getOrigin()->getPlayer();
		$packet = $event->getPacket();
		if($player instanceof Player){
			if(($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData) || ($packet instanceof LevelSoundEventPacket && ($packet->sound === LevelSoundEvent::ATTACK_NODAMAGE || $packet->sound === LevelSoundEvent::ATTACK_STRONG))){
				$this->main->getClickHandler()->addClick($player);
				$player->broadcastAnimation(new ArmSwingAnimation($player));
			}elseif($packet instanceof AnimatePacket){
				NetworkBroadcastUtils::broadcastPackets($player->getViewers(), [$packet]);
				$event->cancel();
			}
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerDropItemEvent(PlayerDropItemEvent $event) : void{
		$event->cancel();
	}
}
