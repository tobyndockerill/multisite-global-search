<p>
  <label for="<?php echo $this->get_field_id('title'); ?>">
    <?php _e('Title', 'ms-global-search'); ?>:
  </label>
  <br />
  <input id="<?php echo $this->get_field_id('title'); ?>"
    name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"
    style="width: 95%;" />
</p>

<p>
  <label for="<?php echo $this->get_field_id('page'); ?>">
    <?php _e( 'Page', 'ms-global-search' ); ?>:
  </label>
  <br />
  <input id="<?php echo $this->get_field_id('page'); ?>"
    name="<?php echo $this->get_field_name('page'); ?>" value="<?php echo $instance['page']; ?>"
    style="width: 95%;" />
</p>

<p>
  <label for="<?php echo $this->get_field_id('which_form'); ?>">
    <?php _e('Form', 'ms-global-search'); ?>:
  </label>
  <br />
  <label>
    <input type="radio" id="<?php echo $this->get_field_id('which_form'); ?>"
      name="<?php echo $this->get_field_name('which_form'); ?>" value="<?php echo self::horizontal ?>"
      <?php if ($instance['which_form']!=self::vertical) echo "checked='checked'";?> />
    <?php _e('Horizontal', 'ms-global-search'); ?>
  </label>

  <label>
    <input type="radio" id="<?php echo $this->get_field_id('which_form'); ?>"
      name="<?php echo $this->get_field_name('which_form'); ?>" value="<?php echo self::vertical ?>"
      <?php if ($instance['which_form'] == self::vertical) echo "checked='checked'";?> />
    <?php _e('Vertical', 'ms-global-search'); ?>
  </label>
</p>

<p>
  <label>
    <input type="checkbox" id="<?php echo $this->get_field_id('search_pages'); ?>"
      name="<?php echo $this->get_field_name('search_pages'); ?>" value="1"
      <?php if ($instance['search_pages']) echo "checked='checked'"; ?> />
    <?php _e('Searching by default on pages', 'ms-global-search'); ?>
  </label>
</p>

<p>
  <label>
    <input type="checkbox" id="<?php echo $this->get_field_id('hide_options'); ?>"
      name="<?php echo $this->get_field_name('hide_options'); ?>" value="1"
      <?php if ($instance['hide_options']) echo "checked='checked'"; ?> />
    <?php _e('Disable search options', 'ms-global-search'); ?>
  </label>
</p>
