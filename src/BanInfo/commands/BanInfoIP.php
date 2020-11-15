<?php
namespace BanInfo\commands;

use BanInfo\Loader;
use BanInfo\APIs\API;
use BanInfo\APIs\DateFormatter;
use BanInfo\APIs\BanInfo;
use pocketmine\command\CommandSender;

class BanInfoIP extends API{
	public function __construct(Loader $plugin){
        parent::__construct($plugin, "baninfo-ip", "Информация о бане", "/bi-ip <ник>", null, ["bi-ip", "tbiip", "biip"]);
        $this->setPermission("baninfo.commands.baninfo.ip");
    }

	public function execute(CommandSender $sender, $currentAlias, array $args){
	    $dateFormatter = new DateFormatter();
		if(!$this->testPermission($sender)){
			return true;
		}
        
		if(count($args) === 0){
            $sender->sendMessage("§4Использование: §c/bi-ip <IP адрес>");

			return false;
		}
        $value = array_shift($args);
        $banInfoClass = new BanInfo($sender->getServer()->getDataPath() . 'banned-ips.txt');
        $baninfo = $banInfoClass->get($value);
        
        if($baninfo == null){
            $sender->sendMessage('§4[BanInfo] §cОшибка: Такого IP адреса нет в бан-листе!');
        }else{
            $date = date('j '.$dateFormatter->getMonth(date('n', $baninfo->bannedDate)).' Y H:i:s', $baninfo->bannedDate);
            if($baninfo->unbanDate != null){
                $until = date('j '.$dateFormatter->getMonth(date('n', $baninfo->unbanDate)).' Y H:i:s', $baninfo->unbanDate);
            }else{
                $until = "Никогда";
            }
            if($baninfo->reason == ''){
                $baninfo->reason = "§7(не указана)";
            }
            $sender->sendMessage("§6--=== §c".$baninfo->player."§6 ===--\n§6Забанен:§c ".$date."\n§6Забанил: §c".$baninfo->bannedBy."\n§6Окончание: §c".$until."\n§6Причина бана: §c".$baninfo->reason);
        }
        
    }
}
