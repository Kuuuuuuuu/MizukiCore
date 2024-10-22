<?php

declare(strict_types=1);

namespace nayuki\tasks;

use nayuki\Main;
use nayuki\misc\AbstractTask;

class MainTask extends AbstractTask{
	public function __construct(private readonly Main $main, int $period = 1){ parent::__construct($period); }

	protected function onUpdate(int $tick) : void{
		foreach($this->main->getSessionManager()->getSessions() as $session){
			$session->update();
		}
	}
}