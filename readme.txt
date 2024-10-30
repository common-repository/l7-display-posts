=== L7 Display Posts ===
Contributors:      Jeffrey S. Mattson
Donate link:       https://github.com/jeffreysmattson
Tags: display, posts, tag, show, columns, cache, object, memcache
Requires at least: 4.1.1
Tested up to:      4.1.1
Stable tag:        0.1.1
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Efficiently show posts by tag or category using a simple shortcode.  Utilizes caching for greater speed and fewer database calls.

== Description ==
Use a shortcode to display your posts.  Display them by tag, number of posts, order (asc, desc), orderby (date, time created, none, ID, author, title, parent, rand), pagination (pages), and display the results in one column or two.

Intended to be used with object caching (memcache) for maximum efficiency.

>[Display Posts tag='a tag' pages='true' posts='7' order='desc' orderby='date']

= cat =
* Category name. Add multiple categories separated by commas. (Note: Using cat and tag will increase query load dramatically)

= tag =
* Tag name. Add mulitiple tags separated by commas.

= pages =
* True or false. Display the posts with pagination or all on one page.

= posts =
* Number of posts to display. If pages is true it is the number of posts per page.

= order =
* Display the posts in descending or ascending order.

= orderby (There are many options including) =
* date
* time created
* none
* ID
* author
* title
* parent
* rand

= Requires PHP version 5.3.0 or greater. = 

== Installation ==

= Admin Panel =

1. Search for l7 Display Posts.
2. Install plugin.
3. Activate plugin.

= Manual Installation =

1. Upload the entire `/l7-display-posts` directory to the `/wp-content/plugins/` directory.
2. Activate Primary Tag Plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= How do I utilize the object caching feature for maximum speed and efficiency? =

To maximize this plugins object caching features it is recommended that a caching plugin be installed. There are many free caching plugins available.  It order to utilize memcache you may have to contact your hosting provider.

== Screenshots ==


== Changelog ==

= 0.1.0 =
* First release

== Upgrade Notice ==

= 0.1.0 =
First Release
