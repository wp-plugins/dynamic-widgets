=== Plugin Name ===
Contributors: Qurl
Donate link:
Tags: widget, widgets, dynamic, sidebar, custom, rules, admin, conditional tags
Requires at least: 2.9.1
Tested up to: 2.9.2
Stable tag: 1.2.3

Dynamic Widgets gives you more control over your widgets. It lets you dynamicly place widgets on WordPress pages.

== Description ==

Dynamic Widgets gives you more control over your widgets. It lets you dynamicly place widgets on WordPress pages by excluding or including rules by role, for the homepage, single posts, pages, authors, categories, archives, search and the error page.

* Default widget display setting is supported for:
  - User roles
  - Front page
  - Single post pages
  - Pages
  - Author pages
  - Category pages
  - Archive pages
  - Error Page
  - Search Page
* Exceptions can be created for:
  - User roles on role, including not logged in (anonymous) users
  - Single post pages on Author, Categories, Tags and/or Individual posts
  - Pages on Page Title
  - Author pages on Author
  - Category pages on Category name

== Installation ==

Installation of this plugin is fairly easy:

1. Unpack `dynamic-widgets.zip`
2. Upload the whole directory and everything underneath to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Visit the Dynamic Widgets Configuration page (settings link).
5. Edit the desired widgets.

== Frequently Asked Questions ==

For the latest FAQ, please visit the [online FAQ](http://www.qurl.nl/faq/).

= What are the (system) requirements to use this plugin? =

1. A properly working WordPress site (doh!).
2. Your theme must have at least one dynamic sidebar.
3. Your theme must call `wp_head()`.
4. PHP5 is highly recommended. Read on if your host uses PHP4.

= My hoster is (still) using PHP4, so what? =

Start immediately looking for another hoster. YES, immediately! NOW! Pronto! PHP4 was introduced in the year 2000 and is [not supported](http://en.wikipedia.org/wiki/PHP#Release_history) anymore. As I don't have PHP4 anymore, I can only be sure for about 80% the plugin will work. Please let me know if it doesn't. I'll try to work out a solution.

= I checked the "Make exception rule available to individual posts and tags" option, but nothing happens. =

Did you save the options? If you did, you may try to hit the (i) icon a bit to the right and read the text which appears below.

= You asked me to create a dump. How do I do that? =

* Click at the bottom of the Widgets Overview page on the 'Advanced >' link.
* Now a button 'Create dump' appears a bit below.
* Click that button.
* Save the text file.
* Remember where you saved it.

= I have found a bug! Now what? =

Please file a [bugreport](http://www.qurl.nl/bugreport/). Please note the procedure how to create a dump in the previous answer. After you've filed the report, I'll get back to you asap.

= How do I completely remove Dynamic Widgets? =

* Click at the bottom of the Widgets Overview page on the 'Advanced >' link.
* Now a button 'Uninstall' appears a bit below.
* Click that button.
* Confirm you really want to uninstall the plugin. After the cleanup, the plugin is deactivated automaticly.
* Remove the directory 'dynamic-widgets' underneath to the `/wp-content/plugins/` directory.

== Changelog ==

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

 Be sure to deactivate Dynamic Widgets Plugin before installing the new version following steps 1 and 2 in the installation procedure. After the install you can re-activate the plugin.

== Screenshots ==

1. Widgets overview page
2. Widget Options page
3. Widget with Dynamic Widgets info and link