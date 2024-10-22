<?php

declare(strict_types=1);

namespace nayuki;

use nayuki\entities\FishingHook;
use nayuki\player\kit\types\Lobby;
use nayuki\player\scoreboard\Scoreboard;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Froglight;
use pocketmine\block\utils\FroglightType;
use pocketmine\entity\Location;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener as PMListener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\NetworkInterfaceRegisterEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\network\mcpe\raklib\RakLibInterface;
use pocketmine\network\query\DedicatedQueryNetworkInterface;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

final readonly class Listener implements PMListener{

	public function __construct(private Main $main){
	}


	/**
	 * @priority HIGHEST
	 */
	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$event->setJoinMessage("");

		$this->main->getPlayerHandler()->loadPlayerData($player);
		$this->main->getPlayerHandler()->giveLobbyItems($player);

		$lobby = new Vector3(
			$this->main::LOBBY_COORDS['x'],
			$this->main::LOBBY_COORDS['y'],
			$this->main::LOBBY_COORDS['z']
		);

		$player->setSpawn($lobby);
		$player->teleport($lobby);

		Server::getInstance()->broadcastMessage(TextFormat::WHITE . "[" . TextFormat::GREEN . "+" . TextFormat::WHITE . "] " . TextFormat::AQUA . $player->getName());
		Utils::playSound('random.levelup', $player);
	}

	/**
	 * @priority HIGHEST
	 */
	public function onQuit(PlayerQuitEvent $event) : void{
		$event->setQuitMessage("");

		$player = $event->getPlayer();

		Server::getInstance()->broadcastMessage(TextFormat::WHITE . "[" . TextFormat::RED . "-" . TextFormat::WHITE . "] " . TextFormat::AQUA . $player->getName());

		$this->main->getClickHandler()->removePlayerClickData($player);
		$this->main->getPlayerHandler()->savePlayerData($player);
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
		if(!($player instanceof Player)){
			return;
		}

		if(
			($packet instanceof PlayerAuthInputPacket && $packet->hasFlag(PlayerAuthInputFlags::MISSED_SWING)) ||
			($packet instanceof InventoryTransactionPacket && $packet->trData instanceof UseItemOnEntityTransactionData)
		){
			$this->main->getClickHandler()->addClick($player);
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerDeathEvent(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();
		$cause = $player->getLastDamageCause();

		$event->setDrops([]);
		$event->setXpDropAmount(0);
		$event->setDeathMessage("");

		$this->main->getPlayerHandler()->handlePlayerDeath($player);

		/** @var EntityDamageByEntityEvent $cause */
		if($cause->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
			$killer = $cause->getDamager();
			if(!($killer instanceof Player)){
				return;
			}

			if($killer->getId() === $player->getId()){
				return;
			}

			$killerSession = $this->main->getSessionManager()->getSession($killer);
			$killerSession->incrementKills();
			$killerSession->addCoins(10);

			Scoreboard::inArena($killer);
			$this->main->getServer()->broadcastMessage(
				TextFormat::GREEN . $killer->getName() .
				TextFormat::WHITE . " killed " .
				TextFormat::AQUA . $player->getName()
			);
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerRespawnEvent(PlayerRespawnEvent $event) : void{
		$player = $event->getPlayer();
		$spawnCoords = $this->main::LOBBY_COORDS;
		$player->teleport(new Vector3($spawnCoords['x'], $spawnCoords['y'], $spawnCoords['z']));

		Scoreboard::spawn($player);
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerDropItemEvent(PlayerDropItemEvent $event) : void{
		$player = $event->getPlayer();

		if(!Server::getInstance()->isOp($player->getName()) || !$player->isCreative()){
			$event->cancel();
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerBreakBlockEvent(BlockBreakEvent $event) : void{
		$player = $event->getPlayer();

		if(!Server::getInstance()->isOp($player->getName()) || !$player->isCreative()){
			$event->cancel();
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerPlaceBlockEvent(BlockPlaceEvent $event) : void{
		$player = $event->getPlayer();

		if(!Server::getInstance()->isOp($player->getName()) || !$player->isCreative()){
			$event->cancel();
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerUseItemEvent(PlayerItemUseEvent $event) : void{
		$player = $event->getPlayer();
		$item = $event->getItem();

		$session = $this->main->getSessionManager()->getSession($player);

		if($item->getTypeId() === ItemTypeIds::FISHING_ROD){
			$this->spawnFishingHook($player);
		}else{
			$session->getCurrentKit()?->handleItemSkill($player, $item);
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerInteractEvent(PlayerInteractEvent $event) : void{
		$player = $event->getPlayer();
		$item = $event->getItem();
		$session = $this->main->getSessionManager()->getSession($player);

		if($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
			if($item->getTypeId() === ItemTypeIds::FISHING_ROD){
				$this->spawnFishingHook($player);
				return;
			}
			$session->getCurrentKit()?->handleBlockSkill($player, $event->getBlock(), $item);
		}

		if(!Server::getInstance()->isOp($player->getName()) || !$player->isCreative()){
			$event->cancel();
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function onDamageEvent(EntityDamageEvent $event) : void{
		$cause = $event->getCause();
		if($cause === EntityDamageEvent::CAUSE_FALL){
			$event->cancel();
			return;
		}

		if($cause !== EntityDamageEvent::CAUSE_ENTITY_ATTACK){
			return;
		}

		/** @var EntityDamageByEntityEvent $event */

		$killer = $event->getDamager();
		$entity = $event->getEntity();

		if(!($killer instanceof Player) || !($entity instanceof Player)){
			return;
		}

		$deathSession = $this->main->getSessionManager()->getSession($entity);
		$killerSession = $this->main->getSessionManager()->getSession($killer);

		if($deathSession->getCurrentKit() instanceof Lobby && $killerSession->getCurrentKit() instanceof Lobby){
			$event->cancel();
			return;
		}

		$this->main->getPlayerHandler()->applyKnockBack($entity, $killer);

		if($entity->getHealth() - $event->getFinalDamage() > 0){
			return;
		}

		$this->main->getPlayerHandler()->handlePlayerDeath($entity);

		$killerSession->incrementKills();
		$killerSession->addCoins(mt_rand(10, 20)); // random coins 10 - 20

		$killerKit = $killerSession->getCurrentKit();
		if($killerKit !== null){
			$killerKit->setEffect($killer);
			$killer->getInventory()->setContents($killerKit->getInventoryItems());
			$killer->getArmorInventory()->setContents($killerKit->getArmorItems());
		}

		$killerStreak = $killerSession->getStreak();
		if($killerStreak % 5 === 0){
			$this->main->getServer()->broadcastMessage(
				TextFormat::AQUA . $killer->getName() .
				TextFormat::WHITE . " is on a " .
				TextFormat::GREEN . $killerStreak .
				TextFormat::WHITE . " kill streak!"
			);
		}

		$this->main->getServer()->broadcastMessage(
			TextFormat::GREEN . $killer->getName() .
			TextFormat::WHITE . " killed " .
			TextFormat::AQUA . $entity->getName()
		);

		Scoreboard::inArena($killer);
		Utils::playSound('game.player.hurt', $killer);

		$event->cancel();
	}

	/**
	 * @priority HIGHEST
	 */
	public function onCraft(CraftItemEvent $event) : void{
		$event->cancel();
	}

	/**
	 * @priority HIGHEST
	 */
	public function onLeaveDecay(LeavesDecayEvent $event) : void{
		$event->cancel();
	}

	/**
	 * @priority HIGHEST
	 */
	public function onBlockBurn(BlockBurnEvent $event) : void{
		$event->cancel();
	}

	/**
	 * @priority HIGHEST
	 */
	public function onBlockUpdate(BlockUpdateEvent $event) : void{
		$event->cancel();
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerExhaust(PlayerExhaustEvent $event) : void{
		$event->cancel();
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerChat(PlayerChatEvent $event) : void{
		$player = $event->getPlayer();
		$msg = $event->getMessage();

		$event->cancel();

		$this->main->getServer()->broadcastMessage(TextFormat::GRAY . "{$player->getName()} ≫" . TextFormat::WHITE . " $msg");
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();

		if(!$player->isOnGround()){
			return;
		}

		$positionBelow = $player->getPosition()->floor()->subtract(0, 1, 0);
		$belowBlock = $player->getWorld()->getBlock($positionBelow);

		if($belowBlock->getTypeId() !== BlockTypeIds::FROGLIGHT){
			return;
		}

		/** @var Froglight $belowBlock */
		if($belowBlock->getFroglightType() === FroglightType::VERDANT){
			$dVector = $player->getDirectionVector();
			$player->setMotion(new Vector3($dVector->x * 2.35, 2.5, $dVector->y * 2.35));
		}
	}

	private function spawnFishingHook(Player $player) : void{
		$session = $this->main->getSessionManager()->getSession($player);
		$hook = $session->getFishingHook();
		if($hook === null){
			$location = $player->getLocation();
			$hook = new FishingHook(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->getYaw(), $location->getPitch()), $player);

			$hook->spawnToAll();
		}elseif(!$hook->isFlaggedForDespawn()){
			$hook->flagForDespawn();
		}
	}
}
