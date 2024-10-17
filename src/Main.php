<?php

declare(strict_types=1);

namespace MizukiCore;

use MizukiCore\handler\ClickHandler;
use MizukiCore\player\session\SessionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

final class Main extends PluginBase{

	public const PREFIX = TextFormat::DARK_GRAY . "[" . TextFormat::DARK_AQUA . "MizukiCore" . TextFormat::DARK_GRAY . "] " . TextFormat::RESET;
	private static Main $instance;
	private SessionManager $sessionManager;
	private ClickHandler $clickHandler;

	public static function getInstance() : Main{
		return self::$instance;
	}

	public function getSessionManager() : SessionManager{
		return $this->sessionManager;
	}

	public function getClickHandler() : ClickHandler{
		return $this->clickHandler;
	}

	public function onLoad() : void{
		self::$instance = $this;
		$this->sessionManager = new SessionManager();
		$this->clickHandler = new ClickHandler();
	}

	public function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents(new Listener($this), $this);

		$this->getLogger()->info(TextFormat::DARK_GREEN . "enabled!");
	}

	public function onDisable() : void{
		$this->getLogger()->info(TextFormat::DARK_RED . "disabled!");
	}
}
