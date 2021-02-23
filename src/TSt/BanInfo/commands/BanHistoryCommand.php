<?php
namespace TSt\BanInfo\commands;

use TSt\BanInfo\Loader;
use TSt\BanInfo\APIs\CommandsClass;
use TSt\BanInfo\APIs\BanInfoClass;
use TSt\BanInfo\TranslateClass;
use TSt\BanInfo\APIs\BanInfoApi;

use pocketmine\command\CommandSender;
use pocketmine\Player;

class BanHistoryCommand extends CommandsClass{
	public function __construct(Loader $plugin){
        parent::__construct($plugin, "playerbans", "Player's bans history", "/pbans <ник>", null, ["pbans"]);
        $this->setPermission("baninfo.commands.history");
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
            $sender->sendMessage($translation->getTranslation("baninfo.history.usage"));
			return false;
		}
        $name = $args[0];
        $name = mb_strtolower($name, "UTF-8");
        $banInfo = new BanInfoClass($this->getPlugin());
        $bInfo = $banInfo->get($name);
        if($bInfo != null){
            $api = new BanInfoApi($this->getPlugin());
            $api->updateHistory($bInfo);
        }
        if(file_exists($this->getPlugin()->getDataFolder().'players/'.$name.'.json')){
            $historyList = json_decode(file_get_contents($this->getPlugin()->getDataFolder().'players/'.$name.'.json'),true);
            $historyList['bans'] = array_reverse($historyList['bans']);
            $items = [];
            if($historyList['bans_count'] <= 10){
                for($k=0;$k<$historyList['bans_count'];$k++){
                    $active = '';
                    if($bInfo != null){
                        if($bInfo->bannedDate == $historyList['bans'][$k]['bannedDate']){
                            $active = ' '.$translation->getTranslation("baninfo.active");
                        }
                    }
                    $date = date('j.m.Y H:i', $historyList['bans'][$k]['bannedDate']);
                    $items[] = $translation->getTranslation("baninfo.history.item", [$date,$historyList['bans'][$k]['bannedBy'],$historyList['bans'][$k]['reason']]) . $active;
                }
                $items = array_reverse($items);
                $msg = implode("\n", $items);
            }else{
                if(!isset($args[1]) or $args[1] < 1){
                    $page = 1;
                }else{
                    $page = $args[1];   
                }
                $offset = 10*($page-1);
                $pages = ceil($historyList['bans_count']/10);
                $count = $historyList['bans_count'] - $offset;
                if($count > 10) $count = 10;
                if($pages >= $page){
                    for($k=$offset; $k<$count+$offset; $k++){
                        $active = '';
                        if($bInfo != null){
                            if($bInfo->bannedDate == $historyList['bans'][$k]['bannedDate']){
                                $active = ' '.$translation->getTranslation("baninfo.active");
                            }
                        }
                        $date = date('j.m.Y H:i', $historyList['bans'][$k]['bannedDate']);
                        $items[] = $translation->getTranslation("baninfo.history.item", [$date,$historyList['bans'][$k]['bannedBy'],$historyList['bans'][$k]['reason']]) . $active;
                    }
                    $items = array_reverse($items);
                    $msg = implode("\n", $items);
                    $msg .="\n". $translation->getTranslation("baninfo.history.footer", [$page, $pages]);
                }else{
                    $msg = $translation->getTranslation("baninfo.history.page_not_found", [$page]);
                }
            }
            $msg .= "\n".$translation->getTranslation("baninfo.history.total_count", [$historyList['bans_count']]);
        }else{
            $msg = $translation->getTranslation("baninfo.history.not_found");
        }
        $sender->sendMessage($msg);
    }
}
