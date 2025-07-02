<?php

declare(strict_types=1);

namespace Cosmetics\Commands;

use Cosmetics\Forms\CosmeticsForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CosmeticsCommand extends Command {

    public function __construct() {
        parent::__construct("cosmeticos", "Abre el menú de cosméticos");
        $this->setPermission("cosmetics.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cEste comando solo puede ser usado en el juego.");
            return;
        }
        (new CosmeticsForm())->sendMain($sender);
    }
}
