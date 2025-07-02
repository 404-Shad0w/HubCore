<?php

declare(strict_types=1);

namespace shadow\npcs;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use QueueAPI\QueueAPI;
use shadow\Loader;
use shadow\tasks\QueueTask;

class NpcsEntity extends Human
{
    private string $modalityKey = "";
    private string $displayText = "";
    public bool $canCollide = false;
    protected bool $immobile = true;

    protected function getInitialDragMultiplier(): float
    {
        return 0.00;
    }

    protected function getInitialGravity(): float
    {
        return 0.00;
    }

    public static function create(Player $player, string $displayText, string $modalityKey): self
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($player->getLocation()->x),
                new DoubleTag($player->getLocation()->y),
                new DoubleTag($player->getLocation()->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag(0),
                new DoubleTag(0),
                new DoubleTag(0)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($player->getLocation()->yaw),
                new FloatTag($player->getLocation()->pitch)
            ]))
            ->setString("ModalityKey", $modalityKey)
            ->setString("DisplayText", $displayText);

        $npc = new self($player->getLocation(), $player->getSkin(), $nbt);
        $npc->setModalityKey($modalityKey);
        $npc->setDisplayText($displayText);

        return $npc;
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    protected function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        if ($nbt->getTag("ModalityKey") !== null) {
            $this->modalityKey = $nbt->getString("ModalityKey");
        }
        if ($nbt->getTag("DisplayText") !== null) {
            $this->displayText = $nbt->getString("DisplayText");
        }
    }

    public function saveNBT(): CompoundTag
    {
        $nbt = parent::saveNBT();
        $nbt->setString("ModalityKey", $this->modalityKey);
        $nbt->setString("DisplayText", $this->displayText);
        return $nbt;
    }

    public function setModalityKey(string $modalityKey): void
    {
        $this->modalityKey = $modalityKey;
    }

    public function setDisplayText(string $text): void
    {
        $this->displayText = $text;
    }

    public function onUpdate(int $currentTick): bool
    {
        $this->setNameTagAlwaysVisible();
        $this->setNameTag(TextFormat::colorize($this->displayText));
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                // Solo permite remover el NPC si tiene el item especial y permiso
                if ($damager->hasPermission('npc.command') && $damager->getInventory()->getItemInHand()->getCustomName() === "§eHub NPC Remover") {
                    $this->kill();
                }
            }
            $config = Loader::getInstance()->getConfig();
            $modalities = $config->get("modalities", []);
            $modality = $modalities[$this->modalityKey];

            Loader::getInstance()->getScheduler()->scheduleRepeatingTask(
                new QueueTask($damager, $this->modalityKey, $modality["ip"], $modality["port"]),
                40
            );
            QueueAPI::add($damager->getName(), $this->modalityKey);
        }
    }
}