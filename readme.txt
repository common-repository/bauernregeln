=== Bauernregeln ===
Contributors: rvincent
Donate link: http://rvincent.digital-nerv.net/
Tags: widget, bauernregeln, german proverbs
Requires at least: 3.5
Tested up to: 4.4
Stable tag: trunk/1.0.1
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Shows a german weather proverb or country saying, called Bauernregel, for every month.

== Description ==
Shows a german weather proverb or country saying, called 'Bauernregel', for every month.
Zeigt verschiedene Bauernregeln für jeden Monat an. Per Zufall jeweils ein anderes beim Seitenaufruf.
Available translations (Of course not the proverbs, since they are german)
* English
* German
* pot file included

== Installation ==
1. Upload `bauernregeln` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Put the 'Widget' in an 'Widget-Area' to display in your blog.

== Frequently Asked Questions ==
= Meine Frage ist nun, ob es eine Möglichkeit gibt die Überschrift zu ändern? In so etwas wie 'Spruch des Monats <aktueller Monat>' oder '<Monat> Bauernregel'. =

Ich habe das Bauernregeln WordPress Plugin umgeschrieben. Nun kann man auch den Titel individuell einstellen.

== Screenshots ==
1. Example of the 'Widget' in a Sidebar
2. Example of the 'Widget' in a Sidebar
3. Screenshot of the 'Widget-Menu'
4. Screenshot of the 'Plugin Options'

== Changelog ==
= 1.0.1 =
* Compatibilty for WordPress 4.3
* Fixes __construct() error Message

= 1.0.0 =
* Compatibilty for WordPress 4.0
* Proper Uninstall and Deactivation
* Disable only_custom Option if there are no custom proverbs available

= 0.1.9 =
* Improved CSV File Support.
* Fixed a Problem where the Upload Button was disabled all time.

= 0.1.8 =
* Fixed Variable declaration.

= 0.1.7 =
* Fixed database handling ($wpdb)
* Added upload for custom proverbs (CSV)
* Added Plugin Options menu (Settings)
* Added possibility to show only custom proverbs

= 0.1.6 =
* Converted to HTML Entities.

= 0.1.5 =
* Replaced dbDelta.
* Added deactivation hook.
* Fixed bug that some proverbs where not shown.

= 0.1.2 =
* Title can show current month for the proverb
* Translation updated
* Examples can be seen in the 'Widget-Box'

= 0.1.1 =
* Added some more proverbs.

= 0.1.0 =
* Initial Release.
* Added proverbs.

== Upgrade Notice ==

= 0.1.6 =
* Safe Character Encoding.

= 0.1.7 =
* Custom proverbs.

= 0.1.8 =
* Bug fix. Undefined index.

= 0.1.9 =
* Improved CSV Support.

= 1.0.0 =
* Complete rewritten and optimized code for WordPress 4.0

= 1.0.1 =
* Compatibilty for WordPress 4.3
