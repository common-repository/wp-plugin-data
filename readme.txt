=== WP Plugin Data ===
Contributors: fergbrain
Donate link: http://www.andrewferguson.net/2007/03/08/general-note/
Tags: plugin, data, API, developer
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 0.5

Provides abstracted data about plugins using the WordPress.org API

== Description ==

Provides abstracted data about plugins. Based on [Plugin Info] (http://wordpress.org/extend/plugins/plugin-info/) by John Blackbourn.

== Installation ==

Delete any previous version of WP Plugin Data and associated files.

Download and install into your plugins directory.

Activate the plugin.

= Usage =

WP Plugin Data uses shortcodes to add information about a particular WordPress.org-hosted plugin to posts and pages. The general format is:

`[wppd slug parameter]`

Where `slug` is the sanitized version of the plugin name, such as `wp-plugin-data`

And `parameter` is one of the following:

* `name` - The name of the plugin
* `slug` - The plugin slug
* `version` - The plugin version number
* `author` - A formatted link to the plugin author's homepage with the author's name as the link text (if the author doesn't have a homepage this will simply display their name)
* `author_name` - The plugin author's name
* `author_url` - The URL of the plugin author's homepage
* `requires` - The minimum WP version required
* `tested` - The highest level of WP the plugin has been tested to
* `rating` - The plugin's star rating as a whole number out of 5 (given by visitors to wp.org)
* `rating_raw` - The plugin's actual average rating as a score out of 100 (given by visitors to wp.org)
* `num_ratings` - The number of people who've rated your plugin on wp.org
* `downloaded` - The all-time download count with comma-separated thousands (eg. "12,345")
* `downloaded_raw` - The all-time download count as a raw number (eg. "12345")
* `last_updated` - The date the plugin was last updated, formatted according to your Date Format settings under Settings->General (eg. "20 January 2008")
* `last_updated_ago` - How long ago the plugin was last updated (eg. "20 days ago")
* `last_updated_raw` - The date the plugin was last updated, in the format "yyyy-mm-dd"
* `num_ratings` - The total number of people who have rated the plugin
* `description` - The description section of the readme.txt
* `faq` - The FAQ section of the readme.txt
* `installation` - The installaton section of the readme.txt
* `screenshots`	- The screen shots section
* `homepage_url` - The URL of the plugin's homepage
* `link_url` - The URL of the plugin's page on the WP Plugin Directory
* `download_url` - The URL of the plugin's ZIP file
* `tags` - A comma-separated list of the plugin's tags


Additionally, there is a secondary shortcode for parameters that return formated links

`[wppdlink slug parameter]Link Text[/wppdlink]`

* `homepage` - A formated link to the plugin's homepage with the specified link text
* `link` - A formatted link to the plugin's page on the WP Plugin Directory with the specified link text
* `download` - A formatted link to the plugin's ZIP file with the specified link text

You can also use `[wppd slug parameter]` as the content. For example:

`[wppdlink wp-plugin-data download]WP Plugin Data - v[wppd wp-plugin-data version][/wppdlink]`

Would return:

`<a href="http://downloads.wordpress.org/plugin/wp-plugin-data.zip">WP Plugin Data - v0.5</a>`

== Frequently Asked Questions ==

= Is the data cached? =
Yes, plugin data retreived from WordPress.org is cached locally for one hour before being refreshed.

= How is the data refreshed? =
When someone visits one of your posts (or pages) that uses the [wppd] or [wppdlink] shortcode, the plugin will check to see if the data is more than one hour old. If it is, it will automatically refresh it at that time.