=== Page View Count  ===
Contributors: a3rev, A3 Revolution Software Development team
Tags: wordpress page view, page view count , post views, postview count,
Requires at least: 3.3
Tested up to: 3.6.1
Stable tag: 1.0.4.1
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

Using the 2 functions the plugin provides that allows you to manually add view counts to any content or object in your theme. This is very useful if your theme or a plugin creates content that does not use custom post type. Also useful if you want to change the position of the Page View Count from the default bottom of the page. 
 

= Localization =

If you do a translation for your site please send it to us and we'll include it in the plugins language folder and credit you here with the translation and a link to your site.

* English (default) - always included.
* Dutch - thanks to [Renee Klein]( http://wpdiscounts.com)
*.po file (pvc.po) in languages folder for translations.
* [Go here](http://a3rev.com/contact-us-page/) to send your translation files to us.

= Documentation & Support =

If you require support first of all please view the plugins docs on the a3rev wiki. [Click Here to view](http://docs.a3rev.com/user-guides/page-view-count/) If you don't find the information you are looking for - THEN please post your support request under the support tab here on this page. If the Page View Count gadget does not show on any page or post it will be because the theme you are using does not use the WordPress Codex not because the plugin does not work. 

PLEASE do not give the plugin a bad star rating - its the theme you are using not the plugin. Instead post about it on the support forum and we will have a look at it for you.

== Installation ==

= Minimum Requirements =

* WordPress 3.3
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

= 1.0.4.1 - 2013/10/14 =
* Fixed:
	* Conflict with new version of WordPress SEO plugin causing duplicate page counts - add_filter( 'wpseo_opengraph_desc', array( 'A3_PVC', 'fixed_wordpress_seo_plugin' ) );
	* Updated Plugin Admin Framework by added array_map( array( $this, 'admin_stripslashes' ) , $current_settings ) to strip slashes on value when show on frontend
* Tweak:
	* Fixed typos on admin panel.
* Translations:
	* Added Dutch translation thanks to Renee Klein

= 1.0.4 - 2013/10/10 =
* Tweaks:
	* a3rev logo image now resizes to the size of the yellow sidebar in tablets and mobiles.
* Fixes:
	* Intuitive Radio Switch settings not saving. Input with disabled attribute could not parse when form is submitted, replace disabled with custom attribute: checkbox-disabled
	* App interface Radio switches not working properly on Android platform, replace removeProp() with removeAttr() function script

= 1.0.3 - 2013/10/05 =
* Features :
	* Upgraded the plugin to the newly developed a3rev admin panel app interface.
	* New admin UI features check boxes replaced by switches.
* Fixed :
	* Plugins admin script and style not loading in Firefox with SSL on admin. Stripped http// and https// protocols so browser will use the protocol that the page was loaded with.

= 1.0.2 - 2013/08/28 =
* Features :
	* Major performance enhancement. All Time Views table data emptied each day on 24 hour cron. 
	* Added House Keeping function to settings. Clean up on Deletion. Option - Choose if you ever delete this plugin it will completely remove all tables and data it has created, leaving no trace it was ever installed.
* Tweaks :
	* Plugin in code tested fully compatible with WordPress v3.6.0
	* Ran full WP_DEBUG All Uncaught exceptions errors and warnings fixed.
	* Added PHP Public Static to functions in Class. Done so that PHP Public Static warnings don't show in WP_DEBUG mode.
	* Added when install and activate plugin link redirects to the plugins dashboard instead of the wp-plugins dashboard.
	* Updated plugins support forum link to the wordpress support forum.

= 1.0.1 - 2013/01/10 =
* Tweak: Updated Support and Pro Version link URL's on wordpress.org description, plugins and plugins dashboard. Links were returning 404 errors since the launch of the all new a3rev.com mobile responsive site as the base e-commerce permalinks is changed.

= 1.0.0 - 2012/12/20 =
* First Release.


== Upgrade Notice ==

= 1.0.4.1 =
Update you plugin now for 2 bug fixes - especially important to upgrade now if you use the WordPress SEO plugin.

= 1.0.4 =
Upgrade now for another admin panel intuitive app interface feature plus a Radio switch bug fix and Android platform bug fix

= 1.0.3 =
Upgrade you plugin now for the all new a3rev admin panel app type interface and a protocols in browser bug fix

= 1.0.2 =
Important upgrade - please update your plugin now for a major performance enhancement.