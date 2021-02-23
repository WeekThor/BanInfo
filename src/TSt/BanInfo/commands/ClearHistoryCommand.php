<?php
namespace TSt\BanInfo\commands;

use TSt\BanInfo\Loader;
use TSt\BanInfo\APIs\CommandsClass;
use TSt\BanInfo\TranslateClass;

use pocketmine\command\CommandSender;
use pocketmine\Player;

class ClearHistoryCommand extends CommandsClass{
	public function __construct(Loader $plugin){
        parent::__construct($plugin, "clearhistory", "Clear player ban history", "/clearhistory <player>", null, [ "cleansoul"]);
        $this->setPermission("baninfo.commands.clearhistory");
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
	    if(count($args) < 1){
	        $sender->sendMessage($translation->getTranslation("baninfo.history.clear.usage"));
	        return false;
	    }
	    $name = mb_strtolower($args[0], "UTF-8");
	    $msg = '';
	    if(file_exists($this->getPlugin()->getDataFolder().'players/'.$name.'.json')){
	        $banHistory = json_decode(file_get_contents($this->getPlugin()->getDataFolder().'players/'.$name.'.json'), true);
	        if(isset($args[1])){
	            if(!is_numeric($args[1])){
	               $msg = $translation->getTranslation("baninfo.history.clear.usage");
	            }else{
	                $new_count = 0;
	                $date = strtotime(date("d.m.Y H:i")."-{$args[1]} days");
	                $new_bans = [];
	                foreach($banHistory['bans'] as $ban){
	                    if($ban['bannedDate'] >= $date){
	                        $new_count++;
	                        $new_bans[] = $ban;
	                    }
	                }
	                $total = $banHistory['bans_count'] - $new_count;
	                $banHistory['bans_count'] = $new_count;
	                $banHistory['bans'] = $new_bans;
	                $h = fopen($this->getPlugin()->getDataFolder().'players/'.$name.'.json', 'w');
	                fwrite($h, json_encode($banHistory));
	                fclose($h);
	                $msg = $translation->getTranslation("baninfo.history.clear.complete", [$total, $name]);
	                $msg .= " ".$translation->getTranslation("baninfo.history.older_than", [$args[1]]);
	            }
	        }else{
	            unlink($this->getPlugin()->getDataFolder().'players/'.$name.'.json');
	            $msg = $translation->getTranslation("baninfo.history.clear.complete", [$banHistory['bans_count'], $name]);
	        }
	    }else{
	        $msg = $translation->getTranslation("baninfo.history.not_found");
	    }
	    $sender->sendMessage($msg);
    }
}
