<?php

declare(strict_types=1);

namespace nayuki\player\kit;

use nayuki\player\kit\types\Bomber;
use pocketmine\utils\RegistryTrait;

final class KitRegistry{
	use RegistryTrait;

	/**
	 * @param string $name
	 *
	 * @return BaseKit
	 */
	public static function fromString(string $name) : BaseKit{
		$kit = self::_registryFromString(strtolower($name));
		assert($kit instanceof BaseKit, "Kit '$name' not found.");
		return $kit;
	}

	/**
	 * @return array<string, BaseKit>
	 */
	public static function getKits() : array{
		return self::_registryGetAll();
	}

	protected static function setup() : void{
		self::register(new Bomber('Bomber'));
	}

	/**
	 * @param BaseKit $kit
	 */
	public static function register(BaseKit $kit) : void{
		self::_registryRegister($kit->getName(), $kit);
	}
}