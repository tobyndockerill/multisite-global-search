<?php
/*
 * Plugin Name: Multisite Global Search
 * Plugin URI: https://github.com/tobyndockerill/multisite-global-search
 * Description: Adds the ability to search through blogs into your WordPress Multisite installation.
 * Version: 2.0.0
 * Tested up to: WordPress 4.5.2
 * Author: Alicia García Holgado
 * Author URI: http://grial.usal.es/agora/mambanegra
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Network: true
*/

/*
  Copyright 2010  Alicia García Holgado  ( email : aliciagh@usal.es )

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace MSGlobalSearch {
  require_once(plugin_dir_path(__FILE__) . 'inc/search_widget.php');
  require_once(plugin_dir_path(__FILE__) . 'inc/presenter.php');
  require_once(plugin_dir_path(__FILE__) . 'inc/db_manager.php');

  /**
   * Singleton Class
   *
   * Provides the initial setup for Multisite Global Search and surfaces the perform_search
   * action.
   */
  final class MSGlobalSearch {
    private static $instance = null;

    public $ms_global_search_url;
    public $ms_global_search_dir;

    /**
     * Get the instance
     *
     * @return MSGlobalSearch
     */
    public static function Instance() {
      if (static::$instance === null) {
        static::$instance = new MSGlobalSearch();
      }
      return static::$instance;
    }

    /**
     * MSGlobalSearch\MSGlobalSearch Contstructor
     *
     * Note: This is a private method to ensure our class is a singleton class.
     */
    private function __construct() {
      // Initialise the Database Manager
      DBManager::Instance();

      // Initialise the Presenter
      Presenter::Instance();

      $this->ms_global_search_url = plugins_url('', __FILE__);
      $this->ms_global_search_dir = dirname(__FILE__);

      // Add and Drop the views when the plugin is activated and deactivated
      register_activation_hook(__FILE__, array(DBManager::Instance(), 'add'));
      register_deactivation_hook(__FILE__, array(DBManager::Instance(), 'drop_all'));

      // Begin the initialisation when the plugins are loaded
      add_action('plugins_loaded', array($this, 'init'));
    }

    /**
     * Begins the initialisation for Multisite Global Search
     *
     * - ensures the installation of WordPress is multisite
     * - ensures that the permalink structure is not the default
     * - initialises the query variables
     * - registers the search widget
     */
    public function init() {
      if (!is_multisite()) {
        add_action('admin_notices', array($this, 'notice_multisite_install'));
        return;
      }

      // Multisite Global Search does not support the default permalinks
      $permalink_structure = get_option('permalink_structure');
      if (empty($permalink_structure)) {
        add_action('admin_notices', array($this, 'notice_permalink'));
        return;
      }

      // Initialise search variables
      add_filter('query_vars', array($this, 'init_queryvars'));

      load_plugin_textdomain('ms-global-search', false, dirname(plugin_basename(__FILE__)) . '/languages');

      add_action('widgets_init', function() {
        register_widget('MSGlobalSearch\SearchWidget');
      });
    }

    /**
     * Returns an admin notice with a message stating a multisite installation is required
     *
     * @return string
     */
    public function notice_multisite_install() {
      echo '<div id="message" class="error fade"><p>';
      _e('<strong>Multisite Global Search</strong></a> requires multisite installation.
        Please <a href="http://codex.wordpress.org/Create_A_Network">create a network</a> first,
        or <a href="plugins.php">deactivate Multisite Global Search</a>.', 'ms-global-search' );
      echo '</p></div>';
    }

    /**
     * Returns an admin notice with a message stating default permalinks are not supported
     *
     * @return string
     */
    public function notice_permalink() {
      echo '<div id="message" class="error fade"><p>';
      _e( '<strong>Multisite Global Search Widget</strong></a> not support default permalinks.
        Please <a target="_blank" href="options-permalink.php">Change Permalinks</a> first.',
        'ms-global-search' );
      echo '</p></div>';
    }

    /**
     * Returns the default query variables
     *
     * @param array $qvars
     *
     * @return array
     */
    public function init_queryvars($qvars) {
      $qvars[] = 'mssearch';
      $qvars[] = 'mswhere';
      $qvars[] = 'msp';

      return $qvars;
    }

    /**
     * Performs a search based on the provided attributes and the current `mssearch` query variable.
     *
     * @param array $atts
     *
     * @return array|null If the value of `mssearch` is empty, null. Otherwise, this function
     *  returns an array containing an array of search results, and the values of $wheresearch
     *  and $term.
     */
    public function perform_search($atts) {
      global $wp_query, $wpdb;

      extract($atts);

      $term = strip_tags(apply_filters('get_search_query', get_query_var('mssearch')));

      $sql_variables = array();

      $results = null;

      if (!empty($term)) {
        // Literal keyword
        if (preg_match('/^\"(.*?)\"$/', stripslashes($term) , $termmatch)) {
          if (!empty($termmatch[1])) {
            $termsearch = "(
              post_title LIKE '%%%s%%' OR post_content LIKE '%%%s%%' OR {$wpdb->users}.display_name LIKE '%%%s%%'
            )";

            $sql_variables[] = $termmatch[1];
            $sql_variables[] = $termmatch[1];
            $sql_variables[] = $termmatch[1];
          }
        } else {
          // Multiple keywords
          $multipleterms = explode(' ', $term);
          if (count($multipleterms) != 1) {
            $termsearch = '( ';
            $totalterms = count($multipleterms);
            $numterms = 1;
            foreach ($multipleterms as $aterm) {
              $termsearch .= "(post_title LIKE '%%%s%%' OR post_content LIKE '%%%s%%' ";

              if ($numterms < $totalterms) {
                $termsearch .= "OR {$wpdb->users}.display_name LIKE '%%%s%%') AND ";
              } else {
                $termsearch .= "OR {$wpdb->users}.display_name LIKE '%%%s%%'))";
              }

              $sql_variables[] = $aterm;
              $sql_variables[] = $aterm;
              $sql_variables[] = $aterm;

              $numterms++;
            }
          } else {
            $termsearch = "(
              post_title LIKE '%%%s%%' OR post_content LIKE '%%%s%%' OR {$wpdb->users}.display_name LIKE '%%%s%%'
            ) ";

            $sql_variables[] = $term;
            $sql_variables[] = $term;
            $sql_variables[] = $term;
          }
        }

        $wheresearch = '';
        // Search only on user blogs.
        $userid = get_current_user_id();
        if (strcmp(apply_filters('get_search_query', get_query_var('mswhere')), 'my') == 0 && $userid != 0 ) {
          $userblogs = get_blogs_of_user($userid);

          $i = 0;
          foreach ($userblogs as $ub) {
            if ($i != 0) {
              $wheresearch .= ' OR ';
            } else {
              $wheresearch .= '( ';
            }
            $i++;
            $wheresearch .= "{$wpdb->base_prefix}v_posts.blog_id = {$ub->userblog_id}";
            if (count($userblogs) == $i) {
              $wheresearch .= ' ) AND ';
            }
          }
        }

        // Search on pages.
        if (get_query_var('msp')) {
          $post_type = "(post_type = 'post' OR post_type = 'page' OR post_type = 'syndication')";
        } else {
          $post_type = "(post_type = 'post' OR post_type = 'syndication')";
        }

        // Show private posts
        $request_sql  = "SELECT {$wpdb->base_prefix}v_posts.* ";
        $request_sql .= "FROM {$wpdb->base_prefix}v_posts ";
        $request_sql .= "LEFT JOIN {$wpdb->users} ";
        $request_sql .= "ON {$wpdb->users}.ID={$wpdb->base_prefix}v_posts.post_author ";
        $request_sql .= "WHERE {$wheresearch} {$termsearch} ";
        if ($userid != 0) {
          $request_sql .= "AND (post_status = 'publish' OR post_status = 'private') ";
        } else {
          $request_sql .= "AND (post_status = 'publish') ";
        }
        $request_sql .= "AND {$post_type} ";
        $request_sql .= "ORDER BY {$wpdb->base_prefix}v_posts.blog_id ASC, ";
        $request_sql .= "{$wpdb->base_prefix}v_posts.post_date DESC, ";
        $request_sql .= "{$wpdb->base_prefix}v_posts.comment_count DESC";

        $request = $wpdb->prepare($request_sql, $sql_variables);

        $results['search'] = $wpdb->get_results( $request );
        $results['wheresearch'] = $wheresearch;
        $results['term'] = $term;
      }

      return $results;
    }
  }

  MSGlobalSearch::Instance();
}
