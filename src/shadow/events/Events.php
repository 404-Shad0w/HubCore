<?php

namespace shadow\events;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;
use shadow\Loader;
use shadow\utils\Utils;

class Events implements Listener
{
    public function handleJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        Utils::ItemHub($player);
        $joinmsg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('join-message'));
        $event->setJoinMessage(TextFormat::colorize($joinmsg));
    }

    public function handleQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        Utils::ItemHub($player);
        $leavemsg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('leave-message'));
        $event->setQuitMessage(TextFormat::colorize($leavemsg));
    }

    public function handleInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if ($event->getItem()->getNamedTag()->getString('hub.items') === 'selector') {
            $event->cancel();
        } elseif ($event->getItem()->getNamedTag()->getString('hub.items') === 'debuff') {
            $event->cancel();
        }
    }
}