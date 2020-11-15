<?php
namespace BanInfo\commands;

use BanInfo\Loader;
use BanInfo\APIs\API;
use BanInfo\APIs\DateFormatter;
use BanInfo\APIs\BanInfo;
use pocketmine\command\CommandSender;

class BanInfoIP extends API{
	public function __construct(Loader $plugin){
        parent::__construct($plugin, "baninfo-ip", "Information about IP ban", "/bi-ip <IP aress>", null, ["bi-ip", "tbiip", "biip"]);
        $this->setPermission("baninfo.commands.baninfo.ip");
    }

	public function execute(CommandSender $sender, $currentAlias, array $args){
	    $dateFormatter = new DateFormatter();
		if(!$this->testPermission($sender)){
			return true;
		}
        
		if(count($args) === 0){
            $sender->sendMessage("§4Use: §c/bi-ip <IP adress>");

			return false;
		}
        $value = array_shift($args);
        $banInfoClass = new BanInfo($sender->getServer()->getDataPath() . 'banned-ips.txt');
        $baninfo = $banInfoClass->get($value);
        
        if($baninfo == null){
            $sender->sendMessage('§4[BanInfo] §cError: IP adress not banned!');
        }else{
            $date = date('j M Y H:i:s', $baninfo->bannedDate);
            if($baninfo->unbanDate != null){
                $until = date('j M Y H:i:s', $baninfo->unbanDate);
            }else{
                $until = "Never";
            }
            if($baninfo->reason == ''){
                $baninfo->reason = "§7(not specified)";
            }
            $sender->sendMessage("§6--=== §c".$baninfo->player."§6 ===--\n§6Banned:§c ".$date."\n§6Banned by: §c".$baninfo->bannedBy."\n§6Ban until: §c".$until."\n§6Ban reason: §c".$baninfo->reason);
        }
        
    }
}
