<?php

namespace shadow\scoreboard;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use QueryAPI\QueryAPI;
use shadow\Loader;

class Scoreboard
{

    public static int $tick = 0;
    public static function send(Player $player): void
    {
        $config = Loader::getInstance()->getConfig();

        $ipAnimations = $config->get('scoreboard')['stri'] ?? ["play.hub.sytes"];
        $ipText = $ipAnimations[self::$tick % count($ipAnimations)];

        $lines = [];

        $lines[] = "&e&m---------------------";
        $lines[] = "&e&r";
        $lines[] = "&l&gPing: &r&a" . $player->getNetworkSession()->getPing() . "ms";
        $modalities = $config->get('modalities') ?? [];
        $totalPlayers = 0;
        foreach ($modalities as $modality) {
            $ip = $modality['ip'] ?? "127.0.0.1";
            $port = $modality['port'] ?? 19132;
            $query = QueryAPI::query($ip, (int)$port);
            $totalPlayers += $query['OnlinePlayers'] ?? 0;
        }
        $lines[] = "&l&gOnline: &r&b" . $totalPlayers;
        $lines[] = "&e&r";
        $lines[] = "      &l&gStatus";

        $modalities = $config->get('modalities') ?? [];
        foreach ($modalities as $key => $modality) {
            $format = $modality['format'] ?? "&f" . ucfirst($key);
            $ip = $modality['ip'] ?? "127.0.0.1";
            $port = $modality['port'] ?? 19132;

            $query = QueryAPI::query($ip, (int)$port);
            $status = ($query['Status'] ?? "offline") === "online" ? "&aOnline" : "&cOffline";
            $online = $query['OnlinePlayers'] ?? 0;
            $max = $query['MaxPlayers'] ?? 0;

            $lines[] = "{$format}&7: &r({$status}&7)";
            $lines[] = "&7({$online}&8/&7{$max})";
            $lines[] = "&e&r";
        }
        $lines[] = "&e&m--------" . $ipText . "--------";

        $coloredLines = [];
        foreach ($lines as $line) {
            $coloredLines[] = TextFormat::colorize($line);
        }

        Loader::getInstance()->getScoreboardManager()->setLines($player, $coloredLines);
    }

    public static function nextTick(): void
    {
        self::$tick++;
    }
}