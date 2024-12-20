<?php

declare(strict_types=1);

namespace nayuki\commands;

use nayuki\Constants;
use nayuki\entities\NPC;
use nayuki\Main;
use nayuki\player\kit\BaseKit;
use nayuki\player\kit\KitRegistry;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function count;

final class NPCCommand extends Command{
	public function __construct(private readonly Main $main){
		parent::__construct('npc', 'NPC Command', '/npc help');
		$this->setPermission('npc.command');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$sender instanceof Player){
			$sender->sendMessage(Constants::PREFIX . TextFormat::RED . 'You can only use this command in-Game!');
			return;
		}

		if(!$this->testPermission($sender)){
			return;
		}

		if(isset($args[0])){
			switch(strtolower($args[0])){
				case 'spawn':
					if(isset($args[1])){
						$kit = KitRegistry::fromString($args[1]);
						if($kit === false){
							$sender->sendMessage(Constants::PREFIX . TextFormat::RED . 'Kit not found!');
							return;
						}
						$this->spawn($sender, $kit);
					}else{
						$sender->sendMessage(Constants::PREFIX . TextFormat::RED . 'Usage: /npc spawn <kit>');
					}
					break;
				case 'delete':
					if(isset($args[1]) && is_numeric($args[1])){
						$entity = $this->main->getServer()->getWorldManager()->findEntity((int) $args[1]);
						if($entity instanceof NPC){
							$entity->destroy();
							$sender->sendMessage(Constants::PREFIX . TextFormat::GREEN . 'NPC removed successfully!');
						}else{
							$sender->sendMessage(Constants::PREFIX . TextFormat::YELLOW . 'NPC not found!');
						}
					}else{
						$sender->sendMessage(Constants::PREFIX . TextFormat::RED . 'Usage: /npc remove <id>');
					}
					break;
				case 'list':
					$entityNames = [];
					foreach($sender->getWorld()->getEntities() as $entity){
						if($entity instanceof NPC){
							$entityNames[] = 'ID: ' . $entity->getId() . ' | Kit: ' . $entity->getKit()->getName();
						}
					}
					$sender->sendMessage(Constants::PREFIX . TextFormat::GREEN . 'NPCs: ' . count($entityNames) . "\n" . implode("\n", $entityNames));
					break;
				case 'help':
					$sender->sendMessage('/npc spawn <kit> | /npc delete <id> | /npc list');
					break;
				default:
					$sender->sendMessage(Constants::PREFIX . TextFormat::RED . "Subcommand '$args[0]' not found! Try '/npc help' for help.");
					break;
			}
		}
	}

	private function spawn(Player $player, BaseKit $kit) : void{
		$pos = $player->getLocation();
		$yaw = $pos->yaw;
		$pitch = $pos->pitch;

		$nbt = CompoundTag::create()
			->setTag('Pos', new ListTag([
				new DoubleTag($pos->x),
				new DoubleTag($pos->y),
				new DoubleTag($pos->z),
			]))
			->setTag('Motion', new ListTag([
				new DoubleTag(0.0),
				new DoubleTag(0.0),
				new DoubleTag(0.0),
			]))
			->setTag('Rotation', new ListTag([
				new FloatTag($yaw),
				new FloatTag($pitch),
			]))
			->setTag('Skin', CompoundTag::create()->setString('Name', $player->getSkin()->getSkinId())->setByteArray('Data', $player->getSkin()->getSkinData())->setByteArray('CapeData', $player->getSkin()->getCapeData())->setString('GeometryName', $player->getSkin()->getGeometryName())->setByteArray('GeometryData', $player->getSkin()->getGeometryData()))
			->setString('kit', $kit->getName());

		$entity = new NPC($pos, $player->getSkin(), $nbt);
		$entity->spawnToAll();

		$player->sendMessage(Constants::PREFIX . TextFormat::GREEN . 'NPC spawned successfully! ID: ' . $entity->getId());
	}
}