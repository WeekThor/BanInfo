<?php
namespace TSt\BanInfo\commands;

use TSt\BanInfo\Loader;
use TSt\BanInfo\APIs\API;
use TSt\BanInfo\APIs\BanInfoClass;
use TSt\BanInfo\TranslateClass;

use pocketmine\command\CommandSender;
use pocketmine\Player;

class Banlist2HistoryCommand extends API{
	public function __construct(Loader $plugin){
        parent::__construct($plugin, "bans2history", "Export banlist to players history", "/exportbans", null, [ "portbanlist", "exportbans"]);
        $this->setPermission("baninfo.commands.bans2history");
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
		$banInfo = new BanInfoClass($this->getPlugin());
		$bans = $banInfo->getAll();
		$total = 0;
		$sender->sendMessage($translation->getTranslation("baninfo.export.start"));
		foreach($bans as $v){
		    $total++;
		    $this->getPlugin()->updateHistory($v);
		}
		$sender->sendMessage($translation->getTranslation("baninfo.export.end", [$total]));
    }
}
