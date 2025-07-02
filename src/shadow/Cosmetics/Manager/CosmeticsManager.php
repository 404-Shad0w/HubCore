<?php

declare(strict_types=1);

namespace Cosmetics\Manager;

use Cosmetics\Storage\PlayerCosmeticsStorage;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\plugin\PluginBase;
use shadow\Loader;

class CosmeticsManager {

    private static ?self $instance = null;

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    public function applyCosmetics(Player $player): void {
        $data = PlayerCosmeticsStorage::get($player);

        $capeTexture = $this->loadCapeTexture($data["cape"] ?? null);
        $wingsSkin = $this->loadWingsSkin($data["wings"] ?? null);

        if ($wingsSkin !== null) {
            $player->setSkin($wingsSkin);
            $player->sendSkin();
            return;
        }

        if ($capeTexture !== null) {
            $skin = new Skin(
                "Standard_Custom_Cape",
                $capeTexture,
                "",
                "geometry.humanoid.custom"
            );
            $player->setSkin($skin);
            $player->sendSkin();
            return;
        }

        // Si no hay cosméticos guardados, no hacemos nada
    }

    public function sendCapeMenu(Player $player): void {
        $capesDir = "plugin_data/HubCore/Cosmetics/capes/";
        $files = array_filter(scandir($capesDir), fn($f) => str_ends_with($f, ".png"));

        $form = new SimpleForm(function(Player $player, $data = null) use ($files): void {
            if ($data === null) return;
            PlayerCosmeticsStorage::saveCape($player, $data);
            $player->sendMessage("§aCapa seleccionada: §f" . $data);
        });

        $form->setTitle("§l🧢 Selecciona tu capa");
        foreach ($files as $file) $form->addButton($file, -1, "", $file);
        $player->sendForm($form);
    }

    public function sendWingsMenu(Player $player): void {
        $wingsDir = "plugin_data/HubCore/Cosmetics/wings/";
        $files = array_filter(scandir($wingsDir), fn($f) => str_ends_with($f, ".geo.json"));

        $form = new SimpleForm(function(Player $player, $data = null) use ($files): void {
            if ($data === null) return;
            PlayerCosmeticsStorage::saveWings($player, $data);
            $player->sendMessage("§aAlas seleccionadas: §f" . $data);
        });

        $form->setTitle("§l🪽 Selecciona tus alas");
        foreach ($files as $file) $form->addButton($file, -1, "", $file);
        $player->sendForm($form);
    }

    public function sendTrailMenu(Player $player): void {
        $trailDir = "plugin_data/HubCore/Cosmetics/trails/";
        $files = array_filter(scandir($trailDir), fn($f) => str_ends_with($f, ".json"));

        $form = new SimpleForm(function(Player $player, $data = null) use ($files): void {
            if ($data === null) return;
            PlayerCosmeticsStorage::saveTrail($player, $data);
            $player->sendMessage("§aBandada seleccionada: §f" . $data);
        });

        $form->setTitle("§l✨ Selecciona tu bandada");
        foreach ($files as $file) $form->addButton($file, -1, "", $file);
        $player->sendForm($form);
    }

    private function loadCapeTexture(?string $capeFile): ?string {
        if ($capeFile === null) return null;
        $path = $this->getPluginDataFolder() . "Cosmetics/capes/" . $capeFile;
        if (!file_exists($path)) return null;
        return file_get_contents($path);
    }
    private function loadWingsSkin(?string $wingsName): ?Skin {
        if ($wingsName === null) return null;

        $folder = $this->getPluginDataFolder() . "Cosmetics/wings/" . pathinfo($wingsName, PATHINFO_FILENAME) . "/";
        $geoFile = $folder . "wings.geo.json";
        $textureFile = $folder . "wings.png";

        if (!file_exists($geoFile) || !file_exists($textureFile)) return null;

        $geometry = file_get_contents($geoFile);
        $texture = file_get_contents($textureFile);

        return new Skin(
            "geometry.wings",
            $texture,
            $geometry,
            "geometry"
        );
    }

    private function getPluginDataFolder(): string {
        // Ajusta esta ruta para apuntar a la carpeta de datos de tu plugin
        return Loader::getInstance()->getDataFolder();
    }
}
