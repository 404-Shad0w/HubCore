<?php

namespace shadow;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use shadow\events\Events;

class Loader extends PluginBase{

    use SingletonTrait;
    public function onLoad() : void{self::setInstance($this);}

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new Events(), $this);
    }
}