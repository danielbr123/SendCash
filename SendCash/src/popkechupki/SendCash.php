<?php

namespace popkechupki;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SendCash extends PluginBase implements Listener { 
    public function onEnable(){                                      
        $this->getLogger()->info(TextFormat::GREEN."SendCashを読み込みました".TextFormat::GOLD." By popkechupki");
        $this->getLogger()->info(TextFormat::RED."このプラグインはpopke LICENSEに同意した上で使用してください。");
        /*PocketMoneyAPI Road*/
        if($this->getServer()->getPluginManager()->getPlugin("PocketMoney") != null){
            $this->PocketMoney = $this->getServer()->getPluginManager()->getPlugin("PocketMoney");
            $this->getLogger()->info("PocketMoneyを検出しました。");
        }else{
            $this->getLogger()->warning("PocketMoneyが見つかりませんでした。プラグインを無効化します。");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }
    public function onCommand(CommandSender $sender, Command $command, $label, array $args){
        if($command->getName() =="cash"){
            switch (strtolower(array_shift($args))){
                case "send":
                    if(!isset($args[0], $args[1])) return $sender->sendMessage("/cash send <player> <amount>");
                    if(!$args[0] instanceof Player) return $sender->sendMessage("[SendCash]プレイヤーが見つかりませんでした。");
                    $this->PocketMoney->grantMoney($args[0], +$args[1]);
                    if (!$sender instanceof Player){
                        $this->getLogger()->info($args[0]."に".$args[1]."を送りました。");
                    }else{
                        $sender->sendMessage("[SendCash]".$args[0]."に".$args[1]."を送りました。");
                    }
                    break;

                case "grant":
                    if (!$sender->isOP()) return $sender->sendMessage("[SendCash]このコマンドはOPのみ実行できます。");
                    $players = Server::getInstance()->getOnlinePlayers();
                    $p = $sender->getName();
                    if(!isset($args[0])) return $sender->sendMessage("/cash grant <amount>");
                    if (!$sender instanceof Player){
                        $this->getLogger()->info("オンラインプレイヤー全員に".$args[0]."を送りました。");
                    }else{
                        $sender->sendMessage("[SendCash]オンラインプレイヤー全員に".$args[0]."を送りました。");
                        $this->PocketMoney->grantMoney($p, -$args[0]);
                    }
                    foreach ($players as $sender) {
                        $user = $sender->getName();
                        $this->PocketMoney->grantMoney($user, +$args[0]);
                    } 
                    break;

                default:
                    $sender->sendMessage("/cash <send | grant>");
                    break;
            }
        }
    }
}