<?php

namespace shadow\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use shadow\Loader;
use shadow\npcs\NpcsEntity;

class NpcModalityesCommand extends Command
{
    public function __construct()
    {
        parent::__construct("hubnpcs", "Manage NPC modalities");
        $this->setPermission("admin.perms");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $player, string $label, array $args)
    {
        if (!$player instanceof Player) return;

        if (empty($args[0])) {
            $player->sendMessage("§cUse: /hubnpc <modalidad>");
            return;
        }

        $modalityKey = strtolower($args[0]);
        $config = Loader::getInstance()->getConfig();
        $modalities = $config->get("modalities", []);

        if (!isset($modalities[$modalityKey])) {
            $player->sendMessage("§cMode not found.");
            return;
        }

        $modality = $modalities[$modalityKey];
        $format = $modality["format"] ?? $modalityKey;

        $npc = NpcsEntity::create($player, $format, $modalityKey);
        $npc->spawnToAll();
        $player->sendMessage(TextFormat::colorize("§aNPC of mode §f{$format}§a created."));
    }
}