<?php
define('CONTENT_TYPE_NAME', 'news_item');


class CRss
{
    private $db;
    private $is_dev_env = true;
    private $newLine = "<br/>";
    public $totalItemsAdded = 0;
    public $totalItemsFound = 0;
    public function CRss() {
        
        if(strpos($_SERVER['SERVER_SOFTWARE'], 'Google App Engine') !== false) {
          $this->is_dev_env = false;
        }
    }
    
    
    private function okToProcessItem($title, $description, $stationName, $pubDate){
        
        if (strlen($title) <= 0 || strlen($description) <= 0){
            return false;
        }
        if (!$this->noBadWordsFound($title,$stationName)){
            return false;
        }
        
        $ignoreYears = array("2006", "2007", "2008", "2009", "2010", "2011", "2012", "2013");
       
//echo $pubDate;exit;
        foreach ($ignoreYears as $year) {
            $found = strstr($pubDate, $year);
            if ($found)
            return false;
        }
        return true;
    }
    
    // method declaration
    public function getChannelNews($stations) {
        $count = 0;
        $news = array();
        //print("<pre>");
        //print_r($stations);
        //exit;
        print("<pre>");
        foreach ($stations as $station => $value) :
            $stationName = $station; 
                
            
            foreach ($value['channels'] as $key => $channel) :
               //print_r($channel);
                //exit;
                $channelUrl = $channel->news_channel_rss_url['und'][0]['value'];
                echo $channelUrl . "<br/>";
                try {
                    //echo $channelUrl . $this->newLine;
                    //Hack for press tv
                    //ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0');
                    if (!$this->is_dev_env) {
                        //Needed for GAE
                        libxml_disable_entity_loader(false);
                    }
                    ini_set('user_agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0');
                    try {
                        
                        @$channelXML = simplexml_load_file($channelUrl);
                    } catch (Exception $exc) {
                        echo "Error parsing channel for :" . $channelUrl . $this->newLine;
                    }
                   
                    //var_dump($channelXML); exit;
                } catch (Exception $exc) {
                    echo "Error parsing channel for :" . $channel->name . $this->newLine;
                }
                if ($channelXML) {
                    $imgUrl = '';
                    //print("<pre>");
                    //echo "total:".count($channelXML->channel->item)."<br/><br/>";
                    foreach ($channelXML->channel->item as $item) {
                        $title = (string) $item->title;
                        $title = trim($this->strip_tags_content($title));
                        $description = (string) $item->description;
                        $description = trim($this->strip_tags_content($description));
         
                            if ($this->okToProcessItem($title, $description, $stationName, (string) $item->pubDate)):
                            
                            switch ($stationName){
                                case 'new york times':
                                    //new york times
                                    $media = $item->children('http://search.yahoo.com/mrss/')->content;
                                    @$imgUrl = (string) $media->attributes()->url;
                                    //echo "$imgUrl<br/>";
                                    break;
                                case 'washington post':
                                    //washington post
                                    @$mediaGroup = $item->children('http://search.yahoo.com/mrss/')->group->content;
                                    if ($mediaGroup){
                                        foreach ($mediaGroup as $child) {
                                            $attrs = $child->attributes();
                                            $imgUrl = (string) $attrs['url'];
                                            //echo (string) "$imgUrl<br/>";
                                            break;
                                        }
                                    }
                                    break;
                                default:
                                    $media = $item->children('http://search.yahoo.com/mrss/');
                                    $attrs = $media->thumbnail->attributes();
                                    if (isset($attrs)) {
                                           @$imgUrl = (string) $attrs['url'];
                                    }
                                    break;
                                
                            }
                           
                            
                            $imgUrl = !empty($imgUrl) ? $imgUrl : '';
                            /*
                            if ($stationName== 9){
                                echo  strlen((string) $item->link)."<br/>";
                            }
                             * 
                             */
                            $pubDate = (string) $item->pubDate;
                            
                            //Should, change, must $hash = md5($stationName.sntnrntolower($title));
                            $hash = md5($stationName).md5($title);
                            //echo "$hash-<br/>";
                            if (!array_key_exists("hash-$hash", $news)) {
                                $news["hash-$hash"] = array(
                                     "news_channel_tid" => $channel->tid,
                                     "news_station_taxonomy_term_name" => $stationName,
                                     "url" => (string) $item->link,
                                     "urlImage" => $imgUrl,
                                     "title" => $title,
                                     "description" => $description,
                                     "itemDate" => $pubDate,
                                     "hash" => $hash
                                );
                            }
                            endif;
    
                    }
            }
        endforeach;
     
        
        endforeach;
        return $news;
    }



    public function noBadWordsFound($title,$stationName) {
        
        $noBadWordsFound = true;
        $lowerTitle = strtolower($title);
        switch ($stationName) {
        case 'bbc'://BBC
            $BBCBadWords = array('video:', 'audio:', 'in pictures:', 'diary:', 'country profile');
            foreach($BBCBadWords as $badword) {
                if (strpos($lowerTitle, $badword) !== false) {
                    $noBadWordsFound = false; 
                }
                if ($noBadWordsFound == true){
                    $words = explode(" ",$lowerTitle);
                    
                    if ($words[count($words)-1] == 'profile') {
                        $noBadWordsFound = false; 
                    }
                }
            }
        break;
        case 'cnn'://CNN
            $CNNBadWords = array('the buzz today');
            foreach($CNNBadWords as $badword) {
                if (strpos($lowerTitle, $badword) !== false) {
                    $noBadWordsFound = false; 
                }
            }
        break;
        }
            
        return $noBadWordsFound;
    }

    
    public function addChannelNewsItems($newsItems) {
        
        $items_found_with_similar_hashcode = $this->getNewsItemsListCheckingAgainstExistingDatabaseItems($newsItems);
        $new_news_items = array();
        $this->totalItemsFound = count($newsItems);
        //shuffle($newsItems);
        //print("<pre>");
        //print_r($items_found_with_similar_hashcode);
        //exit;
        //shuffle($newsItems);
        
        
        
        
        foreach($newsItems as $item) {
            if (!in_array($item['hash'],$items_found_with_similar_hashcode)){

                  $node = new stdClass();
                  $node->type = CONTENT_TYPE_NAME;
                  node_object_prepare($node);
                
                  $node->title    = $item['title'];
                  $node->language = LANGUAGE_NONE;
                  $node->news_item_hashcode['und'][0]['value'] = $item['hash'];
                  $node->news_item_view_count['und'][0]['value'] = 0;
                  $node->news_item_extracted['und'][0]['value'] = 0;
                  $node->news_item_channel['und'][0]['tid'] = $item['news_channel_tid'];
                  $node->body[$node->language][0]['value']   = $item['description'];
                  $node->body[$node->language][0]['summary'] = text_summary($item['description']);
                  $node->body[$node->language][0]['format']  = 'full_html';
                  $node->news_item_story_url['und'][0]['value'] = $item['url'];
                  $node->news_item_story_image_url['und'][0]['value'] = $item['urlImage'];              
                  
                
                  $path = 'news/' . date('Y-m-d') . '/' . str_replace(" ", "-", $item['title']);
                  $node->path = array('alias' => $path);
                
                  try {
                    node_save($node);
                  } catch (Exception $exc) {
                    watchdog(CONTENT_TYPE_NAME, $item['url'], null, WATCHDOG_ERROR, NULL);
                    $node = null;
                  }
                
                $new_news_items[] = $item;
                $this->totalItemsAdded++;
            }
        }
        
    }

   

    public function strip_tags_content($text, $tags = '', $invert = FALSE) {

      $text = strip_tags($text);
      preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
      $tags = array_unique($tags[1]);
       
      if(is_array($tags) AND count($tags) > 0) {
        if($invert == FALSE) {
          return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        else {
          return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
        }
      }
      elseif($invert == FALSE) {
        return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
      }
      return $text;
    }
    
    public function getNewsItemsListCheckingAgainstExistingDatabaseItems($items) {
            
        $table = 'field_data_news_item_hashcode';
        $column = 'news_item_hashcode_value';
        
        $sql = "SELECT $column  FROM  $table WHERE $column  in ( ";
        //$sqltext  = "SELECT url FROM news <br/>";
        $c = '0';
        foreach ($items as $item):
            $hash = $item['hash'];
            //echo "$hash<br/>";
            $sql .=  "'$hash',";
            //$sqltext .= ($c == 0 ? " WHRE ": "OR") . "  (url = '$url')  <br/> ";
            $c++;
        endforeach;
        $sql = rtrim($sql, ",");
        $sql .= ");";
       //echo $sql;
        $result = db_query($sql);
        
        //If results return, otherwise return empty array
        if ($result->rowCount() > 0) {
            return $result->fetchCol();
        } else {
            return array();
        }
       
        
    }
    
    
    
    
}
?>