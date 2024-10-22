<?php

declare(strict_types=1);

namespace nayuki\entities;

use nayuki\entities\Base\Hologram;
use nayuki\Main;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;
use function is_array;

final class Leaderboard extends Hologram{
	private int $countdown = 1;
	private ?string $type = null;
	private string $subtitle = '';

	protected function loadFromNBT(CompoundTag $nbt) : void{
		$this->type = $nbt->getString('type');
	}

	public function onUpdate(int $currentTick) : bool{
		if($currentTick % 20 === 0){
			--$this->countdown;
			$this->setNameTag($this->subtitle . "\n§eUpdate In: §f" . $this->countdown);
			if($this->countdown < 1){
				$this->subtitle = $this->getSubtitleType();
				$this->countdown = 15;
			}
		}
		return parent::onUpdate($currentTick);
	}

	private function getSubtitleType() : string{
		if($this->type === null){
			return '';
		}

		$isKills = $this->type == 'kills';
		$subtitle = TextFormat::AQUA . TextFormat::BOLD . ($isKills ? "Top Kills" : "Top Deaths") . "\n";

		$array = [];
		foreach(Main::getInstance()->getSessionManager()->getSessions() as $session){
			$player = $session->getPlayer();
			$array[$player->getName()] = $isKills ? $session->getKills() : $session->getDeaths();
		}

		$files = glob(Main::getPlayerDataPath() . '*.yml');
		if($files === false){
			return 'Error Loading Data';
		}

		foreach($files as $file){
			$parsed = yaml_parse_file($file);

			if(is_array($parsed) && isset($parsed[$this->type])){
				$playerName = $parsed['name'] ?? 'Loading...';

				if(!isset($array[$playerName])){
					$array[$playerName] = $parsed[$this->type];
				}
			}
		}

		arsort($array);
		$topPlayers = array_slice($array, 0, 10, true);

		$pos = 0;
		foreach($topPlayers as $name => $data){
			$prefix = ($pos < 3) ? '§6[' . ($pos + 1) . '] §r§a' : '§7[' . ($pos + 1) . '] §r§a';
			$suffix = ($pos < 3) ? ' §e' : ' §f';
			$subtitle .= $prefix . $name . $suffix . (int) $data . "\n";
			$pos++;
		}

		return $subtitle;
	}

	public function saveNBT() : CompoundTag{
		$nbt = parent::saveNBT();
		if($this->type !== null){
			$nbt->setString('type', $this->type);
		}
		return $nbt;
	}
}