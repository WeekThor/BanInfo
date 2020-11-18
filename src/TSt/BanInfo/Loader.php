<?php
namespace TSt\BanInfo;

use TSt\BanInfo\commands\BanInfoCommand;
use TSt\BanInfo\commands\BanInfoIP;
use TSt\BanInfo\APIs\BanInfoClass;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerKickEvent;
use TSt\BanInfo\commands\BanHistoryCommand;
use TSt\BanInfo\APIs\BannedPlayer;
use TSt\BanInfo\commands\Banlist2HistoryCommand;
use TSt\BanInfo\commands\ClearHistoryCommand;

class Loader extends PluginBase implements Listener{
  public function onLoad(){
    $this->registerCommands();
    if(!is_dir($this->getDataFolder().'players')){
        mkdir($this->getDataFolder().'players');
    }
  }
  
  public function onEnable(){
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
  }
  
  private function unregisterCommands(array $commands){
        $commandmap = $this->getServer()->getCommandMap();
        foreach($commands as $commandlabel){
            $command = $commandmap->getCommand($commandlabel);
            $command->setLabel($commandlabel . "_disabled");
            $command->unregister($commandmap);
        }
    }
    private function registerCommands(){
        $this->unregisterCommands([

        ]);
        $this->getServer()->getCommandMap()->registerAll("BanInfo", [
            new BanInfoCommand($this),
            new BanInfoIP($this),
            new BanHistoryCommand($this),
            new Banlist2HistoryCommand($this),
            new ClearHistoryCommand($this)
		]);
	}
	
	
	/**
	 * Saved for AdminProtect v1.2.4 support
	 * @param string $file
	 * @return BanInfoClass
	 */
	public function getBanInfo($file = 'banned-players.txt') : BanInfoClass {
	    switch($file){
	        case 'banned-players.txt':
	            return new BanInfoClass($this);
	            break;
	        case 'banned-ips.txt':
	            return new BanInfoClass($this, true);
	            break;
	    }
	}
	
	
	/**
	 * Create player data file when he connected
	 * @param PlayerPreLoginEvent $e
	 */
	public function onPlayerPreLogin(PlayerPreLoginEvent $e){
	    $name = mb_strtolower($e->getPlayer()->getName(), "UTF-8");
	    if(!file_exists($this->getDataFolder().'players/'.$name.'.json')){
	        $default = array(
	            'bans_count' => 0,
	            'bans' => []
	        );
	        $h = fopen($this->getDataFolder().'players/'.$name.'.json', 'w');
	        fwrite($h, json_encode($default));
	        fclose($h);
	        
	    }
	}
	
	
	/**
	 * Check if kicked player was banned and add to history
	 * Information about ban will be recorder only if the banned player was online
	 * or if banned player tries to connects to the server
	 * @param PlayerKickEvent $e
	 */
	public function onPlayerKick(PlayerKickEvent $e){
	    $player = $e->getPlayer();
	    $name = mb_strtolower($player->getName(), "UTF-8");
	    $banInfo = new BanInfoClass($this);
	    $ban = $banInfo->get($player->getName());
	    if($ban != null){
	        $alreadySeted = false;
	        if(file_exists($this->getDataFolder().'players/'.$name.'.json')){
	           $banRecords = json_decode(file_get_contents($this->getDataFolder().'players/'.$name.'.json'), true);
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
    	        $h = fopen($this->getDataFolder().'players/'.$name.'.json', 'w');
    	        fwrite($h, json_encode($banRecords));
    	        fclose($h);
	        }
	    }
	}
	
	
	/**
	 * Update player ban history
	 */
	public function updateHistory(BannedPlayer $info) {
	    if($info != null){
	        $alreadyAdded = false;
	        $name = mb_strtolower($info->player, "UTF-8");
	        if(file_exists($this->getDataFolder().'players/'.$name.'.json')){
	            $banRecords = json_decode(file_get_contents($this->getDataFolder().'players/'.$name.'.json'), true);
	        }else{
	            $banRecords = array('bans_count' => 0, 'bans' => []);
	        }
	        foreach($banRecords['bans'] as $k=>$v){
	            if($v['bannedDate'] == $info->bannedDate){
	                $alreadyAdded = true;
	            }
	        }
	        if(!$alreadyAdded){
	            $banRecords['bans_count'] = $banRecords['bans_count']+1;
	            $banRecords['bans'][] = (array)$info;
	            $h = fopen($this->getDataFolder().'players/'.$name.'.json', 'w');
	            fwrite($h, json_encode($banRecords));
	            fclose($h);
	        }
	    }
	}
	
	
}
