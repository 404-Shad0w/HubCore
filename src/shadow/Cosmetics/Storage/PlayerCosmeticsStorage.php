<?php

declare(strict_types=1);

namespace Cosmetics\Storage;

use pocketmine\player\Player;

class PlayerCosmeticsStorage {

    private static function getFile(Player $player): string {
        return "plugin_data/HubCore/Cosmetics/players/" . $player->getName() . ".json";
    }

    public static function get(Player $player): array {
        $file = self::getFile($player);
        return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    }

    private static function save(Player $player, array $data): void {
        file_put_contents(self::getFile($player), json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function saveCape(Player $player, string $cape): void {
        $data = self::get($player);
        $data["cape"] = $cape;
        self::save($player, $data);
    }

    public static function saveWings(Player $player, string $wings): void {
        $data = self::get($player);
        $data["wings"] = $wings;
        self::save($player, $data);
    }

    public static function saveTrail(Player $player, string $trail): void {
        $data = self::get($player);
        $data["trail"] = $trail;
        self::save($player, $data);
    }
}
