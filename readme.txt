=== Plugin Name ===
Contributors: mbaker
Donate link: http://michael-baker.com/simple_shortcodes/donate/
Tags: shortcode, boilerplate
Requires at least: 2.6
Tested up to: 2.7
Stable tag: trunk

An easy to use mechanism for creating and using custom shortcodes.

== Description ==

Simple Shortcodes provides an easy to use mechanism for creating and using custom shortcodes.

After installing and activating SimpleShortcodes, a Simple Shortcodes configuration item is added to your Settings menu.

From the configuration page you can create new simple shortcodes and edit and delete existing simple shortcodes.

The current version can only make simple substitutions.  Shortcode parameters ([shortcode parameter="value"]) and content wrapping ([shortcode]some content[/shortcode]) are currently not supported.

Simple shortcodes are stored in a database table (MB_SimpleShortcodes), which contains two fields (name, value).

Beware: Use unique names for your shortcodes.  If you use the same shortcode name as that used by another plugin, one of the short code definitions will be overwritten by the other without any warning.

== Installation ==

1. Download, extract & upload the zip file content to WordPress Plugins directory /wp-content/plugins/.
1. Go to your WordPress Admin Dashboard >> Plugins menu.
1. Activate "Simple Shortcodes".


== Frequently Asked Questions ==

= How do I use Simple Shortcodes? =

After installing Simple Shortcodes go to the Settings >> Configure Simple Shortcodes page and create one or more shortcodes.

As an example enter myshortcode in the Shortcode Name text box, enter "This is boilerplate created using myshortcode." in the Shortcode Value text box (with or without the quotes), and click Save.  Your new shortcode will be listed below the text boxes.

To edit an existing shortcode, click on edit, and the shortcode name and value will be placed in the textboxes ready to be edited.  When done click on save.

Beware when saving shortcodes, no check is made to see if there is an exising shortcode with the same name.

To delete an existing shortcode, click on delete.  Beware when deleting shortcodes as it occurs immediately without confirmation, and there is no undelete facility.

To use a shortcode in a post or page on your blog just enter the shortcode in [ ], such as [myshortcode].

= Where can I get support? =

Support for Simple Shortcodes is available from http://michael-baker.com/simple_shortcodes/support/

= What is mb_ack? =

Simple Shortcodes comes with a pre-installed shortcode - [mb_ack].

If you find Simple Shortcodes to be useful I would appreciate it if you could show your support by including the shortcode [mb_ack] on one or more of your posts or pages.

== Screenshots ==

1. Configure Simple Shortcodes