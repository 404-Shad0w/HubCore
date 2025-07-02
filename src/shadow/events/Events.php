<?php

namespace shadow\events;

use Cosmetics\Manager\CosmeticsManager;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;
use shadow\Loader;
use shadow\scoreboard\Scoreboard;
use shadow\utils\Inventories;
use shadow\utils\Utils;

class Events implements Listener
{
    public function handleJoin(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        Utils::ItemHub($player);
        $joinmsg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('join-message'));
        $event->setJoinMessage(TextFormat::colorize($joinmsg));
        Scoreboard::send($player);
        CosmeticsManager::getInstance()->applyCosmetics($player);
    }

    public function handleQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        Utils::ItemHub($player);
        Loader::getInstance()->getScoreboardManager()->remove($player);
        $leavemsg = str_replace("{player}", $player->getName(), Loader::getInstance()->getConfig()->get('leave-message'));
        $event->setQuitMessage(TextFormat::colorize($leavemsg));
    }

    public function handleDamage(EntityDamageByEntityEvent $event): void
    {
        $event->cancel();
    }

    public function handlePlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player->hasPermission('admin.perms')) {
            return;
        }
        $event->cancel();
    }

    public function hanldeBreak(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player->hasPermission('admin.perms')) {
            return;
        }

        $event->cancel();
    }

    /**
     * @param PlayerInteractEvent $event
     * @return void
     */
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if ($event->getItem()->getNamedTag()->getString('hub.items') === 'selector') {
            Inventories::SelctorMenu($player);
            $event->cancel();
        } elseif ($event->getItem()->getNamedTag()->getString('hub.items') === 'debuff') {
            $event->cancel();
            $player = $event->getPlayer();
            $direction = $player->getDirectionVector()->normalize()->multiply(1.5);
            $player->setMotion($direction->add(0, 1, 0));
        }
    }
}