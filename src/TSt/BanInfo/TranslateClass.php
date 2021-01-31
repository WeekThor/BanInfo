<?php
namespace TSt\BanInfo;

class TranslateClass{
    private $lang;
    private $default_lang = "en";
    private $plugin;
    
    public function __construct(Loader $plugin, string $lang = "en") {
        $this->lang = $lang;
        $this->plugin = $plugin;
    }
    
    
    /**
     * Get translated plugin's message
     * @param string $message_name
     * @param array $placeholders
     * @return string
     */
    public function getTranslation(string $message_name, array $placeholders =[]) {
        $plugin = $this->plugin;
        $resourse = $plugin->getResource('locales/'.$this->lang.'.json');
        if($resourse === null){
            $resourse = $plugin->getResource('locales/'.$this->default_lang.'.json');
        }
        $stat = fstat($resourse);
        $locale = json_decode(fread($resourse, $stat['size']), true);
        $message = $locale[$message_name];
        for($k = 0; $k <count($placeholders); $k++){
            $message = str_replace('{'.$k.'}', $placeholders[$k], $message);
        }
        fcloss($resourse);
        return $message;
    }
    
    
    
    /**
     * Get translated month name
     * @param int $month
     * @return string
     */
    public function getTranslatedMonth(int $month) {
        $plugin = $this->plugin;
        $resourse = $plugin->getResource('locales/'.$this->lang.'.json');
        if($resourse === null){
            $resourse = $plugin->getResource('locales/'.$this->default_lang.'.json');
        }
        $stat = fstat($resourse);
        $locale = json_decode(fread($resourse, $stat['size']), true);
        $message = $locale['month'][$month-1];
        fclose($resourse);
        return $message;
    }
}
