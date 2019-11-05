<?php
error_reporting(0);
ini_set('display_errors', 1);
//ini_get('allow_url_fopen');
require_once ("simple_html_dom.php");
//exit;
class CScrapper {

    private $url;
    private $htmlFullPage;
    public $newsContent = '';
    
    public function CScrapper($url, $station) {
        
        $this->url = $url;
        //echo $station;
        switch ($station) {
            case 'bbc':
                $this->getPage();
                $this->newsContent = $this->bbcScrape();
                $this->cleanUp();
                break;
            
            case 'sky':
                $this->getPage();
                $this->newsContent = $this->skyScrape();
                $this->cleanUp();
                break;
                
            case 'press tv':
                $this->getPage();
                $this->newsContent = $this->presstvScrape();
                $this->cleanUp();
                break;
                
             case 'nasa':
                $this->getPage();
                $this->newsContent = $this->nasaScrape();
                $this->cleanUp();
                break;
                
            case 'new york times':
                $this->getPage();
                $this->newsContent = $this->newYorkTimesScrape();
                $this->cleanUp();
                break;

            case 'washington post':
                $this->getPage();
                $this->newsContent = $this->washingtonPostScrape();
                $this->cleanUp();
                break;
             /*   */
                
            case 'aljazeera':
                $this->getPage();
                $this->newsContent = $this->aljazeeraScrape();
                $this->cleanUp();
                break;
                
                
            case 'daily mail':
                $this->getPage();
                $this->newsContent = $this->dailyMailScrape();
                $this->cleanUp();
                break;
                
             case 'reuters':
                $this->getPage();
                $this->newsContent = $this->reutersScrape();
                $this->cleanUp();
                break;
                
             case 'cnn':
                $this->getPage();
                $this->newsContent = $this->cnnScrape();
                $this->cleanUp();
                break;
                
        }
    }
    
    
    public function getPage()
    {
        $this->htmlFullPage = file_get_html($this->url);
        
    }
    
    public function cleanUp()
    {
        $this->htmlFullPage->clear();
        
        unset($this->htmlFullPage);
    }
    
    
    
    private function presstvScrape() {
        
        $text = '';
        //Clean remove all these tags
        /*
        foreach($this->htmlFullPage->find('span.story-date') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div#page-bookmark-links-head') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('h1.story-header') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.story-feature') as $e) {$e->outertext = '';}
        $this->htmlFullPage->save();
        */
        //Get the image
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        $imgFound = false;
        foreach($this->htmlFullPage->find('img#imgMain') as $e){
            $text .= $e->outertext;
        }
        
        if (!$imgFound) {
            foreach($this->htmlFullPage->find('div#mediaspace') as $e){
                $text .= $e->outertext;
            }
        }
        
        foreach($this->htmlFullPage->find('div#divDetailBody') as $e) {       
                $text .= $e->innertext;
        }
        
        $last_Paragraphp_Position = strrpos($text, "<p>");
        
        if ($pos !== false) {
                $text = substr($text, 0, $last_Paragraphp_Position - strlen("<p>"));
        }
            
        return $text;
    }

    private function dailyMailScrape() {
        
        $text = '';
        //Clean remove all these tags
       
        foreach($this->htmlFullPage->find('div#articleIconLinksContainer') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.relatedItemsTopBorder') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.relatedItems') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.column-content') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div#most-watched-videos-wrapper') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div#most-read-news-wrapper') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div#reader-comments') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div#taboola-below-main-column') as $e) {$e->outertext = '';}
        //foreach($this->htmlFullPage->find('a.author') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('p.author-section') as $e) {$e->outertext = '';}
        
        foreach($this->htmlFullPage->find('span.article-timestamp') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('h1') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('ul') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('p.byline-section') as $e) {$e->outertext = '';}
      
        $this->htmlFullPage->save();
        foreach($this->htmlFullPage->find('div#js-article-text') as $e) {       
                $text .= $e->innertext;
        }
        
        //Remove these two from the start
        //<p>By </p>
        //<p> | </p>
        
        //after removing aurhor class, <p>By </p> this is left which needs to replaced
        //$text = substr($text, 28, strlen($text));
        
        return $text;
    }
    
    private function bbcScrape() {
        
        $text = '';
        
        
        //Clean remove all these tags
        foreach($this->htmlFullPage->find('span.story-date') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div#page-bookmark-links-head') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('h1.story-header') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.story-feature') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.embedded-hyper') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.videoInStoryC') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('span.byline') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div#article-sidebar') as $e) {$e->outertext = '';}
        
        $this->htmlFullPage->save();
        $found = false;
        foreach($this->htmlFullPage->find('div.story-body') as $e) {
                
                $text .= $e->innertext;
                $found = true;
        }
        foreach($this->htmlFullPage->find('div#story-body') as $e) {
                
                $text .= $e->innertext;
                $found = true;
        }
        
        return $text;
    }
    
    private function skyScrape() {
        
        $text = '';
        $step1 = false;
        $step2 = false;
        $error = false;
        $image = false;
        
        //We are only interested <div id=articleText>
        //First we have to remove the <section id=recommendedStories which is child of <div id=articleText>
        
        //$SECTION_recommendedStories = $this->htmlFullPage->find('section#recommendedStories');
        
        
        foreach($this->htmlFullPage->find('p.author') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('p.byline') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('p.moduleCaption') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('section#recommendedStories') as $SECTION_recommendedStories) {
            
            $SECTION_recommendedStories->outertext = '';
            $this->htmlFullPage->save();
            $step1 = true;
        }
        
        
        foreach($this->htmlFullPage->find('section#primary') as $SECTION_primary) {
            $image = $SECTION_primary->innertext;
          
        }
        
        if ($step1){
            foreach($this->htmlFullPage->find('div#articleText') as $e) {
                
                $DIV_articleText  = $e->innertext;
                $step2 = true;
            }
        } else { $error = "step1 failed";}
        
        if ($step2){
            $last_Paragraphp_Position = strrpos($DIV_articleText, "<p>");
            if ($last_Paragraphp_Position === false) {
                $error = "step3 failed";
            } else{
                $text = substr($DIV_articleText, 0, $last_Paragraphp_Position - strlen("<p>"));
            }
            
        } else { $error = "step2 failed";}
        
        
        
        return   $image ? $image.$text : $text;
    }



    private function aljazeeraScrape() {
        
        $text = '';
        $image = false;
        
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        
        //Get image, then clear div/span tags
        foreach($this->htmlFullPage->find('div#vdoContainer') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.articleMediaCaption') as $e) {$e->outertext = '';}
        $this->htmlFullPage->save();
        
        foreach($this->htmlFullPage->find('div#ctl00_cphBody_ArticleMedia1_mediaView') as $e) {
            
            $image = $e->innertext;
            $image = str_replace("/mritems", 'http://www.aljazeera.com/mritems', $image);
            
        }
        //end image get
 
        foreach($this->htmlFullPage->find('div') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('span') as $e) {$e->outertext = '';}
        $this->htmlFullPage->save();
        //foreach($this->htmlFullPage->find('div#dvMainAd_Posting') as $e) {$e->outertext = '';}
        
        
        foreach($this->htmlFullPage->find('td.DetailedSummary') as $e) {
            
            $text = $e->innertext;
        }
        
        return   $image ? $image.$text : $text;
    }

    private function nasaScrape() {
        
        $text = '';
        $image = false;
        
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        
        //Get image, then clear div/span tags
        //foreach($this->htmlFullPage->find('div#vdoContainer') as $e) {$e->outertext = '';}
        //foreach($this->htmlFullPage->find('div.articleMediaCaption') as $e) {$e->outertext = '';}
        $this->htmlFullPage->save();
        
        foreach($this->htmlFullPage->find('div.field-item') as $e) {
            
            $text = $e->innertext;
            
        }
        //end image get
 
        //foreach($this->htmlFullPage->find('div#dvMainAd_Posting') as $e) {$e->outertext = '';}
   
        
        return    $text;
    }





    private function reutersScrape() {
        //$text = file_get_contents($this->url);
        $text = '';
        //sharetools-story
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        $this->htmlFullPage->save();
        //$this->htmlFullPage->load($text);
        
        foreach($this->htmlFullPage->find('div#articleImage') as $e) {
            $text = $e->innertext;
        }
        foreach($this->htmlFullPage->find('span#articleText') as $e) {
            $text = $e->innertext;
        }
        $find = array('(Reuters) -');
        $text = str_replace($find, "", $text);
        return $text;
    }
    
    private function cnnScrape() {
        
        //$text = file_get_contents($this->url);
        $text = '';
        //sharetools-story
        //foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        //
        //$this->htmlFullPage->load($text);
        foreach($this->htmlFullPage->find('div.cnn_stryshrwdgtbtm') as $e) {$e->outertext = '';}
        foreach($this->htmlFullPage->find('div.cnn_strylceclbtn') as $e) {$e->outertext = '';}
        
        foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        $this->htmlFullPage->save();
        foreach($this->htmlFullPage->find('div.cnn_strycntntlft') as $e) {
            
            $text = $e->innertext;
        }
        $find = array('(CNN)');
        $text = str_replace($find, "", $text);
        return $text;
    }


    private function washingtonPostScrape() {
    }
    
    private function newYorkTimesScrape() {
        
        $text = '';
        //sharetools-story
        //foreach($this->htmlFullPage->find('script') as $e) {$e->outertext = '';}
        //$this->htmlFullPage->save();
        foreach($this->htmlFullPage->find('div#content') as $e) {
            
            $text = $e->innertext;
        }
        return $text;
    }

}
