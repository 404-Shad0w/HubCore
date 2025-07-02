<?php

namespace shadow\tasks;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use QueueAPI\QueueAPI;
use shadow\utils\Utils;

class QueueTask extends Task
{
    private Player $player;
    private string $queueName;
    private string $ip;
    private int $port;

    /**
     * @param Player $player
     * @param string $queueName
     * @param string $ip
     * @param int $port
     */
    public function __construct(Player $player, string $queueName, string $ip, int $port)
    {
        $this->player = $player;
        $this->queueName = $queueName;
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     * @return void
     */
    public function onRun(): void
    {
        if (!$this->player->isOnline()) {
            QueueAPI::remove($this->player->getName(), $this->queueName);
            $this->getHandler()?->cancel();
            return;
        }

        $pos = QueueAPI::getPosition($this->player->getName(), $this->queueName);
        if ($pos === 1) {
            QueueAPI::remove($this->player->getName(), $this->queueName);
            QueueAPI::next($this->queueName);
            Utils::teleportServer($this->player, $this->ip, $this->port);
            $this->getHandler()?->cancel();
        } else {
            $this->player->sendTip("§eQueue §6{$this->queueName}§e. Position: §a{$pos}");
        }
    }
}