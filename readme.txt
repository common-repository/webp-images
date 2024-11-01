=== WebP Images ===
Contributors: totalpressorg, andreadegiovine
Tags: webp, images, webp converter, images optimize, images compression
Donate link: https://totalpress.org/donate?utm_source=wordpress_org&utm_medium=plugin_page&utm_campaign=webp-images
Requires at least: 4.0
Tested up to: 6.3
Stable tag: 2.0.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Convert and compress images to WebP format easily. Speed ​​up your website.

== Description ==

Convert and compress **JPEG and PNG images to WebP** format easily. Speed ​​up your website.

**What else? What are you waiting for? Start now!**

Do you like the **WebP Images** plugin? Leave a [5-Star Review](https://wordpress.org/support/plugin/webp-images/reviews/?filter=5 "Write Review") to recommend it to other users.

== SUPPORT THE PROJECT ==

❤  **[WRITE A REVIEW](https://wordpress.org/support/plugin/webp-images/reviews/?filter=5 "Write Review")**
❤  **[SEND A DONATION](https://totalpress.org/donate?utm_source=wordpress_org&utm_medium=plugin_page&utm_campaign=webp-images "Send a donation")**
❤  **[BECOME PRO](https://totalpress.org/plugins/webp-images?utm_source=wordpress_org&utm_medium=plugin_page&utm_campaign=webp-images "Become PRO")** (support and get benefits)
❤  **[BECOME TRANSLATION CONTRIBUTOR](https://translate.wordpress.org/projects/wp-plugins/webp-images/ "Translations project page")**

== QUICK LINKS ==

* **[SUPPORT](https://wordpress.org/support/plugin/webp-images/ "Support page")**
* **[DOCUMENTATION](https://totalpress.org/docs/webp-images.html?utm_source=wordpress_org&utm_medium=plugin_page&utm_campaign=webp-images "Plugin documentation")**
* **[SEND SUGGESTIONS](https://totalpress.org/support?subject=WebP%20Images&utm_source=wordpress_org&utm_medium=plugin_page&utm_campaign=webp-images "Send your suggestions")**

== WebP Images requires ==

* Apache server / hosting;
* .htaccess file exists and writable.

== WebP Images features ==

Features included in the "WebP Images" plugin:

* bulk image conversion to webp format;
* bulk removal of images converted to webp;
* set conversion quality for webp format;
* auto conversion of uploaded images to webp format;
* compatible with any use of images (img, picture, background, etc.);
* the original images are not manipulated / deleted / edited;
* reduce frontend image size up to 80%.

== ⚡ WebP Images - PRO FEATURES ⚡ ==

✔ SET **CONVERSION QUALITY**
✔ **AUTO CONVERSION** FOR UPLOADS
✔ UNLIMITED **PRO UPDATES**
✔ **PRO SUPPORT** PRIORITY

== WebP Images development functions ==
`
function edit_max_images_per_group($images_per_group){
	$images_per_group = 20; // Edit this value to increase/decrease max images per group
	return $images_per_group;
}
add_filter('webp_images_bulk_max_elements','edit_max_images_per_group');
`

Add this code in the functions.php file to change the max number of images per group conversions. **Default 5**.

== WebP Images credits ==

The "WebP Images" plugin was entirely **designed and created by Andrea De Giovine**.
If you like the idea and want to support the developer, please [Donate to this plugin](https://totalpress.org/donate?utm_source=wordpress_org&utm_medium=plugin_page&utm_campaign=webp-images "send donation").
For **collaborations** and **consultations** visit the website of the [freelance web developer](https://www.andreadegiovine.it/?utm_source=wordpress_org&utm_medium=plugin_page_text&utm_campaign=webp_images "Web developer freelance").
For **bug reports** and **support for this plugin**, visit the [Support](https://wordpress.org/support/plugin/webp-images/ "Support") section to ask the developer and the community directly.

== Installation ==

To **automatically install** the "WebP Images" plugin you can search from the "Plugins > Add new" section of your WordPress dashboard and click on the "Install Now" button corresponding to this plugin.

To **manually install** the "WebP Images" plugin you can download the latest version from the WordPress.org site, and unpack the zip file in the "wp-content/plugins" folder of your CMS.

Now on the "Plugins" section of your WordPress dashboard you can see the "WebP Images" plugin, click on "Activate".

== Changelog ==

= 2.0.0 =
*2023-08-09*

* Remove cron conversion/deletion management;
* Improve UI;
* Fix to remove converted webp version on original media deletion;

= 1.0.6 =
*2023-07-28*

* Wp 6.2

= 1.0.5 =
* Reload after bulk process end.

= 1.0.4 =
* WP 5.8 compatibility.

= 1.0.3 =
* Fix bug.

= 1.0.2 =
* Fix bug
* Remove conversions limit
* Add quality setting
* Add Check status page

= 1.0.1 =
* Fix bug

= 1.0.0 =
* First release

== Frequently Asked Questions ==

= What are the requirements of the "WebP Images" plugin? =

The plugin works only with **Apache** servers and only if the **.htaccess** file exists and is writable.

= Does the "WebP Images" plugin improve the performance of my website? =

Yes! Speed ​​up your website by serving WebP images instead of standard formats. This new format reduces the size of the images (and therefore of the pages of your website) up to 80%.

= Does the "WebP Images" plugin save storage space? =

No! The plugin converts images (and its resizing) into WebP format, leaving the original files intact. Then on the server there will be the images, their resizing and the versions converted into WebP of the images and their resizing.