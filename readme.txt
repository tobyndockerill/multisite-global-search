=== Multisite Global Search ===
Contributors: aliciagh
Tags: search, multisite, buddypress, widget, multilingual, global, shortcode
Requires at least: 3.0
Tested up to: 3.0.1
Stable tag: 1.2.1

Adds the ability to search through blogs into your WordPress Multisite installation. Based on my other plugin WPMU GLobal Search.

== Description ==

Easily search through all blogs into your WordPress Multisite by post title, post content or post author.
Multisite Global Search doesn't work with single WordPress installation and it must be activated for all sites using "network activate" in the Administration Panel.
This plugin is based on my other global search plugin, [WPMU Global Search](http://wordpress.org/extend/plugins/wpmu-global-search). It has some new features but if you want to keep using the older version of Wordpress MU, you can install my other plugin.
Currently in the following languages:

* English
* Spanish (es_ES)

If you have created your own language pack, or have an update of an existing one, you can send [gettext .po and .mo files](http://codex.wordpress.org/Translating_WordPress) to me so that I can bundle it into Multisite Global Search.

**New features 1.2**

* Check plugin requeriments.
* Fixed some bugs.
* Added error messages.

**New features 1.1**

* Insert search form in templates using the shortcode: `[multisite_search_result]`. [See the plugin page for more information](http://grial.usal.es/agora/pfcgrial/multisite-search).
* Show excerpted results `[multisite_search_result excerpt="yes"]`

**Features**

* Multisite Global Search Widget. Show a search form in your sidebar.
* Customizable results page URI.
* Two different form types, vertical and horizontal.
* Search across all network blogs or only in your blogs if you are logged.
* Search results are showed in a page which contents the shortcode: `[multisite_search_result]`
* Entries on every site across your installation appear in search results immediately after publication.
* Receive results from your complete blog network, even sites you do not own or control.
* Customizable style sheet for widget and results page.

== Installation ==

**Requeriments**

* WordPress Multisite Installation.
* Create view privilege in WordPress database.
* Permalink structure must be diferent to default when widget is activated in a blog of your network.

**Installation is easy**

1. Upload `multisite-global-search` folder to the `wp-content/plugins` directory in your WordPress multisite installation.
2. Activate the plugin in your Administration Panel.
3. Create a new page in your blog with default global search uri: `globalsearch`.
4. Place `[multisite_search_result]` in the post content area.
5. Activate widget `Multisite Global Search`.

== Frequently Asked Questions ==

If you have any further questions, please submit them.

= Can the search form be used with a shortcode in templates versus the widget? =

Insert search form in templates using the shortcode: `[multisite_search_form]`

== Screenshots ==

1. Widget configuration.
2. Vertical Global Search widget.
3. Horizontal Global Search widget.

== Changelog ==
* Fixed: Fatal error redeclared functions

= 1.2.1 =
* Fixed: database prefix problem
* Changed: translation files

= 1.2 =
* Added: error message when plugin installation faults
* Added: error message when permalink structure is "default"
* Fixed: error message when plugin is activated in a WordPress single installation
* Fixed: use constant BLOG_ID_CURRENT_SITE instead of 1

= 1.1 =
* Added: shortcode for search form
* Added: shortcode attribute that enable excerpted results
* Added: new strings to translation files
* Changed: order results
* Fixed: style for results page

== Upgrade Notice ==

= 1.2.2 =
Fixed a fatal error. Sorry for the inconveniences.

= 1.2.1 =
This version fixes a bug with database prefix.

= 1.2 =
This version check that your installation satisfy all plugin requeriments. Should upgrade if you can't get any search results with this plugin.