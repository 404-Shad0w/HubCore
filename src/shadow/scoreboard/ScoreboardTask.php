<?php

namespace shadow\scoreboard;

use pocketmine\scheduler\Task;
use shadow\Loader;
use shadow\scoreboard\Scoreboard;

class ScoreboardTask extends Task {

    public function onRun(): void {
        Scoreboard::nextTick();

        foreach (Loader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            Scoreboard::send($player);
        }
    }
}
