<?php
namespace TSt\BanInfo\APIs;

use TSt\BanInfo\Loader;

class BanInfoClass{
    private $bans;
    private $plugin;
    private $needIPInfo;
    public function  __construct(Loader $plugin, bool $IPList = false) {
       $this->plugin = $plugin;
       $this->needIPInfo = $IPList;
    }
    
    
    /**
     * Get banned player information or null if not banned
     * @param string $name player name
     * @return BannedPlayer|NULL
     */
    public function get($name) : ? BannedPlayer{
        $name = mb_strtolower($name, "UTF-8");
        if($this->needIPInfo){
            $banEntry = $this->plugin->getServer()->getIPBans()->getEntry($name);
        }else{
            $banEntry = $this->plugin->getServer()->getNameBans()->getEntry($name);
        }
        
        if($banEntry != null){
            $banUntil = ($banEntry->getExpires() == null) ? null : $banEntry->getExpires()->getTimestamp();
            return new BannedPlayer($banEntry->getName(), $banEntry->getCreated()->getTimestamp(), $banEntry->getSource(), $banUntil, $banEntry->getReason());
        }else{
            return null;
        }
        
    }
    
    /**
     * Get all banned players
     * @return array
     */
    public function getAll() : array{
        $bans = $this->plugin->getServer()->getNameBans()->getEntries();
        $list = [];
        foreach($bans as $k=>$v){
            $banUntil = ($v->getExpires() == null) ? null : $v->getExpires()->getTimestamp();
            $list[] = new BannedPlayer($v->getName(), $v->getCreated()->getTimestamp(), $v->getSource(), $banUntil, $v->getReason());
        }
        return $list;
    }
}