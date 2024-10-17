<?php

declare(strict_types=1);

namespace MizukiCore;

use pocketmine\event\Listener as PMListener;

final class Listener implements PMListener{

	public function __construct(private Main $plugin){ }

}
