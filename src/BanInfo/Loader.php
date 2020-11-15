<?php
namespace BanInfo;

use BanInfo\commands\BanInfoCommand;
use BanInfo\commands\BanInfoIP;
use BanInfo\APIs\BanInfo;

use pocketmine\plugin\PluginBase;


class Loader extends PluginBase{
  public function onLoad(){
    $this->registerCommands();
    $this->getServer()->getLogger()->info("§2[BanInfo]§a Загрузка....");
    if(!(file_exists($this->getServer()->getDataPath(). 'banned-ips.txt') and file_exists($this->getServer()->getDataPath(). 'banned-players.txt') and file_exists($this->getServer()->getDataPath(). 'banned-cids.txt'))){
        $this->getServer()->getLogger()->info("§4[BanInfo]§c Плагин не может корректно работать на данном сервере!");
        $this->setEnabled(false);
    }
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
            new BanInfoIP($this)
		]);
	}
	
	public function getBanInfo($file) : BanInfo {
	    return new BanInfo($file);
	}
}