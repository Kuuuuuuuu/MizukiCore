<?php

declare(strict_types=1);

namespace nayuki;

use nayuki\commands\LeaderboardCommand;
use nayuki\commands\MarkerCommand;
use nayuki\commands\NPCCommand;
use nayuki\entities\Base\Hologram;
use nayuki\entities\BomberTNT;
use nayuki\entities\FishingHook;
use nayuki\entities\Leaderboard;
use nayuki\entities\Marker;
use nayuki\entities\NPC;
use nayuki\handler\ClickHandler;
use nayuki\handler\PlayerHandler;
use nayuki\player\session\SessionManager;
use nayuki\tasks\MainTask;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class Main extends PluginBase{
	use SingletonTrait;

	private SessionManager $sessionManager;
	private ClickHandler $clickHandler;
	private PlayerHandler $playerHandler;
	private DataConnector $database;

	public function getSessionManager() : SessionManager{
		return $this->sessionManager;
	}

	public function getClickHandler() : ClickHandler{
		return $this->clickHandler;
	}

	public function getPlayerHandler() : PlayerHandler{
		return $this->playerHandler;
	}

	public static function getPlayerDataPath() : string{
		return self::getInstance()->getDataFolder() . 'player/';
	}

	public function onLoad() : void{
		self::setInstance($this);
		$this->sessionManager = new SessionManager();
		$this->clickHandler = new ClickHandler($this);
		$this->playerHandler = new PlayerHandler($this);
	}

	public function onEnable() : void{
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder() . 'db/');
		@mkdir(self::getPlayerDataPath());

		$this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);
		$this->getLogger()->info(TextFormat::DARK_GREEN . 'enabled!');

		new MainTask($this, 1);

		$this->loadWorlds();
		$this->registerEntities();

		$this->getServer()->getCommandMap()->registerAll('mizuki', [
			new LeaderboardCommand(),
			new NPCCommand($this),
			new MarkerCommand($this)
		]);

		$this->database = libasynql::create($this, $this->getConfig()->get('database'), [
			'mysql' => '/db/mysql.sql',
			'sqlite' => '/db/sqlite.sql'
		]);
	}

	public function onDisable() : void{
		$this->getLogger()->info(TextFormat::DARK_RED . 'disabled!');

		if(isset($this->database)){
			$this->database->close();
		}

		foreach($this->getServer()->getOnlinePlayers() as $player){
			$player->kick(TextFormat::RED . 'Server is restarting...');
		}

		foreach($this->getServer()->getWorldManager()->getWorlds() as $world){
			$world->save();

			foreach($world->getEntities() as $entity){
				if($entity instanceof Hologram || $entity instanceof NPC){
					return;
				}
				$entity->flagForDespawn();
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
			$world->setAutoSave(false);
		}
	}

	private function registerEntities() : void{
		EntityFactory::getInstance()->register(Leaderboard::class, function(World $world, CompoundTag $nbt) : Leaderboard{
			return new Leaderboard(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['Leaderboard']);

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
