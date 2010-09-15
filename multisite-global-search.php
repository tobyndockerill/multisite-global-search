<?php
/* 
 * Plugin Name: Multisite Global Search
 * Plugin URI: http://grial.usal.es/agora/pfcgrial/multisite-search
 * Description: Adds the ability to search through blogs into your WordPress Multisite installation. Based on my other plugin WPMU GLobal Search.
 * Version: 1.2.2
 * Requires at least: WordPress 3.0
 * Tested up to: WordPress 3.0.1
 * Author: Alicia García Holgado
 * Author URI: http://grial.usal.es/agora/mambanegra
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
Network: true
*/

/*  Copyright 2010  Alicia García Holgado  ( email : aliciagh@usal.es )

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

if ( !defined( 'MULTISITE' ) || MULTISITE == false ) {
	add_action( 'admin_notices', 'ms_global_search_install_multisite_notice' );
	return;
}

if( !function_exists( 'ms_global_search_install_multisite_notice' ) ) {
	function ms_global_search_install_multisite_notice() {
		echo '<div id="message" class="error fade"><p>';
		_e('<strong>Multisite Global Search</strong></a> requires multisite installation. Please <a href="http://codex.wordpress.org/Create_A_Network">create a network</a> first, or <a href="plugins.php">deactivate Multisite Global Search</a>.', 'ms-global-search' );
		echo '</p></div>';
	}
}

load_plugin_textdomain( 'ms-global-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

$option = get_option( 'permalink_structure' );
if ( empty ( $option ) ) {
	add_action( 'admin_notices', 'ms_global_search_active_widget_notice' );
	return;
}

if( !function_exists( 'ms_global_search_active_widget_notice' ) ) {
	function ms_global_search_active_widget_notice() {
		echo '<div id="message" class="error fade"><p>';
		_e( '<strong>Multisite Global Search Widget</strong></a> not support default permalinks. Please <a target="_blank" href="options-permalink.php">Change Permalinks</a> first.', 'ms-global-search' );
		echo '</p></div>';
	}
}

/**
 * Widget definition.
 */
if( !class_exists( 'Multisite_Global_Search' ) ) {
	class Multisite_Global_Search extends WP_Widget {
		const horizontal = "H";
		const vertical   = "V";
		
		/**
		 * Widget actual processes.
		 */
		function Multisite_Global_Search() {
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'ms-global-search', 'description' => 'Adds the ability to search through blogs into your WordPress 3.0 Multisite installation. Based on my other plugin WPMU GLobal Search.' );
	
			/* Widget control settings. */
			$control_ops = array( 'id_base' => 'ms-global-search' );
	
			/* Create the widget. */
			$this->WP_Widget( 'ms-global-search', $name = __( 'Global Search', 'ms-global-search' ), $widget_ops, $control_ops );
		}
		
		/**
		 * Outputs the options form on admin.
		 */
		function form( $instance ) {
			/* Set up some default widget settings. */
			$defaults = array( 'title' => __( 'Global Search', 'ms-global-search' ), 'page' => __( 'globalsearch', 'ms-global-search' ), 'which_form' => self::vertical );
			$instance = wp_parse_args( ( array ) $instance, $defaults ); ?>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'ms-global-search' ); ?>:</label><br />
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:95%;" />
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e( 'Page', 'ms-global-search' ); ?>:</label><br />
				<input id="<?php echo $this->get_field_id( 'page' ); ?>" name="<?php echo $this->get_field_name( 'page' ); ?>" value="<?php echo $instance['page']; ?>" style="width:95%;" />
			</p>
			
			<p>
		 		<label for="<?php echo $this->get_field_id( 'which_form' ); ?>"><?php _e( 'Form', 'ms-global-search' ); ?>:</label><br />
		 		<input type="radio" id="<?php echo $this->get_field_id( 'which_form' ); ?>" name="<?php echo $this->get_field_name( 'which_form' ); ?>"  value="<?php echo self::horizontal ?>" <?php if( $instance['which_form']!=self::vertical ) echo "checked='checked'";?>>
		 			<?php _e( 'Horizontal', 'ms-global-search' ); ?>
				<input type="radio" id="<?php echo $this->get_field_id( 'which_form' ); ?>" name="<?php echo $this->get_field_name( 'which_form' ); ?>"  value="<?php echo self::vertical ?>" <?php if( $instance['which_form']==self::vertical ) echo "checked='checked'";?>>
					<?php _e( 'Vertical', 'ms-global-search' ); ?>
		 	</p>
			
			<?php
		}
	
		/**
		 * Processes widget options to be saved.
		 */
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
	
			/* Strip tags ( if needed ) and update the widget settings. */
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['page'] = strip_tags( $new_instance['page'] );
			$instance['which_form'] = strip_tags ( $new_instance['which_form'] );
			
			return $instance;
		}
		
		/**
		 * Outputs the content of the widget.
		 */
		function widget( $args, $instance ) {
			extract( $args );
	
			/* User-selected settings. */
			$title = apply_filters( 'widget_title', $instance['title'] );
			$page = $instance['page'];
			
			/* Before widget ( defined by themes ). */
			echo $before_widget;
	
			/* Title of widget ( before and after defined by themes ). */
			if ( $title )
				echo $before_title . $title . $after_title;
			
			if( $instance['which_form'] == self::horizontal )
				$this->ms_global_search_horizontal_form( $page );
			else
				$this->ms_global_search_vertical_form( $page );

			/* After widget ( defined by themes ). */
			echo $after_widget;
			
		}
		
		function ms_global_search_vertical_form( $page ) {
			$rand = rand(); ?>
			<form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'url' ).'/'.$page.'/'; ?>">
				<div>
				    <p><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?></p>
				    <input class="ms-global-search_vbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
				    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' )?>" tabindex="2" />
				    
				    <p>
				    	<input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo $this->id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked'><?php _e( 'All', 'ms-global-search' ); ?>
						<input title="<?php _e( 'Search only on your blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo $this->id_base.'_'.$rand ?>" name="mswhere" value="my"><?php _e( 'My blogs', 'ms-global-search' ); ?>
				    </p>
			    </div>
		    </form>
		<?php
		}
		
		function ms_global_search_horizontal_form( $page ) {
			$rand = rand(); ?>
		    <form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'url' ).'/'.$page.'/'; ?>">
			    <div>
				    <span><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?>&nbsp;</span>
				    <input class="ms-global-search_hbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
				    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' ) ?>" tabindex="2" />

				    <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo $this->id_base.'_'.$rand ?>" name="mswhere" value="all" checked='checked'><?php _e( 'All', 'ms-global-search' ); ?>
					<input title="<?php _e( 'Search only on your blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo $this->id_base.'_'.$rand ?>" name="mswhere" value="my"><?php _e( 'My blogs', 'ms-global-search' ); ?>
			    </div>
		    </form>
		<?php
		}
	}
}

/**
 * Register the Widget.
 */
add_action( 'widgets_init', 'ms_global_search_register' );
if( !function_exists( 'ms_global_search_register' ) ) {
	function ms_global_search_register() {
		register_widget( 'Multisite_Global_Search' );
	}
}

/**
 * Add style file if it exists.
 */
if( !function_exists( 'ms_global_search_style' ) ) {
	function ms_global_search_style() {
		$styleurl = WP_PLUGIN_URL."/".basename( dirname( __FILE__ ) )."/style.css";
		$styledir = WP_PLUGIN_DIR."/".basename( dirname( __FILE__ ) )."/style.css";
		
		if( file_exists( $styledir ) )
			wp_enqueue_style( 'ms_global_search_css_style', $styleurl );
	}
}
add_action( 'wp_print_styles', 'ms_global_search_style' );

/**
 * Init search variables.
 */
add_filter( 'query_vars', 'ms_global_search_queryvars' );
if( !function_exists( 'ms_global_search_queryvars' ) ) {
	function ms_global_search_queryvars( $qvars ) {
	  $qvars[] = 'mssearch';
	  $qvars[] = 'mswhere';
	  return $qvars;
	}
}

/**
 * Shortcodes definition.
 */
if( !function_exists( 'ms_global_search_get_the_content' ) ) {
	function ms_global_search_get_the_content( $s ) {
	    $content = $s->post_content;
	    $content = apply_filters( 'the_content', $content );
	
	    $output = '';
	    if ( post_password_required( $s ) ) {
			$label = 'ms-global-search-'.$s->blog_id.'pwbox_'.$s->ID;
	        $output = '<form action="' . get_blog_option( $s->blog_id, 'siteurl' ) . '/wp-pass.php" method="post">
	        <p>' . __( 'This post is password protected. To view it please enter your password below:', 'ms-global-search' ) . '</p>
	        <p><label for="' . $label . '">' . __( 'Password', 'ms-global-search' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __( 'Submit', 'ms-global-search' ) . '" /></p>
	        </form>
	        ';
	        return apply_filters( 'the_password_form', $output );
		}
	
	    return $content;
	}
}

if( !function_exists( 'ms_global_search_get_the_excerpt' ) ) {
	function ms_global_search_get_the_excerpt( $s ) {
		$output = '';
		if ( post_password_required( $s ) ) {
			$label = 'ms-global-search-'.$s->blog_id.'pwbox_'.$s->ID;
	        $output = '<form action="' . get_blog_option( $s->blog_id, 'siteurl' ) . '/wp-pass.php" method="post">
	        <p>' . __( 'This post is password protected. To view it please enter your password below:', 'ms-global-search' ) . '</p>
	        <p><label for="' . $label . '">' . __( 'Password', 'ms-global-search' ) . ' <input name="post_password" id="' . $label . '" type="password" size="20" /></label> <input type="submit" name="Submit" value="' . __( 'Submit', 'ms-global-search' ) . '" /></p>
	        </form>
	        ';
			return apply_filters( 'the_password_form', $output);
		}
		
		$excerpt = $s->post_excerpt;
		$excerpt = apply_filters( 'get_the_excerpt', $excerpt);
		$excerpt = apply_filters( 'the_excerpt', $excerpt);
		
		$raw_excerpt = $excerpt;
		if ( '' == $excerpt ) {
			$excerpt = $s->post_content;
	
			$excerpt = strip_shortcodes( $excerpt );
	
			$excerpt = apply_filters('the_content', $excerpt);
			$excerpt = str_replace(']]>', ']]&gt;', $excerpt);
			$excerpt_length = apply_filters('excerpt_length', 55);
			$excerpt_more = '... <a href="'. get_blog_permalink( $s->blog_id, $s->ID ). '">' . __( '(Read more)', 'ms-global-search' ) . '</a>';
			$words = preg_split("/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
			if ( count($words) > $excerpt_length ) {
				array_pop($words);
				$excerpt = implode(' ', $words);
				$excerpt = $excerpt . $excerpt_more;
			} else {
				$excerpt = implode(' ', $words);
			}
		}
		return apply_filters('wp_trim_excerpt', $excerpt, $raw_excerpt);
	}
}

if( !function_exists( 'ms_global_search_get_edit_link' ) ) {
	function ms_global_search_get_edit_link( $s, $before = '', $after = '' ) {
	    if ( $s->post_type == 'page' ) {
			if ( !current_user_can( 'edit_page', $s->ID ) ) return;
		} else {
			if ( !current_user_can( 'edit_post', $s->ID ) ) return;
		}
	
	    $context = 'display';
		switch ( $s->post_type ) :
		case 'page' :
			if ( !current_user_can( 'edit_page', $s->ID ) )
				return;
			$file = 'page';
			$var  = 'post';
			break;
		case 'attachment' :
			if ( !current_user_can( 'edit_post', $s->ID ) )
				return;
			$file = 'media';
			$var  = 'attachment_id';
			break;
		case 'revision' :
			if ( !current_user_can( 'edit_post', $s->ID ) )
				return;
			$file	= 'revision';
			$var 	= 'revision';
			$action = '';
			break;
		default :
			if ( !current_user_can( 'edit_post', $s->ID ) )
				return;
			$file = 'post';
			$var  = 'post';
			break;
		endswitch;
	
		$editlink = apply_filters( 'get_edit_post_link', 'http://'.$s->domain.$s->path.'wp-admin/'.$file.'.php?action=edit&amp;'.$var.'='.$s->ID, $s->ID, $context );
	    
	    $link = '<a class="post-edit-link" href="' . $editlink . '" title="' . attribute_escape( __( 'Edit post', 'ms-global-search' ) ) . '">'. __( 'Edit' , 'ms-global-search' ) .'</a>';
		return $before . apply_filters( 'edit_post_link', $link, $s->ID ) . $after;
	}
}

if( !function_exists( 'ms_global_search_get_comments_link' ) ) {
	function ms_global_search_get_comments_link( $s, $css_class = '' ) {
	    global $wpcommentsjavascript, $wpcommentspopupfile;
	
		$number = $s->comment_count;
	
		if ( 0 == $number && 'closed' == $s->comment_status && 'closed' == $s->ping_status ) {
			echo '<span' . ( ( !empty( $css_class ) ) ? ' class="' . $css_class . '"' : '' ) . '>' . __( 'Comments off', 'ms-global-search' ) . '</span>';
			return;
		}
	
		if ( post_password_required() ) {
			echo __( 'Enter your password to view comments', 'ms-global-search' );
			return;
		}
	
		echo '<a href="';
		if ( $wpcommentsjavascript ) {
			if ( empty( $wpcommentspopupfile ) )
				$home = get_blog_option( $s->blog_id, 'home' );
			else
				$home = get_blog_option( $s->blog_id, 'siteurl' );
			echo $home . '/' . $wpcommentspopupfile . '?comments_popup=' . $s->ID;
			echo '" onclick="wpopen( this.href ); return false"';
		} else { // if comments_popup_script() is not in the template, display simple comment link
			if ( 0 == $number )
				echo get_blog_permalink( $s->blog_id, $s->ID ) . '#respond';
			else
				echo get_blog_permalink( $s->blog_id, $s->ID ) . '#comments';
			echo '"';
		}
	
		if ( !empty( $css_class ) ) {
			echo ' class="'.$css_class.'" ';
		}
		$title = attribute_escape( $s->post_title );
	
		echo apply_filters( 'comments_popup_link_attributes', '' );
	
		echo ' title="' . sprintf( __( 'Comment on %s', 'ms-global-search' ), $title ) . '">';
	    if ( $number > 1 )
			$output = str_replace( '%', number_format_i18n( $number ), __( '% Comments', 'ms-global-search' ) );
		elseif ( $number == 0 )
			$output = __( 'No Comments', 'ms-global-search' );
		else // must be one
			$output = __( '1 Comment', 'ms-global-search' );
	
		echo apply_filters( 'comments_number', $output, $number );
		echo '</a>';
	}
}

if( !function_exists( 'ms_global_search_page' ) ) {
	function ms_global_search_page( $atts ) {
		global $wp_query, $wpdb;
		
		extract( shortcode_atts( array( 'excerpt' => 'no' ), $atts ) );
		
		$term = apply_filters( 'get_search_query', get_query_var( 'mssearch' ) );
	
		if( !empty( $term ) ) {
			$wheresearch = '';
			/* Search only on user blogs. */
			$userid = get_current_user_id();
			if( strcmp( apply_filters ( 'get_search_query', get_query_var( 'mswhere' ) ), 'my' ) == 0 && $userid != 0 ) {
				$userblogs = get_blogs_of_user( $userid );
				
				$i=0;
				foreach( $userblogs as $ub ) {
					if( $i != 0 ) $wheresearch .= " OR ";
					else $wheresearch .= "( ";
					$i++;
					$wheresearch .= $wpdb->base_prefix."v_posts.blog_id = ".$ub->userblog_id;
					if( count( $userblogs ) == $i ) $wheresearch .= " ) AND ";
				}
			}
			
			$request = $wpdb->prepare( "SELECT ".$wpdb->base_prefix."v_posts.* from ".$wpdb->base_prefix."v_posts left join ".$wpdb->users." on ".$wpdb->users.".ID=".$wpdb->base_prefix."v_posts.post_author ".
			"where ".$wheresearch." ( post_title LIKE '%%".$term."%%' OR post_content LIKE '%%".$term."%%' OR ".$wpdb->users.".display_name LIKE '%%".$term."%%' )".
		    "AND ( post_status = 'publish' OR post_status = 'private' ) AND post_type = 'post' ORDER BY ".$wpdb->base_prefix."v_posts.blog_id ASC, ".$wpdb->base_prefix."v_posts.post_date DESC, ".$wpdb->base_prefix."v_posts.comment_count DESC" );
	
			$search = $wpdb->get_results( $request );
	
			if( empty( $search ) ) { ?>
				<h3 class='globalpage_title center'><?php _e( "Not found", 'ms-global-search' ) ?> <span class='ms-global-search_term'><?php echo $term; ?></span><?php if( !empty( $wheresearch ) ) echo " ".__( 'in your blogs', 'ms-global-search' ); ?>.</h3>
				<p class='globalpage_message center'><?php _e( "Sorry, but you are looking for something that isn't here.", 'ms-global-search' ) ?></p>
			<?php
	        } else { ?>
	        	<p><?php echo count( $search )." ".__( 'matches with', 'ms-global-search' ) ?> <span class='ms-global-search_term'><?php echo $term; ?></span><?php if( !empty( $wheresearch ) ) echo " ".__( 'in your blogs', 'ms-global-search' ); ?>.</p>
	        <?php
	            $blogid = '';
	            foreach( $search as $s ) {
	                $author = get_userdata( $s->post_author );
	                if( $blogid != $s->blog_id ) {
	                    $blogid = $s->blog_id; ?>
	                    
	                    <h2 class='globalblog_title'><?php echo get_blog_option( $blogid, 'blogname' ) ?></h2>
	                <?php
	                } ?>
	
	                <div <?php post_class( 'globalsearch_post' ) ?>>
	                	<div class="globalsearch_header">
	                    	<h2 id="post-<?php echo $s->ID.$s->blog_id; ?>" class="globalsearch_title"><a href="<?php echo get_blog_permalink( $s->blog_id, $s->ID ); ?>" rel="bookmark" title="<?php echo __( 'Permanent Link to', 'ms-global-search' ).' '.$s->post_title; ?>"><?php echo $s->post_title ?></a></h2>
	                    	<p class="globalsearch_meta">
								<span class="globalsearch_comment"><?php ms_global_search_get_comments_link( $s ); ?></span>
								<span class="globalsearch_date"><?php echo date( __( 'j/m/y, G:i', 'ms-global-search' ) ,strtotime( $s->post_date ) ); ?></span>
								<span class="globalsearch_author"><?php echo '<a href="http://' . $s->domain.$s->path.'author/'.$author->user_nicename . '" title="' . $author->user_nicename . '">' . $author->user_nicename . '</a>'; ?></span>
								<?php echo ms_global_search_get_edit_link( $s, '<span class="globalsearch_edit">', '</span>' ); ?>
							</p>
						</div>
						
						<div class="globalsearch_content">
	                    	<div class="entry">
	                    		<?php
	                    		if(strcmp($excerpt, "yes") == 0)
	                    			echo ms_global_search_get_the_excerpt( $s );
	                        	else
	                        		echo ms_global_search_get_the_content( $s ); ?>
	                    	</div>
						</div>
	                </div>
	            <?php
	            }
	        }
	    } else { ?>
		    <h3 class='globalpage_title center'><?php _e( "Not found", 'ms-global-search' ) ?></h3>
	        <p class='globalpage_message center'><?php _e( "Sorry, but you are looking for something that isn't here.", 'ms-global-search' ) ?></p>
	    <?php
	    }
	}
}
add_shortcode( 'multisite_search_result', 'ms_global_search_page' );

if( !function_exists( 'ms_global_search_form' ) ) {
	function ms_global_search_form( $atts ) {
		global $wp_query, $wpdb;
		
		extract( shortcode_atts( array( 'type' => 'vertical', 'page' => __( 'globalsearch', 'ms-global-search' ) ), $atts ) );
		
		$rand = rand();
		if( strcmp( $type, 'horizontal' ) == 0 ) { ?>
			<form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'url' ).'/'.$page.'/'; ?>">
			    <div>
				    <span><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?>&nbsp;</span>
				    <input class="ms-global-search_hbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
				    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' ) ?>" tabindex="2" />
	
				    <input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="all" checked='checked'><?php _e( 'All', 'ms-global-search' ); ?>
					<input title="<?php _e( 'Search only on your blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="my"><?php _e( 'My blogs', 'ms-global-search' ); ?>
			    </div>
		    </form>
		<?php
		} else { ?>
			<form class="ms-global-search_form" method="get" action="<?php echo get_bloginfo( 'url' ).'/'.$page.'/'; ?>">
				<div>
				    <p><?php _e( 'Search across all blogs:', 'ms-global-search' ) ?></p>
				    <input class="ms-global-search_vbox" name="mssearch" type="text" value="" size="16" tabindex="1" />
				    <input type="submit" class="button" value="<?php _e( 'Search', 'ms-global-search' )?>" tabindex="2" />
				    
				    <p>
				    	<input title="<?php _e( 'Search on all blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="all" checked='checked'><?php _e( 'All', 'ms-global-search' ); ?>
						<input title="<?php _e( 'Search only on your blogs', 'ms-global-search' ); ?>" type="radio" id="<?php echo "ms-global-search-form-".$rand ?>" name="mswhere" value="my"><?php _e( 'My blogs', 'ms-global-search' ); ?>
				    </p>
			    </div>
			</form>
		<?php
		}
	}
}
add_shortcode( 'multisite_search_form', 'ms_global_search_form' );

/**
 * Builds a view that contains posts from all blogs.
 * Views are built by activate_blog, desactivate_blog, archive_blog, unarchive_blog, delete_blog and wpmu_new_blog hooks.
 */
add_action ( 'wpmu_new_blog', 'ms_global_search_build_views_add' );
add_action ( 'delete_blog', 'ms_global_search_build_views_drop', 10, 1 );
add_action ( 'archive_blog', 'ms_global_search_build_views_drop', 10, 1 );
add_action ( 'unarchive_blog', 'ms_global_search_build_views_unarchive', 10, 1 );
add_action ( 'activate_blog', 'ms_global_search_build_views_activate', 10, 1 );
add_action ( 'deactivate_blog', 'ms_global_search_build_views_drop', 10, 1 );

register_activation_hook( __FILE__, 'ms_global_search_build_views_add' );
register_deactivation_hook( __FILE__, 'ms_global_search_drop_views');

if( !function_exists( 'ms_global_search_build_views_drop' ) ) {
	function ms_global_search_build_views_drop( $trigger ) {
	    global $wpdb;
	
	    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE blog_id != {$trigger} AND site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC" ) );
	    ms_global_search_v_query( $blogs );
	}
}

if( !function_exists( 'ms_global_search_build_views_add' ) ) {
	function ms_global_search_build_views_add() {
	    global $wpdb;
	
	    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ORDER BY registered DESC" ) );
	    ms_global_search_v_query( $blogs );
	}
}

if( !function_exists( 'ms_global_search_build_views_activate' ) ) {
	function ms_global_search_build_views_activate( $trigger ) {
	    global $wpdb;
	
	    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE ( blog_id = {$trigger} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' ) OR ( site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ) ORDER BY registered DESC" ) );
	    
	    ms_global_search_v_query( $blogs );
	}
}

if( !function_exists( 'ms_global_search_build_views_unarchive' ) ) {
	function ms_global_search_build_views_unarchive( $trigger ) {
	    global $wpdb;
	
	    $blogs = $wpdb->get_results( $wpdb->prepare( "SELECT blog_id, domain, path FROM {$wpdb->blogs} WHERE ( blog_id = {$trigger} AND public = '1' AND deleted = '0' AND mature = '0' AND spam = '0' ) OR ( site_id = {$wpdb->siteid} AND public = '1' AND archived = '0' AND mature = '0' AND spam = '0' AND deleted = '0' ) ORDER BY registered DESC" ) );
	    ms_global_search_v_query( $blogs );
	}
}

if( !function_exists( 'ms_global_search_v_query' ) ) {
	function ms_global_search_v_query( $blogs ) {
	    global $wpdb;
	
	    $i = 0;
	    $posts_select_query = '';
	    $postmeta_select_query = '';
	    $comments_select_query = '';
	    foreach ( $blogs as $blog ) {
	        if ( $i != 0 ) {
	            $posts_select_query    .= ' UNION ';
	            $postmeta_select_query .= ' UNION ';
	            $comments_select_query .= ' UNION ';
	        }
	        
	        $posts_select_query    .= " ( SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, posts{$blog->blog_id}.* FROM {$wpdb->get_blog_prefix( $blog->blog_id )}posts posts{$blog->blog_id} WHERE posts{$blog->blog_id}.post_type != 'revision' AND posts{$blog->blog_id}.post_status = 'publish' ) ";
	        $postmeta_select_query .= " ( SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, postmeta{$blog->blog_id}.* FROM {$wpdb->get_blog_prefix( $blog->blog_id )}postmeta postmeta{$blog->blog_id} ) ";
	        $comments_select_query .= " ( SELECT '{$blog->blog_id}' AS blog_id, '{$blog->domain}' AS domain, '{$blog->path}' AS path, comments{$blog->blog_id}.* FROM {$wpdb->get_blog_prefix( $blog->blog_id )}comments comments{$blog->blog_id} ) ";
	        
	        $i++;
	    }
	    
	
	    if( $blogs != null ) {
		    $v_query1  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_posts` AS ".$posts_select_query;
			if ( $wpdb->query( $wpdb->prepare( $v_query1 ) ) === false ) {
				$wpdb->print_error();
				wp_die( __( 'Error creating search views in the database. <a href="plugins.php">Deactivate Multisite Global Search</a> and check you have create views privilege in your WordPress database.', 'ms-global-search' ).'<br />'. $wpdb->last_error );
			}
			
			$v_query2  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_postmeta` AS ".$postmeta_select_query;
			if ( $wpdb->query( $wpdb->prepare( $v_query2 ) ) === false ) {
				$wpdb->print_error();
				wp_die( __( 'Error creating search views in the database. <a href="plugins.php">Deactivate Multisite Global Search</a> and check you have create views privilege in your WordPress database.', 'ms-global-search' ).'<br />'. $wpdb->last_error );
			}
			
			$v_query3  = "CREATE OR REPLACE VIEW `{$wpdb->base_prefix}v_comments` AS ".$comments_select_query;
			if ( $wpdb->query( $wpdb->prepare( $v_query3 ) ) === false ) {
				$wpdb->print_error();
				wp_die( __( 'Error creating search views in the database. <a href="plugins.php">Deactivate Multisite Global Search</a> and check you have create views privilege in your WordPress database.', 'ms-global-search' ).'<br />'. $wpdb->last_error );
			}
	    } else {
	    	wp_die( __( '<strong>Multisite Global Search</strong></a> requires multisite installation. Please <a href="http://codex.wordpress.org/Create_A_Network">create a network</a> first, or <a href="plugins.php">deactivate Multisite Global Search</a>.', 'ms-global-search' ) );
	    }
	}
}

if( !function_exists( 'ms_global_search_drop_views' ) ) {
	function ms_global_search_drop_views() {
		global $wpdb;
		
		$wpdb->query( $wpdb->prepare( "DROP VIEW `{$wpdb->base_prefix}v_posts`" ) );
		$wpdb->query( $wpdb->prepare( "DROP VIEW `{$wpdb->base_prefix}v_postmeta`" ) );
		$wpdb->query( $wpdb->prepare( "DROP VIEW `{$wpdb->base_prefix}v_comments`" ) );
	}
}

?>
