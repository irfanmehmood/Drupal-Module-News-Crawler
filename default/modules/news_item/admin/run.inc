<?php





function news_item_run_manual() {
    
     /*
    print_r("<pre>");

    $categoryVocabulary = taxonomy_vocabulary_machine_name_load(TAXONOMY_VOCABULARY_CATEGORIES);
    $tree = taxonomy_get_tree($categoryVocabulary->vid);
    foreach ($tree as $term) {
        //var_dump(taxonomy_get_children($term->tid, $vid = $categoryVocabulary->vid));
        if ($term->parents[0] == 0) {
            //var_dump(taxonomy_get_children($term->tid, $vid = $categoryVocabulary->vid));
            $childTerms = taxonomy_get_children($term->tid, $categoryVocabulary->vid);
            foreach ($childTerms as $child) {
                var_dump($child->name);
            }
        }
       //echo count(taxonomy_get_children($term->tid, $vid = $categoryVocabulary->vid));
       
    }
    
    
     
   
    $vocabulary_category_term_science_technology = taxonomy_get_term_by_name('technology', 'news_channel_categories');
        */
    
  require_once ( dirname(__FILE__) . '/scripts/schedule.php');   
  $schecdule = new CSchedule();
  $schecdule->start_importing_news();
  drupal_set_message("News Items - Imported");
    
  $form['#submit'][] = 'news_feeder_run_manual_submit';
  return system_settings_form($form);
     
}
?>