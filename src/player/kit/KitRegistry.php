<?php

declare(strict_types=1);

namespace MizukiCore\player\kit;

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
		self::register(new Boxing('Boxing'));
		self::register(new Resistance('Resistance'));
		self::register(new Fist('Fist'));
		self::register(new Sumo('Sumo'));
	}

	/**
	 * @param BaseKit $kit
	 */
	public static function register(BaseKit $kit) : void{
		self::_registryRegister($kit->getName(), $kit);
	}
}