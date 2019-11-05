<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?>"<?php print $attributes; ?>>

  <?php print $user_picture; ?>

  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <div style="float:left;margin-right:10px;">
    <?php
    
        $imagePath = $node->news_item_story_image_url['und'][0]['value'];
        
        if (empty($imagePath)) {
            $imagePath = drupal_get_path('module', 'news_item') . "/public/images/icon-news.gif";
        }
        // irfan - start
        print theme('image', array(
            //This sets the image path
            'path' => $imagePath,
            'width' => '76',
            'height' => '60',
            //This is necessary to cancel getsize method, which is only for local images
            'getsize' => false)
            );
        // irfan - end
    ?>
    </div>
    <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="submitted"><?php print $submitted ?></div>
  <?php endif; ?>

  <div class="content clearfix"<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
      print render($content);
    ?>
  </div>

  <div class="clearfix">
    <?php if (!empty($content['links'])): ?>
      <div class="links"><?php print render($content['links']); ?></div>
    <?php endif; ?>

    <?php print render($content['comments']); ?>
  </div>

</div>