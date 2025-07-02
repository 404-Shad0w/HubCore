<?php

namespace  shadow\utils;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use QueryAPI\QueryAPI;
use QueueAPI\QueueAPI;
use shadow\Loader;
use shadow\tasks\QueueTask;

class Inventories
{
    public static function SelctorMenu(Player $player)
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $config = Loader::getInstance()->getConfig();

        $modalities = $config->get("modalities", []);
        $center = 11;
        $count = count($modalities);
        $start = $center - intdiv($count, 2);

        $i = 0;
        foreach ($modalities as $key => $modality) {
            $query = QueryAPI::query($modality["ip"], $modality["port"]);
            $item = VanillaItems::PAPER();
            $item->setCustomName($modality["format"]);
            $item->setLore([
                "§l§6Players: §r§7".$query["OnlinePlayers"] . "/" . $query["MaxPlayers"],
                "§l§6Status: §f" . ($modality["status"] === "online" ? "§aOnline" : "§cOffline"),
            ]);
            $item->getNamedTag()->setString("modality_key", $key);

            $menu->getInventory()->setItem($start + $i, $item);
            $i++;
        }

        $menu->setListener(function (InvMenuTransaction $transaction) use ($modalities, $query): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $clickedItem = $transaction->getItemClicked();
            $modalityKey = $clickedItem->getNamedTag()->getString("modality_key", "");
            $status = $modalities[$clickedItem->getNamedTag()->getString("modality_key", "")]["status"];

            if ($query["OnlinePlayers"] === $query["MaxPlayers"]) {
                $player->sendMessage("§cfull modality.");
                return $transaction->discard();
            }

            if ($status === "offline") {
                $player->sendMessage("§coffline mode");
                return $transaction->discard();
            }

            if ($status === "whitelist" && !$player->hasPermission($modalityKey."join")) {
                $player->sendMessage("§cwhitelist mode");
                return $transaction->discard();
            }



            if ($modalityKey !== "" && isset($modalities[$modalityKey])) {
                $modality = $modalities[$modalityKey];
                if ($modality["status"] === "online") {
                    Loader::getInstance()->getScheduler()->scheduleRepeatingTask(
                        new QueueTask($player, $modalityKey, $modality["ip"], $modality["port"]),
                        40
                    );
                    QueueAPI::add($player->getName(), $modalityKey);
                } elseif ($modality["status"] === "whitelist" && $player->hasPermission($modalityKey."join")) {
                    Loader::getInstance()->getScheduler()->scheduleRepeatingTask(
                        new QueueTask($player, $modalityKey, $modality["ip"], $modality["port"]),
                        40
                    );
                    QueueAPI::add($player->getName(), $modalityKey);
                } else {
                    $player->sendMessage("§coffline mode");
                }
            }
            return $transaction->discard();
        });
        $menu->send($player, "§l§6Server Selector");
    }
}