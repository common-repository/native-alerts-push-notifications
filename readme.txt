=== NativeAlerts Push Notifications ===
Contributors: adianttech
Tags: web-push, push notifications, ad networks, ads, adiant, advertising, affiliate, engagement, income, monetization, monetize,  pay per click
Requires at least: 4.9.8
Tested up to: 4.9.8
Requires PHP: 5.2.4
Stable tag: 1.2
License: GPLv3

== Description ==
NativeAlerts is a web push notification platform that allows publishers to easily and quickly implement web push technology on their websites. In addition to being able to invite your audience to view new posts, generate additional revenue by periodically sending sponsored offers via ad push notifications.

Web push technology provides a way for publishers to reach their audience, even when the browser is not in focus.

== Features ==
* Increase monetization and improve user engagement with NativeAlerts
* Provides single-step or two-step subscription method. The single step immediately prompts for subscription, whereas the latter gives you flexibility in adding your own custom message before users opt-in.
* Performance analytics/comparison between pushes.
* Measure user retention and attrition.
* Geo targeted ad push notifications.
* Target users by subscription age. Set up a separate schedule for newly subscribed users and those who have subscribed longer.
* Web push notification is supported by Chrome, Android, Firefox and Edge browsers.

== Installation ==
1. Upload 'nativealerts-push' to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You will need an API login and Password in order to use the plugin. Contact adops@nativealerts.com to get your API credentials.
4. When you get your credentials, login to the admin area and enter them into the NativeAlerts -> Settings section.
 

== Frequently Asked Questions ==
= How do I install the plugin? =
Install and activate the plugin on the Plugins page. Then go to the Settings section and enter your API credentials.

= How do I use the plugin? =
Once the plugin is installed, web push notifications will be installed and users who have not yet blocked push notifications on your domain will be prompted to allow push notification.

= Are there any other requirements for this plugin? =
PHP 4.9.8 or newer is required for this plugin to work. You will also need a NativeAlerts account. Contact adops@nativealerts.com to request API access.

= Does it modify how the content is rendered? =
No. The plugin does not visually change your page. The plugin imports a lightweight javascript worker file, publicly accessbile from https://api.nativealerts.com/worker.js, that enables web push notification prompt.

= How much is this service? Is there a monthly fee? =
This service is free of charge!

== About Third-Party, Privacy and Terms of Use ===
* Terms of Use. Usage of the plugin is subject to NativeAlert's Terms of Use. Please visit https://www.nativealerts.com/terms to view in its entirety.
* API Use and Server-to-Server requests. The plugin uses NativeAlert's API calls to https://api.nativealerts.com/ to populate the reports available from wp-admin. When a report is requested, a server-side https request is made to the API. See https://www.nativealerts.com/api for more information. This only occurs on the private wp-admin pages and does not apply to published posts.
* Ad notifications are delivered via the Adblade Platform, Adiant's Online Advertising Division. For more on Adblade's Privacy Policy, please visit https://www.adblade.com/doc/privacy

== Changelog ==
= 1.0 =
* Initial plugin

= 1.1 =
* Added ability to compose Push Messages based on Top 10 Recent posts
* Added "All" options on target segments and users
* Added calendar widget and time selection

= 1.2 =
* Added sortable columns in the reports
* Added Revenue report
* Added eCPM and CTR totals