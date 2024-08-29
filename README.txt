=== Wbcom Designs - BuddyPress Friend & Follow Suggestion ===
Contributors: wbcomdesigns
Donate link: https://wbcomdesigns.com/
Tags: friends, follow, suggestion, buddypress, profile
Requires at least: 3.0.1
Tested up to: 6.6.2
Stable tag: 1.5.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The BuddyPress Friends and Follow suggestions plugin assists you with improving your BuddyPress or BuddyBoss Platform-based community website by providing profile-matching features to your users. It shows profile matching percentages in the member header area.

== Description ==

With BuddyPress Friends and follow suggestion plugin you will have the ability to set profile matching criteria with the percentage, based upon that matching percentage will be calculated and reflected on the member's profile header.

You’ll also have a widget showing the list of users whose profiles match yours. You can add them as friends or follow them directly from the widget. If you are searching for a plugin that works similar to Facebook’s ‘People you may know’ widget, then you are at the right place.



== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `buddypress-friend-follow-suggestion.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= Can I use it with BuddyBoss Platform Plugin? =

Yes! The plugin is compatible with the BuddyBoss platform.

= Can I manage the number of users displayed on the widget? =

Yes! You can set the number of users displayed By navigating to Appearance >> Widgets >> BuddyPress friend and Follow Suggestion.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screenshot


== Changelog ==
= 1.5.0 =
* Fix: Resolved follow issue and friendship request withdrawal in Swiper slider with BuddyBoss using drag.
* Fix: Addressed UI issues, including member counts on reload, text domain, cancel button, and static text appearance on the horizontal layout.
* Update: Updated JS library and documentation link.
* Enhancement: Managed default slide UI and follow button icon with BuddyBoss theme.
* Fix: Resolved exclude user issue and managed Swiper widget layout two UI fixes.
* Enhancement: Added class in Swiper slider and managed layout of two UI fixes.
* Performance: Optimized member list rendering by reducing redundant function calls, caching values, and improving conditional logic.
* Enhancement: Removed unnecessary BuddyPress check and improved flexibility in bp_suggestions_user_status with HTML output and filter hook.
* Improvement: Optimized bp_suggestions_get_matched_users function for large communities.
* Fix: Resolved set transient issue for left swipe users.
* Fix: Managed notification UI issue with Reign theme and BuddyBoss platform/theme UI issues.
* Fix: Resolved right swipe and like/dislike highlight color issues.
* Fix: Add friend request issue resolved and follow suggestion removed from slider widget.
* Cleanup: Removed old codes and managed no suggestion found UI.
* Enhancement: Set Discover, Message, and Profile links in the Swiper widget.
* Fix: Managed horizontal Swiper slider UI and profile matching percentage in SocialV theme.
* Enhancement: Added Swiper slider script for like/dislike functionality and new layout widget with Swiper.
* Fix: Addressed license issue where deactivation response failed.
* Fix: Resolved blank widget issue on max member option and managed Swiper/horizontal layout arrows.
* Enhancement: Added navigation and AJAX functionality for follow/friendship buttons in widgets.
* Fix: Managed follow and friendship icons and layout UI.
* Enhancement: Introduced new UI for Swiper layout and updated user icon.

= 1.4.9 =
* Fix: (#74)Fixed Video UI issue with BB
* Fix: (#70) Fixed issue with php 8.2

= 1.4.8 =
* Updated: Video on admin section
* Updated: Admin faq hover color
* Updated: Ads banner URL
* Updated: Admin label and setting title and video
* Fix: Text domain issue
* Fix: BP v12 fixes
* Fix: License issue

= 1.4.7 =
* Fix: PHPCS fixes
* Fix: PHPCS nonce fixes

= 1.4.6 =
* Fix: (#63) Issue with suggestion widget.
* Added: (#61) Hookable position to remove admin user from suggestion widget.

= 1.4.5 =
* Fix: (#58)Fixed unable to save profile fields with multisite setup

= 1.4.4 =
* Fix: (#54) Added buddypress component check and admin notice
* Fix: Fixed Plugin redirect issue when multi plugin activate the same time

= 1.4.3 =
* Fix: #50 - Deprecated warning
* Fix: (#52) Fixed widgets console error
* Fix: Admin notice error

= 1.4.2 =
* Fix: Button hover bg color and button alignment
* Fix: remove admin notice

= 1.4.1 =
* Fix: Fixed phpcs fixes
* Fix: Update admin wrapper UI

= 1.4.0 =
* Fix: managed Cancel Friendship tooltip
* Fix: Added default option
* Fix: Fixed array offset issue
* Fix: (#45) Follow button align
* Fix: (#30) Add layout setting vertical and horizontal layout
* Fix: Added widget layout template
* Fix: (#41)Fixed php error
* Fix: Add option in widget
* Fix: add swiper slider js and css
* Fix: (#29) friend follow button add tool tip
* Fix: (#44) Added RTL support

= 1.3.1 =
* Fix: (#25) Managed widget UI with buddyboss
* Fix: (#23) Managed widget UI in single member and group
* Fix: (#06) Fixed notice and warning error

= 1.3.0 =
* Fix: #17 - Update friends, follow widget UI
* Fix: #14 - Console error "bp is not defined"
* Fix: #19 - Suggestions starting percentage error
* Fix: #12 - notice is not displaying when BuddyPress is not activate
* Fix: #20 - Fixed phpcs errors

= 1.2.1 =
* Fix: Managed Backend UI

= 1.2.0 =
* Fix: Managed UI with buddypress 8.0.0
* Fix: #9 - Fatal Error: When friend component is not activated
* Fix: #10 - Show a message "No suggestion found"
* Fix: Update generic button UI

= 1.1.0 =
* Fix: (#5) Fixed follow button not working with buddyboss

= 1.0.0 =
* Initial Release
