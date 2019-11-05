<?php


define('MODULE_CONTENT_TYPE', 'news_item');


function news_item_delete_news_items() {
    
    drupal_set_message("News Feeder - Items Deleted");
    
    // Gather all the example content that might have been created while this
    // module was enabled.
    $sql = 'SELECT nid FROM {node} n WHERE n.type = :type';
    $result = db_query($sql, array(':type' => MODULE_CONTENT_TYPE));
    $nids = array();
    
    foreach ($result as $row) {
        $nids[] = $row->nid;
    }
    
    // Delete all the nodes at once
    if (count($nids) > 0) {
        node_delete_multiple($nids);
    }
}
?>