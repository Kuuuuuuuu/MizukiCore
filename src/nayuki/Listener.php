<?php

declare(strict_types=1);

namespace nayuki;

use nayuki\entities\FishingHook;
use nayuki\player\scoreboard\Scoreboard;
use pocketmine\block\Froglight;
use pocketmine\block\utils\FroglightType;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\entity\Location;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockBurnEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
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

		Server::getInstance()->broadcastMessage(TextFormat::WHITE . "[" . TextFormat::GREEN . "+" . TextFormat::WHITE . "] " . TextFormat::AQUA . $player->getName());
		Scoreboard::spawn($player);
		Utils::playSound('random.levelup', $player);
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
	public function onPlayerDeathEvent(PlayerDeathEvent $event) : void{
		$player = $event->getPlayer();
		$cause = $player->getLastDamageCause();
		$event->setDrops([]);

		if($cause instanceof EntityDamageByEntityEvent){
			$killer = $cause->getDamager();
			if($killer instanceof Player){
				$killerSession = $this->main->getSessionManager()->getSession($killer);
				$killerSession->incrementKills();
				$killerSession->addCoins(10);

				$playerSession = $this->main->getSessionManager()->getSession($player);
				$playerSession->incrementDeaths();

				Scoreboard::inArena($killer);
				$killer->sendMessage(TextFormat::GREEN . "You killed " . TextFormat::AQUA . $player->getName());
			}
		}

		Scoreboard::spawn($player);
	}

	/**
	 * @priority HIGHEST
	 */
	public function onPlayerRespawnEvent(PlayerRespawnEvent $event) : void{
		$player = $event->getPlayer();
		$spawnCoords = $this->main::SPAWN_COORDS;
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

		$player->broadcastAnimation(new ArmSwingAnimation($player));
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
	public function onDamageEvent(EntityDamageByEntityEvent $event) : void{
		$damager = $event->getDamager();
		$entity = $event->getEntity();

		if(!($damager instanceof Player) || !($entity instanceof Player)){
			return;
		}

		if($entity->getHealth() - $event->getFinalDamage() > 0){
			return;
		}

		$event->cancel();

		$deathSession = $this->main->getSessionManager()->getSession($entity);
		$killerSession = $this->main->getSessionManager()->getSession($damager);

		$deathSession->incrementDeaths();
		$killerSession->incrementKills();
		$killerSession->addCoins(10);

		$killerKit = $killerSession->getCurrentKit();
		if($killerKit !== null){
			$killerKit->setEffect($damager);
			$damager->getInventory()->setContents($killerKit->getInventoryItems());
			$damager->getArmorInventory()->setContents($killerKit->getArmorItems());
		}

		$killerStreak = $killerSession->getStreak();
		if($killerStreak % 5 === 0){
			$this->main->getServer()->broadcastMessage(TextFormat::AQUA . $damager->getName() . TextFormat::WHITE . " is on a " . TextFormat::GREEN . $killerStreak . TextFormat::WHITE . " kill streak!");
		}

		$this->main->getServer()->broadcastMessage(TextFormat::GREEN . $damager->getName() . TextFormat::WHITE . " killed " . TextFormat::AQUA . $entity->getName());

		$entity->setHealth(20);
		$entity->teleport(new Vector3($this->main::SPAWN_COORDS['x'], $this->main::SPAWN_COORDS['y'], $this->main::SPAWN_COORDS['z']));
		$entity->getInventory()->clearAll();
		$entity->getArmorInventory()->clearAll();
		$entity->getEffects()->clear();
		$entity->extinguish();
		$entity->setFireTicks(0);

		Scoreboard::spawn($entity);
		Scoreboard::inArena($damager);
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
		$this->main->getServer()->broadcastMessage(TextFormat::GRAY . "{$player->getName()} ≫" . TextFormat::WHITE . " $msg");
		$event->cancel();
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

		if($belowBlock instanceof Froglight && $belowBlock->getFroglightType() === FroglightType::VERDANT){
			$dVector = $player->getDirectionVector();
			$player->setMotion(new Vector3($dVector->x * 2.35, 3.5, $dVector->y * 2.35));
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
