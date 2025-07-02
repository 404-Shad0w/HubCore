<?php

namespace shadow;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;
use shadow\commands\NpcModalityesCommand;
use shadow\events\Events;
use shadow\npcs\NpcsEntity;
use shadow\scoreboard\ScoreboardManager;

class Loader extends PluginBase{

    use SingletonTrait;

    public ScoreboardManager $scoreboardManager;
    public function onLoad() : void{self::setInstance($this);}

    protected function onEnable(): void
    {
        $this->scoreboardManager = new ScoreboardManager();
        $this->getServer()->getPluginManager()->registerEvents(new Events(), $this);
        $this->getServer()->getCommandMap()->register("hub", new NpcModalityesCommand());
        EntityFactory::getInstance()->register(NpcsEntity::class, function (World $world, CompoundTag $nbt): NpcsEntity {
            return new NpcsEntity(EntityDataHelper::parseLocation($nbt, $world), NpcsEntity::parseSkinNBT($nbt), $nbt);
        }, ['NpcsEntity']);

        $baseDir = $this->getDataFolder() . "Cosmetics/";

        $folders = [
            $baseDir,
            $baseDir . "capes/",
            $baseDir . "wings/",
            $baseDir . "trails/",
            $baseDir . "players/",
        ];

        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
        }
    }

    /**
     * @return ScoreboardManager
     */
    public function getScoreboardManager(): ScoreboardManager
    {
        return $this->scoreboardManager;
    }
}