<?php

declare(strict_types=1);

namespace nayuki\commands;

use nayuki\Constants;
use nayuki\entities\Leaderboard;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use function in_array;

final class LeaderboardCommand extends Command{
	public function __construct(){
		parent::__construct(
			'leaderboard',
			'Leaderboard Command',
			'/leaderboard help',
			['lb']
		);
		$this->setPermission('leaderboard.command');
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
					if(!isset($args[1])){
						$sender->sendMessage(Constants::PREFIX . TextFormat::RED . 'Usage: /leaderboard spawn <type>');
						return;
					}
					if(in_array($args[1], ['kills', 'deaths'], true)){
						$this->spawn($sender, $args[1]);
						return;
					}
					$sender->sendMessage(Constants::PREFIX . TextFormat::RED . 'Usage: /leaderboard spawn <kills|deaths>');
					break;
				case 'remove-all':
					foreach($sender->getWorld()->getEntities() as $entity){
						if($entity instanceof Leaderboard){
							$entity->flagForDespawn();
						}
					}
					break;
				case 'help':
					$sender->sendMessage('/leaderboard spawn <kills|deaths> | /leaderboard remove-all');
					break;
				default:
					$sender->sendMessage(Constants::PREFIX . TextFormat::RED . "Subcommand '$args[0]' not found! Try '/leaderboard help' for help.");
					break;
			}
		}
	}

	public function spawn(Player $player, string $type) : void{
		$entity = new Leaderboard($player->getLocation(), CompoundTag::create()->setString('type', $type));
		$entity->setNameTagAlwaysVisible();
		$entity->spawnToAll();

		$player->sendMessage(Constants::PREFIX . TextFormat::GREEN . 'Leaderboard' . ' created successfully! ID: ' . $entity->getId());
	}
}