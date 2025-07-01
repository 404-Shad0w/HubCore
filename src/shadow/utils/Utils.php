<?php

namespace shadow\utils;

use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class Utils
{
    public static function ItemHub(Player $player): void
    {
        $items = [
            3 => VanillaItems::COMPASS()->setCustomName(TextFormat::colorize("&l&6Server Selector"))->getNamedTag()->setString('hub.items', 'selector'),
            5 => VanillaItems::ENDER_PEARL()->setCustomName(TextFormat::colorize("&l&3Debuff"))->getNamedTag()->setString('hub.items', 'debuff'),
        ];

        foreach ($items as $slot => $name) {
            $player->getInventory()->setItem($slot, $name);
        }
    }
}