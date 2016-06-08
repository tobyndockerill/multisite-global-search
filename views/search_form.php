<?php
  $vertical = $type == MSGlobalSearch\SearchWidget::vertical;

  $msp = get_query_var('msp');
  if ($msp !== null) {
    $search_pages = $search_pages || $msp;
  }

  $search_my = false;
  $mswhere = get_query_var('mswhere');
  if ($mswhere !== null) {
    $search_my = $mswhere == 'my';
  }
?>

<form class="ms-global-search_form ms-global-search_vbox" method="get" role="search"
  action="<?php echo home_url() . '/' . $page . '/'; ?>">
  <div>
    <p class="search-form">
      <? get_search_form() ?>
    </p>

    <?php if ($hide_options) : ?>
      <input title="<?php _e('Search on pages', 'ms-global-search'); ?>" type="hidden"
        id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1" checked="checked" />
      <input title="<?php _e('Search on all blogs', 'ms-global-search'); ?>" type="hidden"
        id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked' />
    <?php else : ?>
      <?php if ($vertical) : ?>
        <p style="<?php echo $search_pages ? '"display: none"' : ''?>">
      <?php endif; ?>
        <label>
          <input title="<?php _e('Search on pages', 'ms-global-search'); ?>" type="checkbox"
            id="<?php echo $id_base.'_'.$rand2 ?>" name="msp" value="1"
            <?php echo $search_pages ? 'checked="checked"' : ''; ?> />

          <?php _e('Search on pages', 'ms-global-search'); ?>
        </label>
      <?php if ($vertical) : ?>
        </p>
      <?php endif; ?>

      <?php if ( get_current_user_id() != 0 ) : ?>
        <?php if ($vertical) : ?>
          <p>
        <?php endif; ?>
          <label>
            <input title="<?php _e('Search on all blogs', 'ms-global-search'); ?>" type="radio"
            id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="all"
            <?php echo $search_my ? '' : 'checked="checked"' ?> />
            <?php _e('All', 'ms-global-search'); ?>
          </label>
          <label>
            <input title="<?php _e("Search only on blogs where I'm a member", 'ms-global-search'); ?>"
              type="radio" id="<?php echo $id_base.'_'.$rand ?>" name="mswhere" value="my"
              <?php echo $search_my ? 'checked="checked"' : '' ?> />
            <?php _e('All', 'ms-global-search'); ?>
            <?php _e("Blogs where I'm a member", 'ms-global-search'); ?>
          </label>
        <?php if ($vertical) : ?>
          </p>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</form>

