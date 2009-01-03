=== Simple Shortcodes ===
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
 
Simple shortcodes are stored in a database table (MB\_SimpleShortcodes), which contains two fields (name, value). 
 
Beware: Use unique names for your shortcodes.  If you use the same shortcode name as that used by another plugin, one of the short code definitions will be overwritten by the other without any warning.

== Installation ==
 
1. Download (http://downloads.wordpress.org/plugin/simple-shortcodes.zip) , extract & upload the zip file content to your WordPress Plugins directory /wp-content/plugins/ 
1. Go to your WordPress Admin Dashboard >> Plugins menu 
1. Activate "Simple Shortcodes"  
 
If you are upgrading from version 0.2 you will need to deactivate Simple Shortcodes then activate Simple Shortcodes after you upgrade to modify the Simple Shortcodes database table.

== Frequently Asked Questions ==

= How do I use Simple Shortcodes? =

After installing Simple Shortcodes go to the Settings >> Configure Simple Shortcodes page and create one or more shortcodes. 
 
As an example enter myshortcode in the Shortcode Name text box, enter "This is boilerplate created using myshortcode." in the Shortcode Value text box (with or without the quotes), and click Save.  Your new shortcode will be listed below the text boxes. 
 
To edit an existing shortcode, click on edit, and the shortcode name and value will be placed in the textboxes ready to be edited.  When done click on save. 
 
Beware when saving shortcodes, no check is made to see if there is an exising shortcode with the same name. 
 
To delete an existing shortcode, click on delete.  Beware when deleting shortcodes as it occurs immediately without confirmation, and there is no undelete facility. 
 
To use a shortcode in a post or page on your blog just enter the shortcode in [ ], such as [myshortcode].

= How can I change the behaviour of the Configure Simple Shortcodes page? =

There are three options which can be changed to alter the behaviour of the Configure Simple Shortcodes page.  These can be altered using the form at the foot of the Configure Simple Shortcodes page. 
 
The options are: 
  
* *Shortcode Value Input Width*: The width of the shortcode value input field. Valid values are 1 to 999, but anything over 100 is probably not sensible. The default value is 45. 
* *Shortcode Value Input Height*: The height of the shortcode value input field. Valid values are 1 to 999, but anything over 20 is probably not sensible.  The default value is 1. 
* *Display HTML Markup*: Whether to display HTML Markup in the shortcodes values or not. Valid values are yes or no. The default value is no. 

 
Before saving, all of the options are checked to ensure that they are valid. Any options which are invalid are silently changed to their default values. 
 
As alternatives to yes or no, y t true 1 (or any number other than 0) and n f false 0 are accepted as alternatives to yes and no respectively. Any combination of case is also accepted, such as TrUe or fAlSe. All of the alternatives are translated to yes or no.

= Where can I get support? =

Support for Simple Shortcodes is available from http://michael-baker.com/simple\_shortcodes/support/ 

= What is the History of Simple Shortcodes? =

Version 0.2, Released 3 January 2009  
 
Following feedback and my initial experience of using Simple Shortcodes I've made the following changes: 
  
* Removed the 255 character limit on the size of simple shortcode values. 
* Added options to change the height and width of the simple shortcode value input field. 
* Added an option to display html markup in simple shortcodes on the configuration page. This will be particularly useful if any of your simple shortcodes contain unballanced html markup, such as a single </tr>. Why you'd want to do that I don't know, but you might. 
* Added a test to check that MB\_SS\_Handler.php can be opened for writing. This file, which contains the simple shortcode handler functions, needs to be re-written each time a simple shortcode is added, deleted or modified. 

 
Version 0.1  
 
For many years I've maintained the website of my friend, the world renowed marine artist, Gordon Frickers (http://frickers.co.uk/) . Initially I used ppwizard (http://dennisbareis.com/ppwizard.htm)  and wenost (http://wenost.sourceforge.net/)  to build the site. Among other things I used ppwizard to set up macros for commonly used text like phone numbers and Gordon's address. 
 
When I set up a WordPress Blog for Gordon (http://frickers.co.uk/blog/) , I wanted to be able use a similar mechanism for such commonly used text like phone and address. 
 
Simple Shortcodes was originally developed between Christmas and New Year in 2008.

= What is mb_ack? =

Simple Shortcodes comes with a pre-installed shortcode - [mb\_ack]. 
 
If you find Simple Shortcodes to be useful I would appreciate it if you could show your support by including the shortcode [mb\_ack] on one or more of your posts or pages.

== Screenshots ==

1. Configure Simple Shortcodes
