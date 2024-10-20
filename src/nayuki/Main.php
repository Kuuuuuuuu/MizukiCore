<?php

declare(strict_types=1);

namespace nayuki;

use nayuki\commands\HologramCommand;
use nayuki\commands\MarkerCommand;
use nayuki\commands\NPCCommand;
use nayuki\entities\BomberTNT;
use nayuki\entities\FishingHook;
use nayuki\entities\Hologram;
use nayuki\entities\Marker;
use nayuki\entities\NPC;
use nayuki\handler\ClickHandler;
use nayuki\player\PlayerHandler;
use nayuki\player\session\SessionManager;
use nayuki\tasks\MainTask;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

final class Main extends PluginBase{
	public const PREFIX = TextFormat::DARK_GRAY . "[" . TextFormat::AQUA . "MizukiCore" . TextFormat::DARK_GRAY . "] " . TextFormat::RESET;
	public const SPAWN_COORDS = [
		'x' => 13,
		'y' => 118,
		'z' => 372,
	];
	public const ARENA_SPAWN_COORDS = [
		"-46:6:-50",
		"20:7:2",
		"16:20:-22",
		"21:19:85",
		"44:37:-69",
		"82:-16:1",
		"-91:48:100",
		"-95:7:-33",
		"-117:17:-108",
		"-23:7:30",
		"-55:6:9"
	];

	private static Main $instance;
	private SessionManager $sessionManager;
	private ClickHandler $clickHandler;
	private PlayerHandler $playerHandler;

	public function getSessionManager() : SessionManager{
		return $this->sessionManager;
	}

	public function getClickHandler() : ClickHandler{
		return $this->clickHandler;
	}

	public function getPlayerHandler() : PlayerHandler{
		return $this->playerHandler;
	}

	public static function getInstance() : Main{
		return self::$instance;
	}

	public static function getPlayerDataPath() : string{
		return self::getInstance()->getDataFolder() . 'player/';
	}

	public function onLoad() : void{
		self::$instance = $this;
		$this->sessionManager = new SessionManager();
		$this->clickHandler = new ClickHandler($this);
		$this->playerHandler = new PlayerHandler($this);
	}

	public function onEnable() : void{
		@mkdir($this->getDataFolder());
		@mkdir(self::getPlayerDataPath());

		$this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);
		$this->getLogger()->info(TextFormat::DARK_GREEN . "enabled!");

		new MainTask(1);

		$this->loadWorlds();
		$this->registerEntities();
		$this->registerCommands();
	}

	public function onDisable() : void{
		$this->getLogger()->info(TextFormat::DARK_RED . "disabled!");

		foreach($this->getServer()->getOnlinePlayers() as $player){
			$player->kick(TextFormat::RED . "Server is restarting...");
		}

		foreach($this->getServer()->getWorldManager()->getWorlds() as $world){
			foreach($world->getEntities() as $entity){
				if($entity instanceof Hologram || $entity instanceof NPC || $entity instanceof Marker){
					return;
				}
				$entity->close();
			}
		}
	}

	private function loadWorlds() : void{
		$worldManager = $this->getServer()->getWorldManager();
		$worldPaths = glob($this->getServer()->getDataPath() . 'worlds/*', GLOB_ONLYDIR);

		if($worldPaths === false){
			return;
		}

		foreach($worldPaths as $worldPath){
			$worldName = basename($worldPath);
			$worldManager->loadWorld($worldName, true);
		}

		foreach($worldManager->getWorlds() as $world){
			$world->setTime(0);
			$world->stopTime();
			$world->setSpawnLocation(new Vector3(self::SPAWN_COORDS['x'], self::SPAWN_COORDS['y'], self::SPAWN_COORDS['z']));
		}
	}

	private function registerCommands() : void{
		$this->getServer()->getCommandMap()->registerAll("mizuki", [
			new HologramCommand(),
			new NPCCommand($this),
			new MarkerCommand($this)
		]);
	}

	private function registerEntities() : void{
		EntityFactory::getInstance()->register(Hologram::class, function(World $world, CompoundTag $nbt) : Hologram{
			return new Hologram(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['Hologram']);

		EntityFactory::getInstance()->register(NPC::class, function(World $world, CompoundTag $nbt) : NPC{
			return new NPC(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
		}, ['NPC']);

		EntityFactory::getInstance()->register(BomberTNT::class, function(World $world, CompoundTag $nbt) : BomberTNT{
			return new BomberTNT(null, EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['BomberTNT']);

		EntityFactory::getInstance()->register(FishingHook::class, function(World $world, CompoundTag $nbt) : FishingHook{
			return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
		}, ['FishingHook']);

		EntityFactory::getInstance()->register(Marker::class, function(World $world, CompoundTag $nbt) : Marker{
			return new Marker(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['Marker']);
	}
}
