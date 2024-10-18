<?php

declare(strict_types=1);

namespace nayuki\commands;

use nayuki\entities\Hologram;
use nayuki\Main;
use nayuki\Utils;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function in_array;

final class HologramCommand extends Command{
	public function __construct(){
		parent::__construct(
			'hologram',
			'Hologram Command',
			'/hologram help'
		);
		$this->setPermission('hologram.command');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(Main::PREFIX . TextFormat::RED . 'You can only use this command in-Game!');
			return;
		}

		if(!$this->testPermission($sender)){
			return;
		}

		if(isset($args[0])){
			switch(strtolower($args[0])){
				case 'spawn':
					if(!isset($args[1])){
						$sender->sendMessage(Main::PREFIX . TextFormat::RED . 'Usage: /hologram spawn <type>');
						return;
					}
					if(in_array($args[1], ['kills', 'deaths'], true)){
						$this->spawn($sender, $args[1]);
						return;
					}
					$sender->sendMessage(Main::PREFIX . TextFormat::RED . 'Usage: /hologram spawn <kills|deaths>');
					break;
				case 'remove-all':
					foreach($sender->getWorld()->getEntities() as $entity){
						if($entity instanceof Hologram){
							$entity->flagForDespawn();
						}
					}
					break;
				case 'help':
					$sender->sendMessage('/hologram spawn <kills|deaths> | /hologram remove-all');
					break;
				default:
					$sender->sendMessage(Main::PREFIX . TextFormat::RED . "Subcommand '$args[0]' not found! Try '/hologram help' for help.");
					break;
			}
		}
	}

	public function spawn(Player $player, string $type) : void{
		$nbt = Utils::createBaseNBT($player->getPosition(), null, $player->getLocation()->getYaw(), $player->getLocation()->getPitch());
		$nbt->setString('type', $type);
		$entity = new Hologram($player->getLocation(), $nbt);
		$entity->setNameTagAlwaysVisible();
		$entity->spawnToAll();

		$player->sendMessage(Main::PREFIX . TextFormat::GREEN . 'Hologram' . ' created successfully! ID: ' . $entity->getId());
	}
}