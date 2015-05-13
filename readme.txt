=== Plugin Name ===
Contributors: asimeon, triechert
Tags: wikipedia, widget, sidebar, search, knowledge
Requires at least: 3.5
Tested up to: 4.2
Stable tag: 0.13.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows a simple Ajax based Wikipedia search-formular and the results for the current post/page title or default keywords.

== Description ==

The <strong>Wikipedia Widget</strong> shows a Wikipedia search-formular and its search results for the current post or page on your sidebar. On other sites (like category or front-page) it can defined any default search-keywords or other input-field.

The search results were Ajax-based fetched from the <a href="http://en.wikipedia.org/w/api.php">Wikipedia-API</a>.

<strong>Main Features: </strong>

* Define a default search string or get the current post title
* Show or hide the search form or define an alternative search form within the page
* Use cache for the search results for better speed 
* Results are clear formatted, with links and pictures

Be free for testing and suggest improvements.

== Installation ==

1. Upload the plugin to your `/wp-content/plugins/` directory, activate and use as a sidebar-widget.
2. Set the Wikipedia URL to your country.

== Frequently Asked Questions ==

No Questions have been asked yet. 

== Screenshots ==

1. Back-end configuration
2. Front-end display search results and input

== Upgrade Notice ==
* No upgrade changes yet.

== Changelog ==

= 0.13.12 =
* remove bug with loading values of alternative input-fields
* WordPress 3.8 compatibilty
* pack widget into one file
* several small code changes
* new loader


= 0.13.07 =
* fixed too long transient-names

= 0.13.05 =
* add cache for every search result
* remove search trim

= 0.13.04 =

* Fixed ajaxurl
* add alternativ search form feature
* add cache for the default search term if used
* test if value changed before search
* several code changes/improvements

== Tickets ==

* Check given Wikipedia-URL at backend
* plugin page at a-simeon.de
* improve faq