<?php

namespace nayuki;

use pocketmine\utils\TextFormat;

final class Constants{
	public const PREFIX = TextFormat::DARK_GRAY . '[' . TextFormat::AQUA . 'MizukiCore' . TextFormat::DARK_GRAY . '] ' . TextFormat::RESET;
	public const LOBBY_COORDS = [
		'x' => 13,
		'y' => 118,
		'z' => 372,
	];
	public const ARENA_LOBBY_COORDS = [
		'-46:32:-50',
		'20:7:2',
		'16:20:-22',
		'21:19:85',
		'44:37:-69',
		'82:-13:1',
		'-91:48:100',
		'-95:7:-32',
		'-117:17:-108',
		'-23:7:30',
		'-55:6:9'
	];
	public const GOLDEN_APPLE_DROP_COORDS = [
		'12:6:86',
		'108:4:-131',
		'-122:17:79'
	];
}