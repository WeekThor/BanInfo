<?php
namespace BanInfo\APIs;

class DateFormatter{
    private $month = [
                        'января',
                        'февраля',
                        'марта',
                        'апреля',
                        'мая',
                        'июня',
                        'июля',
                        'августа',
                        'сентября',
                        'октября',
                        'ноября',
                        'декабря'
                    ];
    
    /**
     * @param int $month
     * @return string
     */
    public function getMonth($month) {
        return $this->month[$month-1];
    }
}