<?php

namespace shadow\utils;

use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use shadow\Loader;

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

    public static function setStatusModalities(string $modality, string $status, Player $player): void
    {
        $config = Loader::getInstance()->getConfig();
        $modalitys = $config->get("modality", []);

        if (!isset($modalitys[$modality])) {
            $player->sendMessage(TextFormat::colorize("&l&4Modality is not exist!"));
        }

        if ($modalitys[$modality]["status"] === $status) {
            $player->sendMessage(TextFormat::colorize("&l&4This modality is already set to {$status}!"));
        }

        $modalitys[$modality]["status"] = $status;
        $config->set("modality", $modalitys);
        $config->save();
        $player->sendMessage(TextFormat::colorize("&l&6Modality {$modality} is now set to {$status}!"));
    }

    public static function teleportServer(Player $player, string $ip, int $port): void
    {
        $player->transfer($ip, $port, TextFormat::colorize("&l&6Connecting to {$ip}:{$port}..."));
    }
}