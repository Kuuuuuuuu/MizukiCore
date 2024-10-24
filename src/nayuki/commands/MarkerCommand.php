<?php

declare(strict_types=1);

namespace nayuki\commands;

use nayuki\entities\Marker;
use nayuki\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function count;

final class MarkerCommand extends Command{
	public function __construct(private readonly Main $main){
		parent::__construct('marker', 'Marker Command', '/marker help');
		$this->setPermission('marker.command');
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
					if(isset($args[1])){
						$this->spawn($sender, $args[1]);
					}else{
						$sender->sendMessage(Main::PREFIX . TextFormat::RED . 'Usage: /marker spawn <kit>');
					}
					break;
				case 'delete':
					if(isset($args[1]) && is_numeric($args[1])){
						$entity = $this->main->getServer()->getWorldManager()->findEntity((int) $args[1]);
						if($entity instanceof Marker){
							$entity->close();
							$sender->sendMessage(Main::PREFIX . TextFormat::GREEN . 'Marker removed successfully!');
						}else{
							$sender->sendMessage(Main::PREFIX . TextFormat::YELLOW . 'Marker not found!');
						}
					}else{
						$sender->sendMessage(Main::PREFIX . TextFormat::RED . 'Usage: /marker remove <id>');
					}
					break;
				case 'list':
					$entityNames = [];
					foreach($sender->getWorld()->getEntities() as $entity){
						if($entity instanceof Marker){
							$entityNames[] = 'ID: ' . $entity->getId() . ' | Text: ' . $entity->getText();
						}
					}
					$sender->sendMessage(Main::PREFIX . TextFormat::GREEN . 'Markers: ' . count($entityNames) . "\n" . implode("\n", $entityNames));
					break;
				case 'help':
					$sender->sendMessage('/marker spawn <kit> | /marker delete <id> | /marker list');
					break;
				default:
					$sender->sendMessage(Main::PREFIX . TextFormat::RED . "Subcommand '$args[0]' not found! Try '/marker help' for help.");
					break;
			}
		}
	}

	private function spawn(Player $player, string $text) : void{
		$pos = $player->getLocation();
		$entity = new Marker($pos, CompoundTag::create()->setString('text', $text));
		$entity->spawnToAll();

		$player->sendMessage(Main::PREFIX . TextFormat::GREEN . 'Marker spawned successfully! ID: ' . $entity->getId());
	}
}