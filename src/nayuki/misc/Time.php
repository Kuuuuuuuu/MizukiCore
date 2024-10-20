<?php

namespace nayuki\misc;

final class Time{
	public static function secondsToTicks(int $seconds) : int{
		return $seconds * 20;
	}

	public static function minutesToTicks(int $minutes) : int{
		return self::secondsToTicks($minutes * 60);
	}

	public static function hoursToTicks(int $hours) : int{
		return self::minutesToTicks($hours * 60);
	}
}