<?php

declare(strict_types=1);

namespace nayuki\player\kit\types;

use nayuki\forms\CustomForm;
use nayuki\Main;
use nayuki\player\kit\BaseKit;
use nayuki\player\scoreboard\Scoreboard;
use nayuki\player\scoreboard\ScoreboardUtils;
use pocketmine\block\Block;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

final class Lobby extends BaseKit{

	private static string $settingsItemName = TextFormat::RESET . TextFormat::AQUA . 'Settings' . TextFormat::RESET . TextFormat::WHITE . ' (กดค้างเพื่อเปิดเมนู)';
	private static string $shopItemName = TextFormat::RESET . TextFormat::AQUA . 'Shop' . TextFormat::RESET . TextFormat::WHITE . ' (กดค้างเพื่อเปิดเมนู)';

	/**
	 * @return Item[]
	 */
	public function getArmorItems() : array{
		return [];
	}

	/**
	 * @return Item[]
	 */
	public function getInventoryItems() : array{
		return [
			VanillaItems::AIR(),
			VanillaItems::AIR(),
			VanillaItems::AIR(),
			VanillaItems::AIR(),
			VanillaItems::COMPASS()->setCustomName(self::$settingsItemName),
			VanillaItems::BOOK()->setCustomName(self::$shopItemName)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FORTUNE(), 9)),
		];
	}

	public function setEffect(Player $player) : void{
		// TODO: Implement setEffect() method.
	}

	public function handleBlockSkill(Player $player, Block $blockAgainst, Item $itemOnHand) : void{
		// TODO: Implement handleBlockSkill() method.
	}

	public function handleItemSkill(Player $player, Item $itemOnHand) : void{
		if($itemOnHand->getTypeId() === VanillaItems::BOOK()->getTypeId() && $itemOnHand->getCustomName() === self::$shopItemName){
			// TODO: making some shop ui but rn I'm too sleepy to do it :p
		}elseif($itemOnHand->getTypeId() === VanillaItems::COMPASS()->getTypeId() && $itemOnHand->getCustomName() === self::$settingsItemName){
			$session = Main::getInstance()->getSessionManager()->getSession($player);
			$form = new CustomForm(function(Player $player, array $data = null) use ($session) : void{
				if($data === null){
					return;
				}

				$handlers = [
					'cps' => function(bool $value) use ($session){
						$session->setCpsCounterEnabled($value);
					},
					'scoreboard' => function(bool $value) use ($session, $player){
						$session->setScoreboardEnabled($value);

						if($value){
							Scoreboard::spawn($player);
						}else{
							ScoreboardUtils::remove($player);
						}
					}
				];

				foreach($data as $key => $value){
					$handler = $handlers[strtolower($key)] ?? null;
					if($handler !== null){
						$handler((bool) $value);
					}
				}
			});

			$form->setTitle(TextFormat::AQUA . 'Settings');
			$form->addToggle('CPS Counter', $session->isCpsCounterEnabled(), 'cps');
			$form->addToggle('Scoreboard', $session->isScoreboardEnabled(), 'scoreboard');

			$player->sendForm($form);
		}
	}
}