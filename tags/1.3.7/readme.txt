=== Plugin Name ===
Contributors: Qurl
Donate link:
Tags: widget, widgets, dynamic, sidebar, custom, rules, admin, condition, conditional tags
Requires at least: 2.9.1
Tested up to: 3.0.3
Stable tag: 1.3.7

Dynamic Widgets gives you full control on which pages your widgets will appear. It lets you dynamicly place the widgets on WordPress pages.

== Description ==

Dynamic Widgets gives you full control on which pages your widgets will appear. It lets you dynamicly place the widgets on WordPress pages by setting conditional rules with just a few mouse clicks by role, dates, language (WPML), for the homepage, single posts, pages, authors, categories, archives, error page, search page and custom post types.

* Default widget display setting is supported for:
  - User roles
  - Dates
  - Language (WPML)
  - Front page
  - Single post pages
  - Pages
  - Author pages
  - Category pages
  - Archive pages
  - Error Page
  - Search Page
  - Custom Post Types
  - WP Shopping Cart / WP E-Commerce Categories

* Exceptions can be created for:
  - User roles on role, including not logged in (anonymous) users
  - Dates on from, to or range
  - Language (WPML) on language
  - Single post pages on Author, Categories, Tags and/or Individual posts
  - Pages on Page Title, including inheritance from hierarchical parents
  - Author pages on Author
  - Category pages on Category name
  - Custom Posts Type on Custom post name, including inheritance from hierarchical parents
  - WP Shopping Cart / WP E-Commerce Categories on Category name

* Plugin support for:
  - WP MultiLingual (WPML)
  - WP Shopping Cart / WP E-Commerce (WPSC / WPEC)

* Language files provided:
	- French (fr_FR) by Alexis Nomine

== Installation ==

Installation of this plugin is fairly easy:

1. Unpack `dynamic-widgets.zip`
2. Upload the whole directory and everything underneath to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Visit the Dynamic Widgets Configuration page (settings link).
5. Edit the desired widgets.

== Frequently Asked Questions ==

For the latest FAQ, please visit the [online FAQ](http://www.qurl.nl/dynamic-widgets/faq/).

= What are the (system) requirements to use this plugin? =

1. A properly working WordPress site (doh!).
2. Your theme must have at least one dynamic sidebar.
3. Your theme must call `wp_head()`.
4. PHP5 is highly recommended. Read on if your host uses PHP4.

= My hoster is (still) using PHP4, so what? =

Start immediately looking for another hoster. YES, immediately! NOW! Pronto! PHP4 was introduced in the year 2000 and is [not supported](http://en.wikipedia.org/wiki/PHP#Release_history) anymore. As I don't have PHP4 anymore, I can only be sure for about 80% the plugin will work. Please let me know if it doesn't. I'll try to work out a solution.

= I'm not sure my theme is calling `wp_head()`. Can I check? =

Yes, you can. In the Dynamic Widgets Overview page, click the 'Advanced >' link at the bottom. You should see if `wp_head()` is called in your theme. It is possible Dynamic Widgets can't detect if the theme is calling `wp_head()`. Please contact the author of the theme to ask for it. You can also of course just try Dynamic Widgets to see if it works.

= Does the plugin work on WordPress 3.0 MU? =

Yes, but only if you activate the plugin on a per site base. Network Activation is not supported.

= I checked the "Make exception rule available to individual posts and tags" option, but nothing happens. =

Did you save the options? If you did, you may try to hit the (i) icon a bit to the right and read the text which appears below.

= The plugin slows down the loading of a page dramatically. Can you do something about it? =

Try setting the plugin to the 'OLD' method. You can do this by clicking on the 'Advanced >' link at the bottom of the Widgets Overview page and check the box next to 'Use OLD method'. See if that helps. Setting the plugin using the 'OLD' method comes with a downside unfortunately. It may leave you behind with a visible empty sidebar.

= I want to check if the ‘OLD’ method suits me better, is there a way back if it doesn’t? =

Yes! You can switch between FILTER and OLD method without any loss of widgets configuration or whatsoever.

= I want in Page X the sidebar becomes empty, but instead several widgets are shown in that sidebar. Am I doing something wrong? =

Your theme probably uses a 'default display widgets policy'. When a sidebar becomes empty, the theme detects this and places widgets by default in it. The plugin can't do anything about that. Ask the theme creator how to fix this.

= You asked me to create a dump. How do I do that? =

* Click at the bottom of the Widgets Overview page on the 'Advanced >' link.
* Now a button 'Create dump' appears a bit below.
* Click that button.
* Save the text file.
* Remember where you saved it.

= I have found a bug! Now what? =

Please file a [bugreport](http://www.qurl.nl/dynamic-widgets/bugreport/). Please note the procedure how to create a dump in the previous answer. After you've filed the report, I'll get back to you asap.

= How do I completely remove Dynamic Widgets? =

* Click at the bottom of the Widgets Overview page on the 'Advanced >' link.
* Now a button 'Uninstall' appears a bit below.
* Click that button.
* Confirm you really want to uninstall the plugin. After the cleanup, the plugin is deactivated automaticly.
* Remove the directory 'dynamic-widgets' underneath to the `/wp-content/plugins/` directory.

== Changelog ==

= Version 1.3.7 =

* Added more l10n text strings.
* Added French language files (locale: fr_FR) - Merci beaucoup Alexis!
* Added language (WPML) as an option.
* Added hierarchical inheritance support for Pages and Custom Post Types
* Bugfix for unexpected behavior when two widgets are in opposite config of eachother.
* Fixed a couple of l10n text strings
* Changed UI in edit options screen (Thanks Alexis for the help!).
* Speeded up the removing process in FILTER method.

= Version 1.3.6 =

* Added l10n support.
* Added Dutch language files (locale: nl)
* Added support for WP Shopping Cart / WP E-Commerce Categories.
* Bugfix for error 404 (file not found) when saving options.
* Bugfix for unexpected behavior in subsequent category pages.
* Bugfix for unexpected behavior in single post when using individual exception rules.
* Bugfix for unexpected behavior in Custom Post Types.
* Bugfix for incorrect use and display of Custom Post Types in Widget Edit Options screen.
* Removed several PHP notices.

= Version 1.3.5 =

* Added support for themes which use the WP function is_active_sidebar() when the method is set to FILTER (default).
* Bugfix by removing a possible unnecessary loop for dynamic widget options.

= Version 1.3.4 =

* Bugfix for minor flaw "Invalid argument supplied for foreach() in dynwid_admin_save.php on line 203"

= Version 1.3.3 =

* Added Custom Post Types support for WordPress 3.0.
* Added WPML support for static pages, category pages, category in single posts and custom post types.
* Bugfix for not resetting checked count when enabling individual posts with authors and/or category set.

= Version 1.3.2 =

* Added an internal filter when checking for widget options to make the plugin faster.

= Version 1.3.1 =

* Maintenance release for WordPress 3.0 support.

= Version 1.3 =

* Added support for dates functionality.

= Version 1.2.6 =

* Another bugfix try for nasty PHP warning "Cannot use a scalar value as an array".

= Version 1.2.5 =

* Bugfix for user role detection when using SPF.

= Version 1.2.4 =

* Bugfix(?) for PHP warning "Cannot use a scalar value as an array"

= Version 1.2.3 =

* Added default widget display setting option for Search Page.

= Version 1.2.2 =

* Added detection for posts page when front page display is set to static page (more or less a bugfix for 1.2.1).

= Version 1.2.1 =

* Added functionality when front page display is set to static page.

= Version 1.2 =

* Added support for PHP4 (not fully tested).
* Added Dynamic Widgets info and edit link in the widgets admin itself.
* Added support for widget display setting options for Author Pages.
* Added support for Single Posts exception rules for tags.
* Added support for Single Posts exception rules for individual posts.
* Bugfix for rare cases not selecting the right default option for single posts.
* Bugfix for wrong exception rules were applied in rare cases when rules are set for a page or archive page.
* Bugfix for displaying confusing success and error message.
* Bugfix for not displaying checked checkboxes in MS Internet Explorer.
* Workaround to stop showing invalid (not clean unregistered) widgets without a name.
* Some small textual changes.
* Moved general helpinfo to standard WordPress contextual help screen.

= Version 1.1.1 =

* Bugfix for unexpected default option values when using role options.

= Version 1.1 =

* Added support for widget display settings based on role, including not logged in (anonymous) users.

= Version 1.0.1 =

* Added default widget display setting option for 'Not Found' Error (404) Page.

== Upgrade Notice ==

Be sure to deactivate Dynamic Widgets Plugin before installing the new version following steps 1 and 2 in the installation procedure. After the install you can reactivate the plugin.

== Screenshots ==

1. Widgets overview page
2. Widget Options page
3. Widget with Dynamic Widgets info and link