=== Plugin Name ===
Contributors: GaryJ, freemius
Donate link: http://code.garyjones.co.uk/donate/
Tags: diagnosis, server, PHP, MySQL, information
Requires at least: 3.2
Tested up to: 4.0
Stable tag: 3.0.0

Adds pages to the Dashboard menu with technical details about PHP, MySQL and other server details an administrator might need.

== Description ==

It is hard for novice WordPress users to find out what the backbone of their hosting is made up of. Some plugins may require a specific version of the PHP scripting language or a certain version of the MySQL database management software.

Diagnosis adds pages to the Dashboard menu where the main administrator of the Wordpress installation can view technical data about the server in an easy manner along with a suitable explanation. Most fields also have a link to an appropriate Wikipedia article.

This plugin is predominantly based on the original Diagnosis plugin: http://nlindblad.org/wordpress/diagnosis/

== Installation ==

1. Upload the `diagnosis` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Visit Dashboard -> Diagnosis to see the information.

== Frequently Asked Questions ==

= Something isn't working correctly. What should I do? =

Make sure you're on the latest version of WordPress, then let me know what the problem is with as much detail as possible.

== Screenshots ==

1. Part of the Diagnosis screen.

== Changelog ==

= 3.0.0 =
* Complete rewrite to update code organisation.
* Support for GitHub Updater added.

= 2.1.2 =
* Fixed more strings that weren't translatable.
* Fixed permissions issue for non-MS sites added in 2.1
* Added German translation.

= 2.1.1 =
* Re-commit since previous version apparently lost css and languages folder (props tsdk for the heads-up).

= 2.1 =
* Improved and tidied code structure.
* Fixed some table heading strings that weren't translatable.
* Fixed some typos.
* Added a couple of new rows of data.
* Now requires PHP5.

= 2.0.2 =
* Bump to indicate support for 3.1.
* Fixed minor code standard issues.

= 2.0.1 =
* Added support for localization.
* `diagnosis.pot` file added to `languages` subfolder.
* Fixed minor style issue.

= 2.0 =
* Initial release based on original plugin: http://nlindblad.org/wordpress/diagnosis/ .
* Rewritten as a plugin class to ensure no function name collisions.
* Added `custom_theme_support('diagnosis_menu')` and `custom_theme_support('diagnosis_phpinfo_menu')` checks so it can be disabled in a theme.
* Added new `diagnosis_read` capability, assigned to administrators by default.
* Added `phpinfo()` as a new page.
* Moved styles into external style sheet so they are cached.

== Upgrade Notice ==

= 3.0.0 =
Refactor to update code. No new features.

= 2.1.2 =
Fixes translation and permission issues.

= 2.1.1 =
Fixes missing CSS from previous version.

= 2.1 =
Several code improvements and fixes for translations.

= 2.0.2 =
* Bump to indicate support for 3.1 - no new features added or bugs fixed.

= 2.0.1 =
Adds support for translations.

= 2.0 =
Initial release based on original plugin: http://nlindblad.org/wordpress/diagnosis/

== Translations ==
* Deutsch: by [Dave @ deckerweb.de](http://deckerweb.de/material/sprachdateien/wordpress-plugins/#diagnosis)
