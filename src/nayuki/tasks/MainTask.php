<?php

namespace nayuki\tasks;

use nayuki\misc\AbstractTask;

class MainTask extends AbstractTask{
	protected function onUpdate(int $tick) : void{
		if($tick % 20 === 0){

		}
	}

}