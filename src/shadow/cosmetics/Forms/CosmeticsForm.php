<?php

declare(strict_types=1);

namespace Cosmetics\Forms;

use Cosmetics\Manager\CosmeticsManager;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;

class CosmeticsForm {

    public function sendMain(Player $player): void {
        $form = new SimpleForm(function (Player $player, $data = null): void {
            if ($data === null) return;
            match ($data) {
                "cape" => CosmeticsManager::getInstance()->sendCapeMenu($player),
                "wings" => CosmeticsManager::getInstance()->sendWingsMenu($player),
                "trail" => CosmeticsManager::getInstance()->sendTrailMenu($player),
            };
        });

        $form->setTitle("§l✦ COSMÉTICOS ✦");
        $form->addButton("🧢 Capas", -1, "", "cape");
        $form->addButton("🪽 Alas", -1, "", "wings");
        $form->addButton("✨ Bandadas", -1, "", "trail");
        $player->sendForm($form);
    }
}
