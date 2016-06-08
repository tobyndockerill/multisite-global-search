<?php if (empty($search)) : ?>
  <h3 class='globalpage_title center'>
    <?php _e('Not found', 'ms-global-search') ?>
    <span class='ms-global-search_term'>
      <?php echo stripslashes($term); ?>
    </span>
    <?php if (!empty($wheresearch)) : ?>
      <?php echo ' ' .__('in your blogs', 'ms-global-search' ); ?>.
    <?php endif; ?>
  </h3>

  <p class='globalpage_message center'>
    <?php _e("Sorry, but you are looking for something that isn't here.", 'ms-global-search') ?>
  </p>
<?php else : ?>
  <?php $countResult = count($search); ?>

  <p>
    <?php if ($countResult < 2) : ?>
      <?php echo $countResult . ' ' . __('match with', 'ms-global-search') ?>
    <?php else : ?>
      <?php echo $countResult . ' ' . __('matches with', 'ms-global-search') ?>
    <?php endif; ?>
    <span class='ms-global-search_term'><?php echo stripslashes($term); ?></span>
    <?php if (!empty($wheresearch)) : ?>
      <?php echo ' ' .__( 'in your blogs', 'ms-global-search' ); ?>
    <?php endif; ?>
  </p>

  <?php $blogid = ''; ?>
  <?php foreach ($search as $s) : ?>
    <?php if ($blogid != $s->blog_id) : ?>
      <?php $blogid = $s->blog_id; ?>
      <h2 class='globalblog_title'><?php echo get_blog_option($blogid, 'blogname') ?></h2>
    <?php endif; ?>

    <div <?php post_class('globalsearch_post') ?>>
      <div class="globalsearch_header">
        <h2 id="post-<?php echo $s->ID . $s->blog_id; ?>" class="globalsearch_title">
          <a href="<?php echo get_blog_permalink( $s->blog_id, $s->ID ); ?>" rel="bookmark"
            title="<?php echo __('Permanent Link to', 'ms-global-search') . ' ' . $s->post_title; ?>">
            <?php echo $s->post_title ?>
          </a>
        </h2>
        <p class="globalsearch_meta">
          <span class="globalsearch_date">
            <?php echo date(__( 'j/m/y, G:i', 'ms-global-search') , strtotime($s->post_date)); ?>
          </span>
        </p>
      </div>

      <div class="globalsearch_content">
        <?php if (strcmp($featured_images, 'yes') == 0) : ?>
          <div class="entry_thumbnail">
            <?php echo $presenter->get_the_post_thumbnail_by_blog($s->blog_id, $s->ID, 'thumbnail'); ?>
          </div>
        <?php endif; ?>
        <div class="entry">
          <?php
            if (strcmp($excerpt, 'yes') == 0) {
              $excerpt = $presenter->get_the_excerpt($s);
            } else {
              $excerpt = $presenter->get_the_content($s);
            }

            if (strcmp($remove_content_images, 'yes') == 0) {
              $excerpt = preg_replace("/<img[^>]+\>/i", '', $excerpt);
            }
          ?>
          <?php echo $excerpt; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
