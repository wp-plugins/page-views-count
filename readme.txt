=== Page View Count  ===
Contributors: a3rev, A3 Revolution Software Development team
Tags: wordpress page view, page view count , post views, postview count,
Requires at least: 3.8
Tested up to: 4.0
Stable tag: 1.0.7
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Places an icon, all time views count and views today count at the bottom of posts, pages and custom post types on any Wordpress website.

== Description ==

Page Views Count does 2 things - beautifully

1. On the front end it adds an icon and page views count to the bottom of pages and posts on your WordPress website.

2. On the back end via check boxes you can apply or hide Page Views Count on all Posts, Pages and all custom posts types including WooCommerce and WP e-Commerce custom post types.


= Why we built it =

We built this plugin and use this plugin because we think its is very good visual feedback for site users and site admins about the all time popularity and visits that day of individual posts and pages on any Wordpress website.

= Functions =

Using the 2 functions the plugin provides that allows you to manually add view counts to any content or object in your theme. This is very useful if your theme or a plugin creates content that does not use custom post type. Also useful if you want to change the position of the Page View Count from the default bottom of the page. Support for echo and return parameters when getting visitor stats on any variable.

= Featuring Sass (Simply Awesome Stylesheets) =

* All the front end style display is created via Sass for faster loader and render.
* Sass is the most mature, stable, and powerful professional grade CSS extension language in the world.
* This plugins backend admin panel are created in Sass.


= Featuring backbone.js =

* All front end functions build with backbone.js
* Will add full automatic integration with WordPress JSON RESTful API when it is released.

= Mobile Responsive App style admin interface =

* 100% compatible with WordPress v4.0 admin interface.
* Backward compatibility to WordPress v3.8
* Sass app style admin interface.
* Admin app interface is fully mobile and tablet responsive.

= Localization =

If you do a translation for your site please send it to us and we'll include it in the plugins language folder and credit you here with the translation and a link to your site.

* English (default) - always included.
* Dutch - thanks to [Renee Klein]( http://wpdiscounts.com)
* .po file (pvc.po) in languages folder for translations.
* [Go here](http://a3rev.com/contact-us-page/) to send your translation files to us.

= Documentation & Support =

If you require support first of all please view the plugins docs on the a3rev wiki. [Click Here to view](http://docs.a3rev.com/user-guides/page-view-count/) If you don't find the information you are looking for - THEN please post your support request under the support tab here on this page. If the Page View Count gadget does not show on any page or post it will be because the theme you are using does not use the WordPress Codex not because the plugin does not work.

PLEASE do not give the plugin a bad star rating - its the theme you are using not the plugin. Instead post about it on the support forum and we will have a look at it for you.

== Installation ==

= Minimum Requirements =

* WordPress 3.8
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't even need to leave your web browser. To do an automatic install of Page Views Count, log in to your WordPress admin panel, navigate to the Plugins menu and click Add New.

In the search field type "Page Views Count" and click Search Plugins. Once you have found our plugin you can install it by simply clicking Install Now. After clicking that link you will be asked if you are sure you want to install the plugin. Click yes and WordPress will automatically complete the installation.

= Manual installation =

The manual installation method involves down loading our plugin and uploading it to your web server via your favorite FTP application.

1. Download the plugin file to your computer and unzip it
2. Using an FTP program, or your hosting control panel, upload the unzipped plugin folder to your WordPress installations wp-content/plugins/ directory.
3. Activate the plugin from the Plugins menu within the WordPress admin.


== Screenshots ==

1. Page Views Count is added to the bottom of posts and pages
2. Page Views Count simple admin panel.


== Usage ==

1. Install and activate the plugin

2. Go to Settings > Page View Count

3. Select which post and page types you want the Page View Count to show on.

4. Have fun.

== Frequently Asked Questions ==


== Changelog ==

= 1.0.7 =
* Feature - Next step in full conversion of plugin to backbone.js and Sass - full admin panel conversion from CSS to Sass.

= 1.0.6.4 - 2014/09/13 =
* Tweak - Added WordPress plugin icon.
* Fix - Changed <code>__DIR__</code> to <code>dirname( __FILE__ )</code> for Sass script so that on some server <code>__DIR__</code> is not defined

= 1.0.6.3 - 2014/09/10 =
* Tweak - Updated google font face in plugin framework.
* Tweak - Tested 100% compatible with WordPress Version 4.0
* Fix - Use correct variable $postid instead of $post->ID for 'custom_stats_update_echo()' function to fix the problem don't auto increase total view for use template tag '<?php pvc_stats_update( $postid ); ?>'

= 1.0.6.2 - 2014/07/22 =
* Fix - Add missed style when disable AJAX load for view counts

= 1.0.6.1 - 2014/07/22 =
* Fix - Add missed style for class .pvc_clear in Sass

= 1.0.6 - 2014/07/21 =
* Feature - Beginning of conversion of the plugin to backbone.js and Sass
* Feature - Loads superfast on frontend with substantially less calls to the server making it very lightweight and super fast.
* Feature - On Category extract pages plugin now only calls once to admin-ajax.php regardless of how many extract view counts are shown.
* Feature - Upgrade Page View Count front end count and load function to backbone.js
* Feature - Upgraded plugins front end CSS to Sass.

= 1.0.5 - 2014/06/27 =
* Feature - Added load Counter on front end by Ajax option to prevent count being cached by caching plugins.
* Tweak - added ajax event 'pvc_ajax_load_stats' and trigger $("body").trigger("pvc_stats_loaded_" + pvc_object_id ); for that event
* Tweak - added ajax event 'pvc_ajax_update_stats' and trigger $("body").trigger("pvc_stats_updated_" + post_id ); for that event
* Tweak - use $wpdb->prepare() for all sql command for security
* Tweak - Updated chosen js script to latest version 1.1.0 on the a3rev Plugin Framework
* Tweak - Added support for placeholder feature for input, email , password , text area types.
* Tweak - Added Russiian translation thanks to [Renat Nurlyev](http://kluchkuspexu.ru/)

= 1.0.4.3 - 2014/05/26 =
* Tweak - Changed add_filter( 'gettext', array( $this, 'change_button_text' ), null, 2 ); to add_filter( 'gettext', array( $this, 'change_button_text' ), null, 3 );
* Tweak - Update change_button_text() function from ( $original == 'Insert into Post' ) to ( is_admin() && $original === 'Insert into Post' )
* Tweak - Checked and updated for full compatibility with WordPress version 3.9.1
* Tweak - Updated plugins description text and admin panel yellow sidebar text.
* Tweak - Updated Framework help text font for consistency.
* Tweak - Added remove_all_filters('mce_external_plugins'); before call to wp_editor to remove extension scripts from other plugins.
* Tweak - Full WP_DEBUG ran, all uncaught exceptions, errors, warnings, notices and php strict standard notices fixed.
* Tweak - Added PHP Public Static to functions in Class. Done so that Public Static warnings don't show in DE_BUG mode.
* Fix - Code tweaks to fix a3 Plugins Framework conflict with WP e-Commerce tax rates.

= 1.0.4.2 - 2013/12/24 =
* Feature - a3rev Plugin Framework admin interface upgraded to 100% Compatibility with WordPress v3.8.0 with backward compatibility.
* Feature - Feature - a3rev framework 100% mobile and tablet responsive, portrait and landscape viewing.
* Feature - Added option of return parameter to existing echo for getting page view stats on any variable.
* Tweak - Upgraded dashboard switches to Vector based display that shows when WordPress version 3.8.0 is activated.
* Tweak - Tweak - Upgraded all plugin .jpg icons and images to Vector based display for full compatibility with new WordPress version.
* Tweak - Admin panel Yellow sidebar not show in Mobile screens to optimize admin panel screen space.
* Tweak - Tested 100% compatible with WP 3.8.0
* Fix - Upgraded array_textareas type for Padding, Margin settings on the a3rev plugin framework.

= 1.0.4.1 - 2013/10/14 =
* Tweak - Fixed typos on admin panel.
* Fix - Conflict with new version of WordPress SEO plugin causing duplicate page counts - add_filter( 'wpseo_opengraph_desc', array( 'A3_PVC', 'fixed_wordpress_seo_plugin' ) );
* Fix - Updated Plugin Admin Framework by added array_map( array( $this, 'admin_stripslashes' ) , $current_settings ) to strip slashes on value when show on frontend
* Translation - Added Dutch translation thanks to Renee Klein

= 1.0.4 - 2013/10/10 =
* Tweak - a3rev logo image now resizes to the size of the yellow sidebar in tablets and mobiles.
* Fix - Intuitive Radio Switch settings not saving. Input with disabled attribute could not parse when form is submitted, replace disabled with custom attribute: checkbox-disabled
* Fix - App interface Radio switches not working properly on Android platform, replace removeProp() with removeAttr() function script

= 1.0.3 - 2013/10/05 =
* Feature - Upgraded the plugin to the newly developed a3rev admin panel app interface.
* Feature - New admin UI features check boxes replaced by switches.
* Fix - Plugins admin script and style not loading in Firefox with SSL on admin. Stripped http// and https// protocols so browser will use the protocol that the page was loaded with.

= 1.0.2 - 2013/08/28 =
* Feature - Major performance enhancement. All Time Views table data emptied each day on 24 hour cron.
* Featue - Added House Keeping function to settings. Clean up on Deletion. Option - Choose if you ever delete this plugin it will completely remove all tables and data it has created.
* Tweak - Plugin in code tested fully compatible with WordPress v3.6.0
* Tweak - Ran full WP_DEBUG All Uncaught exceptions errors and warnings fixed.
* Tweak - Added PHP Public Static to functions in Class. Done so that PHP Public Static warnings don't show in WP_DEBUG mode.
* Tweak - Added when install and activate plugin link redirects to the plugins dashboard instead of the wp-plugins dashboard.
* Tweak - Updated plugins support forum link to the wordpress support forum.

= 1.0.1 - 2013/01/10 =
* Tweak - Updated Support and Pro Version link URL's on wordpress.org description, plugins and plugins dashboard. Links were returning 404 errors since the launch of the all new a3rev.com mobile responsive site as the base e-commerce permalinks is changed.

= 1.0.0 - 2012/12/20 =
* First Release.


== Upgrade Notice ==

= 1.0.7 =
Upgrade now for full admin panel conversion from CSS to Sass

= 1.0.6.4 =
Upgrade now for 1 Sass bug fix

= 1.0.6.3 =
Update you plugin now for 1 framework code tweak plus 1 bug fix and full compatibility with WordPress Version 4.0

= 1.0.6.2 =
Upgrade now for a bug fix for missed style when disable AJAX load view counts in version release 1.0.6.1.

= 1.0.6.1 =
Upgrade now for a bug fix for Sass in yesterday’s major version release 1.0.6.

= 1.0.6 =
Upgrade now for the beginning of conversion of plugin to backbone.js and Sass. Much faster front-end load and less calls on server.

= 1.0.5 =
Update now for new feature - load counter by Ajax event plus security hardening and 2 framework code tweaks and Russian translation.

= 1.0.4.3 =
Upgrade now for full compatoibility with WordPress Version 3.9.1, 8 code tweaks and 1 bug fix.

= 1.0.4.2 =
Upgrade now for full a3rev Plugin Framework compatibility with WordPress version 3.8.0 and backwards. New admin interface full mobile and tablet responsive display.

= 1.0.4.1 =
Update you plugin now for 2 bug fixes - especially important to upgrade now if you use the WordPress SEO plugin.

= 1.0.4 =
Upgrade now for another admin panel intuitive app interface feature plus a Radio switch bug fix and Android platform bug fix

= 1.0.3 =
Upgrade you plugin now for the all new a3rev admin panel app type interface and a protocols in browser bug fix

= 1.0.2 =
Important upgrade - please update your plugin now for a major performance enhancement.
