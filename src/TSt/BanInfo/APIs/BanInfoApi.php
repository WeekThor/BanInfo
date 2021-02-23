<?php
namespace TSt\BanInfo\APIs;

use TSt\BanInfo\Loader;

class BanInfoApi{
    public $api_version = '1.0.0';
    private $plugin;
    public function  __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }
    
    /**
     * Update Player's ban history
     * @param string|BannedPlayer $player Player name or BannedPlayer class records
     * @return void
     */
    public function updateHistory($player) : void{
        if($player instanceof BannedPlayer){
            $ban = $player;
            $player = $ban->player;
        }else{
            $player = mb_strtolower($player);
            $bInfo = new BanInfoClass($this->plugin);
            $ban = $bInfo->get($player);
        }
        if($ban != null){
            $alreadySeted = false;
            if(file_exists($this->plugin->getDataFolder().'players/'.$player.'.json')){
                $banRecords = json_decode(file_get_contents($this->plugin->getDataFolder().'players/'.$player.'.json'), true);
            }else{
                $banRecords = array('bans_count' => 0, 'bans' => []);
            }
            foreach($banRecords['bans'] as $k=>$v){
                if($v['bannedDate'] == $ban->bannedDate){
                    $alreadySeted = true;
                }
            }
            if(!$alreadySeted){
                $banRecords['bans_count'] = $banRecords['bans_count']+1;
                $banRecords['bans'][] = (array)$ban;
                $h = fopen($this->plugin->getDataFolder().'players/'.$player.'.json', 'w');
                fwrite($h, json_encode($banRecords));
                fclose($h);
            }
        }
    }
    
    /**
     * Get BanInfoClass
     * @param bool $needIP
     * @return BanInfoClass
     */
    public function getBanInfo(bool $needIP):BanInfoClass{
        return new BanInfoClass($this->plugin, $needIP);
    }
}