<?php

declare(strict_types=1);

namespace nayuki\misc;

use nayuki\Main;
use pocketmine\scheduler\Task;

abstract class AbstractTask extends Task{
	private int $currentTick = 0;
	private int $period;

	public function __construct(int $period = 1){
		Main::getInstance()->getScheduler()->scheduleRepeatingTask($this, $period);
		$this->period = $period;
	}

	public function onRun() : void{
		$this->onUpdate($this->currentTick);
		$this->currentTick += $this->period;
	}

	/**
	 * @param int $tick
	 *
	 * @return void
	 */
	abstract protected function onUpdate(int $tick) : void;
}