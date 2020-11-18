<?php
namespace TSt\BanInfo\APIs;

/**
 * @author WeekThor
 * Saved in order not to rewrite the commands code
 */
class BannedPlayer{
    
    public $player;
    public $bannedDate;
    public $bannedBy;
    public $unbanDate;
    public $reason;
    
    public function  __construct(string $name, int $date, string $admin, $banUntil, string $reason) {
        $this->player = $name;
        $this->bannedBy = $admin;
        $this->bannedDate = $date;
        $this->reason = $reason;
        $this->unbanDate = $banUntil;
    }
}