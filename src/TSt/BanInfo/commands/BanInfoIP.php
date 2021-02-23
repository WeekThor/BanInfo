<?php
namespace TSt\BanInfo\commands;

use TSt\BanInfo\Loader;
use TSt\BanInfo\APIs\CommandsClass;
use TSt\BanInfo\APIs\BanInfoClass;
use TSt\BanInfo\TranslateClass;

use pocketmine\command\CommandSender;
use pocketmine\Player;

class BanInfoIP extends CommandsClass{
	public function __construct(Loader $plugin){
        parent::__construct($plugin, "baninfo-ip", "IP active ban information", "/bi-ip <ник>", null, ["bi-ip", "tbiip", "biip"]);
        $this->setPermission("baninfo.commands.baninfo.ip");
    }

	public function execute(CommandSender $sender, $currentAlias, array $args){
	    if($sender instanceof Player){
	        $lang = explode('_', $sender->getLocale());
	        $translation = new TranslateClass($this->getPlugin(), mb_strtolower($lang[0], "UTF-8"));
	    }else{
	        $translation = new TranslateClass($this->getPlugin());
	    }
	    $this->setPermissionMessage($translation->getTranslation("baninfo.no_perminssions"));
	    
	    if(!$this->testPermission($sender)){
	        return false;
	    }
		
		if(count($args) === 0){
		    $sender->sendMessage($translation->getTranslation("baninfo.ip.usage"));
		    return false;
		}
        $value = array_shift($args);
        $banInfoClass = new BanInfoClass($this->getPlugin(), true);
        $baninfo = $banInfoClass->get($value);
        
        if($baninfo == null){
            $sender->sendMessage($translation->getTranslation("baninfo.ip.not_banned"));
        }else{
            $date = date('j ', $baninfo->bannedDate).$translation->getTranslatedMonth(date('n', $baninfo->bannedDate)).date(' Y H:i:s', $baninfo->bannedDate);
            if($baninfo->unbanDate != null){
                $until = date('j ', $baninfo->unbanDate).$translation->getTranslatedMonth(date('n', $baninfo->unbanDate)).date(' Y H:i:s', $baninfo->unbanDate);
            }else{
                $until = $translation->getTranslation("baninfo.never");
            }
            if($baninfo->reason == ''){
                $baninfo->reason = $translation->getTranslation("baninfo.reason.not_specified");
            }
            $sender->sendMessage($translation->getTranslation("baninfo.info_message", [$baninfo->player, $date, $baninfo->bannedBy, $until, $baninfo->reason]));
        }
    }
}
