<?php
namespace MSGlobalSearch {
  /**
   * Create a widget that outputs the Promoted RSS Feed.
   */
  class SearchWidget extends \WP_Widget {
    const horizontal = 'H';
    const vertical   = 'V';

    /**
     * Sets up the details of the widget
     */
    public function __construct() {
      $widget_ops = array(
        'classname' => 'ms-global-search-widget',
        'description' => 'Adds the ability to search blogs in your WordPress Multisite installation.'
      );

      // Widget control settings
      $control_ops = array(
        'id_base' => 'ms-global-search'
      );

      // Create the widget
      parent::__construct( 'ms-global-search', $name = __( 'Global Search', 'ms-global-search' ),
        $widget_ops, $control_ops);
    }

    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param SearchWidget $instance
     *
     * @return string
     */
    public function widget($args, $instance) {
      extract($args);

      // User-selected settings
      $title = apply_filters('widget_title', $instance['title']);
      $page = $instance['page'];
      $search_pages = $instance['search_pages'];
      $hide_options = $instance['hide_options'];

      // Before widget (defined by theme)
      echo $args['before_widget'];

      // Title of widget (before and after defined by theme)
      if ($title) {
        echo $args['before_title'] . $title . $args['after_title'];
      }

      $this->render_search_form($page, $search_pages, $hide_options, $instance['which_form']);

      // After widget (defined by theme)
      echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     *
     * @param SearchWidget $instance
     *
     * @return string
     */
    public function form($instance) {
      // Set up some default widget settings
      $defaults = array(
        'title' => __('Global Search', 'ms-global-search'),
        'page' => __('globalsearch', 'ms-global-search'),
        'which_form' => self::vertical,
        'search_pages' => 0,
        'hide_options' => 0
      );
      $instance = wp_parse_args((array) $instance, $defaults);

      $file = plugin_dir_path(__FILE__) . '../views/widget_form.php';
      if (is_file($file)) {
        include($file);
      }
    }

    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     *
     * @return SearchWidget $instance
     */
    public function update($new_instance, $old_instance) {
      $instance = $old_instance;

      // Strip tags (if needed) and update the widget settings
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['page'] = strip_tags($new_instance['page']);
      $instance['which_form'] = strip_tags($new_instance['which_form']);
      $instance['search_pages'] = strip_tags($new_instance['search_pages']);
      $instance['hide_options'] = strip_tags($new_instance['hide_options']);

      return $instance;
    }

    /**
     * Calls the search_form method on the Presenter.
     *
     * @param string $page The slug that contains the search shortcode
     * @param int $search_pages Whether to search pages or just posts (0 or 1)
     * @param int $hide_options Whether to hide the options (0 or 1)
     * @param string $type The type of form to display ('horizontal' or 'vertical')
     *
     * @return string
     */
    private function render_search_form($page, $search_pages, $hide_options, $type) {
      if (isset($this)) {
        $id_base = $this->id_base;
      } else {
        $id_base = 'ms-global-search';
      }

      Presenter::Instance()->search_form(
        array(
          'id_base' => $id_base,
          'type' => $type,
          'page' => $page,
          'search_pages' => $search_pages,
          'hide_options' => $hide_options
        )
      );
    }
  }
}
