<?php

declare(strict_types=1);

namespace nayuki;

use nayuki\commands\HologramCommand;
use nayuki\entities\Hologram;
use nayuki\handler\ClickHandler;
use nayuki\player\PlayerHandler;
use nayuki\player\session\SessionManager;
use nayuki\tasks\MainTask;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;

final class Main extends PluginBase{
	public const PREFIX = TextFormat::DARK_GRAY . "[" . TextFormat::DARK_AQUA . "MizukiCore" . TextFormat::DARK_GRAY . "] " . TextFormat::RESET;
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
		$this->getServer()->getCommandMap()->register("hologram", new HologramCommand());

		new MainTask(1);

		EntityFactory::getInstance()->register(Hologram::class, function(World $world, CompoundTag $nbt) : Hologram{
			return new Hologram(EntityDataHelper::parseLocation($nbt, $world), $nbt);
		}, ['Hologram']);
	}

	public function onDisable() : void{
		$this->getLogger()->info(TextFormat::DARK_RED . "disabled!");
	}
}
