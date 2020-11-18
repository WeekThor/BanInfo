<?php
namespace TSt\BanInfo;

class TranslateClass{
    private $lang;
    private $lang_list = ["ru", "en"];
    private $default_lang = "en";
    private $plugin;
    
    public function __construct(Loader $plugin, string $lang = "en") {
        if(!in_array($lang, $this->lang_list)){
            $lang = $this->default_lang;
        }
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
        $recources = $plugin->getResources();
        try{
            $locales = json_decode(file_get_contents($recources['locales.json']->getPath().'/locales.json'), true);
            $message = $locales[$this->lang][$message_name];
            for($k = 0; $k <count($placeholders); $k++){
                $message = str_replace('{'.$k.'}', $placeholders[$k], $message);
            }
            return $message;
        }catch (\Exception $e){
            return $e->getMessage();
            $plugin->getServer()->getLogger()->error($e->getMessage());
        }
    }
    
    
    
    /**
     * Get translated month name
     * @param int $month
     * @return string
     */
    public function getTranslatedMonth(int $month) {
        $plugin = $this->plugin;
        $recources = $plugin->getResources();
        try{
            $locales = json_decode(file_get_contents($recources['locales.json']->getPath().'/locales.json'), true);
            return $locales[$this->lang]['month'][$month-1];
        }catch (\Exception $e){
            return $e->getMessage();
            $this->plugin->getServer()->getLogger()->error($e->getMessage());
        }
    }
}