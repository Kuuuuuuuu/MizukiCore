<?php

declare(strict_types=1);

namespace nayuki\player\kit;

use InvalidArgumentException;
use nayuki\player\kit\types\Assassin;
use nayuki\player\kit\types\Berserker;
use nayuki\player\kit\types\Bomber;
use nayuki\player\kit\types\Fisherman;
use nayuki\player\kit\types\Freezer;
use nayuki\player\kit\types\Mage;
use nayuki\player\kit\types\Priest;
use nayuki\player\kit\types\Pyro;
use nayuki\player\kit\types\Ranger;
use nayuki\player\kit\types\Tank;
use nayuki\player\kit\types\Warrior;
use pocketmine\utils\RegistryTrait;

final class KitRegistry{
	use RegistryTrait;

	public static function fromString(string $name) : BaseKit|false{
		try{
			$kit = self::_registryFromString(strtolower($name));
			if(!($kit instanceof BaseKit)){
				return false;
			}
			return $kit;
		}catch(InvalidArgumentException){
			return false;
		}
	}

	protected static function setup() : void{
		self::register(new Assassin('Assassin'));
		self::register(new Berserker('Berserker'));
		self::register(new Bomber('Bomber'));
		self::register(new Fisherman('Fisherman'));
		self::register(new Freezer('Freezer'));
		self::register(new Mage('Mage'));
		self::register(new Priest('Priest'));
		self::register(new Pyro('Pyro'));
		self::register(new Ranger('Ranger'));
		self::register(new Tank('Tank'));
		self::register(new Warrior('Warrior'));
	}

	/**
	 * @param BaseKit $kit
	 */
	public static function register(BaseKit $kit) : void{
		self::_registryRegister($kit->getName(), $kit);
	}
}