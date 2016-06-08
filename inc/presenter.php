<?php
namespace MSGlobalSearch {
  /**
   * Singleton Class
   *
   * Handles the presentation of search results.
   */
  final class Presenter {
    private static $instance = null;

    /**
     * Get the instance
     *
     * @return Presenter
     */
    public static function Instance() {
      if (static::$instance === null) {
        static::$instance = new Presenter();
      }
      return static::$instance;
    }

    /**
     * MSGlobalSearch\Presenter Contstructor
     *
     * Note: This is a private method to ensure our class is a singleton class.
     */
    private function __construct() {
      add_shortcode('multisite_search_form', array($this, 'search_form'));
      add_shortcode('multisite_search_result', array($this, 'search_result'));

      // Add style file if it exists
      add_action('wp_print_styles', array($this, 'enqueue_styles'));
    }

    /**
     * Enqueues our custom CSS styles
     */
    public function enqueue_styles() {
      $styleurl = MSGlobalSearch::Instance()->ms_global_search_url . '/views/style.css';
      $styledir = MSGlobalSearch::Instance()->ms_global_search_dir . '/views/style.css';

      if (file_exists($styledir)) {
        wp_enqueue_style('ms_global_search_css_style', $styleurl);
      }
    }

    /**
     * Renders a search form to the page
     *
     * This is called as a result of the [multisite_search_form] shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    public function search_form($atts) {
      global $wp_query, $wpdb;

      // In order to use as much of WordPress's default search form as possible, we attach our
      // own filter to the search form and remove it once it has been rendered to the screen.
      add_filter('get_search_form', array($this, 'search_form_modify'));

      extract(
        shortcode_atts(
          array(
            'type' => 'vertical',
            'page' => __( 'globalsearch', 'ms-global-search' ),
            'search_on_pages' => 0,
            'hide_options' => 0,
            'id_base' => 'ms-global-search',
            'page' => '',
            'search_pages' => '',
            'hide_options' => ''
          ), $atts
        )
      );

      $rand = rand();
      $rand2 = $rand + 1;

      $file = plugin_dir_path(__FILE__) . '../views/search_form.php';
      if (is_file($file)) {
        include($file);
      }

      remove_filter('get_search_form', array($this, 'search_form_modify'));
    }

    /**
     * In the spirit of using WordPress's default search form as much as possible, we use this
     * filter to replace the name of the search input and insert the current search term if present,
     * but leave almost everything else alone.
     *
     * @param string $html
     *
     * @return string
     */
    public function search_form_modify($html) {
      $new_html = str_replace('name="s"', 'name="mssearch"', $html);
      $term = get_query_var('mssearch');
      if ($term !== null) {
        $new_html = str_replace('value=', 'value="' . $term . '"', $new_html);
      }
      return $new_html;
    }

    /**
     * Calls `perform_search` on MSGlobalSearch then renders the results to the screen
     *
     * This is called as a result of the [multisite_search_result] shortcode.
     *
     * @param array $atts
     *
     * @return string
     */
    function search_result($atts) {
      $atts = shortcode_atts(
        array(
          'excerpt' => 'no',
          'featured_images' => 'no',
          'remove_content_images'=>'no'
        ), $atts
      );

      $results = MSGlobalSearch::Instance()->perform_search($atts);

      if ($results) {
        extract($atts);
        extract($results);

        // Show search results.
        $presenter = $this;
        $file = plugin_dir_path(__FILE__) . '../views/search_results.php';
        if (is_file($file)) {
          include($file);
        }
      }
    }

    /**
     * Returns the content of the given post, $s.
     *
     * If the post is password-protected, this returns a form prompting the user to enter their
     * password if they have access.
     *
     * @param post $s
     *
     * @return string
     */
    public function get_the_content($s) {
      $content = $s->post_content;
      switch_to_blog($s->blog_id);
        $content = apply_filters('the_content', $content);
      restore_current_blog();

      $output = '';
      if (post_password_required($s)) {
        $label = 'ms-global-search-' . $s->blog_id . 'pwbox_' . $s->ID;
        $output = '<form action="' . get_blog_option($s->blog_id, 'siteurl') . '/wp-pass.php" method="post">' +
          '<p>' +
          __('This post is password protected. To view it please enter your password below:', 'ms-global-search') +
          '</p>' +
          '<p><label for="' . $label . '">' . __('Password', 'ms-global-search') +
          '<input name="post_password" id="' . $label . '" type="password" size="20" /></label>' +
          '<input type="submit" name="Submit" value="' . __( 'Submit', 'ms-global-search' ) . '" />' +
          '</p>' +
          '</form>';
        return apply_filters('the_password_form', $output);
      }

      return $content;
    }

    /**
     * Returns the excerpt of the given post, $s.
     *
     * If the excerpt has not been specifically entered, it will be automatically generated.
     *
     * If the post is password-protected, this returns a form prompting the user to enter their
     * password if they have access.
     *
     * @param post $s
     *
     * @return string
     */
    public function get_the_excerpt($s) {
      $output = '';
      if (post_password_required($s)) {
        $label = 'ms-global-search-' . $s->blog_id . 'pwbox_' . $s->ID;
        $output = '<form action="' . get_blog_option($s->blog_id, 'siteurl') . '/wp-pass.php" method="post">' +
          '<p>' +
          __('This post is password protected. To view it please enter your password below:', 'ms-global-search') +
          '</p>' +
          '<p><label for="' . $label . '">' . __('Password', 'ms-global-search') +
          '<input name="post_password" id="' . $label . '" type="password" size="20" /></label>' +
          '<input type="submit" name="Submit" value="' . __( 'Submit', 'ms-global-search' ) . '" />' +
          '</p>' +
          '</form>';
        return apply_filters( 'the_password_form', $output );
      }

      $excerpt = $s->post_excerpt;

      if (empty($excerpt)) {
        $raw_excerpt = $excerpt;
        $excerpt = $s->post_content;

        $excerpt = strip_shortcodes($excerpt);
        $excerpt = apply_filters('the_content', $excerpt);
        $excerpt = str_replace(']]>', ']]&gt;', $excerpt);
        $excerpt_length = apply_filters('excerpt_length', 55);
        $excerpt_more = '... ' +
          '<a href="'. get_blog_permalink($s->blog_id, $s->ID). '">' +
          __('(Read more)', 'ms-global-search') +
          '</a>';
        $words = preg_split("/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);

        if (count($words) > $excerpt_length) {
          array_pop($words);
          $excerpt = implode(' ', $words);
          $excerpt = $excerpt . $excerpt_more;
        } else {
          $excerpt = implode(' ', $words);
        }

        return $excerpt;
      } else {
        return apply_filters('get_the_excerpt', $excerpt);
      }
    }

    /**
     * Returns a URL for the thumnail for the given blog_id/post_id.
     *
     * Original concept from
     * http://www.htmlcenter.com/blog/wordpress-multi-site-get-a-featured-image-from-another-blog/
     *
     * @param int $blog_id
     * @param int $post_id
     * @param string $size A size string to be passed to `get_the_post_thumbnail`
     * @param array $attrs An array to be passed to `get_the_post_thumbnail`
     *
     * @return string The URL for the post's thumnail image
     */
    public function get_the_post_thumbnail_by_blog($blog_id=NULL, $post_id=NULL,
      $size='post-thumbnail', $attrs=NULL) {

      global $wpdb;
      global $current_blog;

      $sameblog = false;

      if (empty($blog_id) || $blog_id == $current_blog->ID) {
        $blog_id = $current_blog->ID;
        $sameblog = true;
      }

      if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
      }

      if ($sameblog) {
        return get_the_post_thumbnail($post_id, $size, $attrs);
      }

      if (!$this->has_post_thumbnail_by_blog($blog_id, $post_id)) {
        return false;
      }

      $oldblog = $wpdb->set_blog_id($blog_id);
      $blogdetails = get_blog_details($blog_id);

      $current_blog_url = $current_blog->domain . $current_blog->path;
      $other_blog_url = $blogdetails->domain . $blogdetails->path;
      $thumbcode = str_replace($current_blog_url, $other_blog_url,
        get_the_post_thumbnail($post_id, $size, $attrs));

      // This is needed for multisites for the image urls
      if (is_multisite()) {
        $thumbcode = str_replace('wp-content/uploads', 'files', $thumbcode);
      }

      $wpdb->set_blog_id($oldblog);
      return $thumbcode;
    }

    /**
     * Whether the post has a thumnail.
     * If not given blog_id, it will use the global $current_blog variable.
     * If not given post_id, it will use the global $post variable.
     *
     * @param int $blog_id
     * @param int $post_id
     *
     * @return bool
     */
    function has_post_thumbnail_by_blog($blog_id=NULL, $post_id=NULL) {
      if (empty($blog_id)) {
        global $current_blog;
        $blog_id = $current_blog;
      }

      if (empty($post_id)) {
        global $post;
        $post_id = $post->ID;
      }

      global $wpdb;

      $oldblog = $wpdb->set_blog_id($blog_id);

      $thumbid = has_post_thumbnail($post_id);
      $wpdb->set_blog_id($oldblog);

      return ($thumbid !== false) ? true : false;
    }

    /**
     * Calls the `get_the_post_thumbnail_by_blog` method using the defaults provided.
     *
     * @param int $blog_id
     * @param int $post_id
     * @param string $size
     * @param array $attrs
     *
     * @return string The URL for the post's thumnail image
     */
    function the_post_thumbnail_by_blog($blog_id=NULL, $post_id=NULL, $size='post-thumbnail', $attrs=NULL) {
      echo $this->get_the_post_thumbnail_by_blog($blog_id, $post_id, $size, $attrs);
    }
  }
}
