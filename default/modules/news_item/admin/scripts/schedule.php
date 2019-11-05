<?php
require_once ( dirname(__FILE__) . '/rss.php');


define('TAXONOMY_STATIONS', 'news_stations');
define('TAXONOMY_CHANNELS', 'news_channels');
define('TAXONOMY_CATEGORIES', 'news_channel_categories');
define('MODULE', 'news_feeder');

class CSchedule {

    private $time_start;
    public $totalTime;

    public function __construct() {
        $this -> time_start = microtime(true);
    }
    
    public function start() {
        $this -> time_start = microtime(true);
    }
    
    public function getStations(){
        $vocabulary = taxonomy_vocabulary_machine_name_load(TAXONOMY_STATIONS);
        $vocabulary_terms = taxonomy_get_tree($vocabulary->vid);
        return $vocabulary_terms;
    }
    
    public function getChannels($station_taxonomay_id = 0){
       
        // Load all vocabulary channels     
        $vocabulary = taxonomy_vocabulary_machine_name_load(TAXONOMY_CHANNELS);
        $vocabulary_terms = taxonomy_get_tree($vocabulary->vid);
        $channels = array();
        
        // Only add selected station channels
        foreach ($vocabulary_terms as $channel) {
              //This method below is un-documented, only way to get fields associated with taxonomy term 
              $channel_term = taxonomy_term_load($channel->tid);
              if ($channel_term->news_channel_parent['und'][0]['tid'] == $station_taxonomay_id) {
                $channels[] = $channel_term;
              }
        }
        
        return $channels;
        
    }
    
    public function start_importing_news() {

        $stations = array();
        foreach ($this->getStations() as $station) {
            $stations[$station->name]["channels"] = $this->getChannels($station->tid);
        }
        
        
        /* Test loop to list all channels 
        foreach ($stations as $station) {
            foreach ($station['channels'] as $channel) {
                echo $channel->name."<br/>"; 
            }
        }exit;
        */
        
        
        
        $rss = new CRss();
        
        $newsItems = $rss->getChannelNews($stations);
        //print_r("<pre>");
        //var_dump($newsItems);exit;
        $rss->addChannelNewsItems($newsItems);
        
        
        // Stats after finishing importing news items
        
        $itemsFound = $rss->totalItemsFound;
        $itemsAdded = $rss->totalItemsAdded;
        watchdog(MODULE, "News Imported: $itemsFound Added: $itemsAdded", null, WATCHDOG_INFO, NULL);
        
        /*
        WATCHDOG_EMERGENCY: Emergency, system is unusable.
        WATCHDOG_ALERT: Alert, action must be taken immediately.
        WATCHDOG_CRITICAL: Critical conditions.
        WATCHDOG_ERROR: Error conditions.
        WATCHDOG_WARNING: Warning conditions.
        WATCHDOG_NOTICE: (default) Normal but significant conditions.
        WATCHDOG_INFO: Informational messages.
        WATCHDOG_DEBUG: Debug-level messages.
        */
        
        
    }
}
?>