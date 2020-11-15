<?php
namespace BanInfo\APIs;

class BanInfo{
    private $file;
   // private $bans;
    public function  __construct(string $file) {
       $this->file = $file;
       $this->load();
    }
    
    private function load() {
        $bans = file_get_contents($this->file);
       // echo $bans;
        $bans = str_replace(" victim name | ban date | banned by | banned until | reason\n", '', $bans);
        $bans = explode("#", $bans);
        unset($bans[0]);
        unset($bans[1]);
        
        $bans = implode('#', $bans);
        $banslist = explode("\n", $bans);
        $banlist = array();
        for($k = 0; $k<count($banslist); $k++){
            $banedinfo = explode("|", $banslist[$k]);
            try{
                $banlist[$banedinfo[0]] = new BannedPlayer($banedinfo[0], strtotime($banedinfo[1]), $banedinfo[2], strtotime($banedinfo[3]), $banedinfo[4]);
            }catch (\Exception $e){
                //...
            }
        }
        $this->bans = $banlist;
    }
    
    public function get($name) : ? BannedPlayer{
        $name = mb_strtolower($name, "UTF-8");
        if(isset($this->bans[$name])){
            return $this->bans[$name];
        }else{
            return null;
        }
        
    }
}

class BannedPlayer{
    
    public $player;
    public $bannedDate;
    public $bannedBy;
    public $unbanDate;
    public $reason;
    
    public function  __construct(string $name, int $date, string $admin, int $banUntil, string $reason) {
        $this->player = $name;
        $this->bannedBy = $admin;
        $this->bannedDate = $date;
        $this->reason = $reason;
        $this->unbanDate = $banUntil;
    }
}