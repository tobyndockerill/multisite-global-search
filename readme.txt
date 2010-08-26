=== Multisite Global Search ===
Contributors: aliciagh
Tags: search, multisite, widget, multilingual, global, shortcode
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: trunk 

Adds the ability to search through blogs into your WordPress Multisite installation. Based on my other plugin WPMU GLobal Search.

== Description ==

Easily search through all blogs into your WordPress Multisite by post title, post content or post author.
Multisite Global Search doesn't work with single WordPress installation and it must be activated for all sites using "network activate" in the Administration Panel.
This plugin is based on my other global search plugin, [WPMU Global Search](http://wordpress.org/extend/plugins/wpmu-global-search). It has some new features but if you want to keep using the older version of Wordpress MU, you can install my other plugin.
Currently in the following languages:

* English
* Spanish (es_ES)

If you have created your own language pack, or have an update of an existing one, you can send [gettext .po and .mo files](http://codex.wordpress.org/Translating_WordPress) to me so that I can bundle it into Multisite Global Search.

== Installation ==

Installation is easy:

1. Upload `multisite-global-search` folder to the `wp-content/plugins` directory in your WordPress multisite installation.
2. Activate the plugin in your Administration Panel.
3. Create a new page in your blog with default global search uri: `globalsearch`.
4. Place `[multisite_search_result]` in the post content area.
5. Activate widget `Multisite Global Search`.

== Frequently Asked Questions ==

If you have any further questions, please submit them.

== Screenshots ==

1. Widget configuration.
2. Vertical Global Search widget.
3. Horizontal Global Search widget.