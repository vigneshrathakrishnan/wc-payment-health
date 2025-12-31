=== WC Payment Health ===
Contributors: vikee
Tags: woocommerce, payments, analytics, gateways, monitoring
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Monitor WooCommerce payment success and failure rates across gateways with CSV import/export support.

== Description ==

**WC Payment Health** helps store owners monitor the health of their WooCommerce payment gateways by tracking successful and failed payment outcomes.

The plugin records final payment outcomes (success or failure) for both online and offline payment methods and presents a simple admin dashboard showing gateway-level statistics.

This plugin is designed as a lightweight MVP with a focus on clarity, correctness, and minimal performance impact.

### Key Features

* Tracks successful and failed payments per gateway
* Supports online and offline payment methods
* Final-outcome based tracking (no noisy intermediate states)
* Admin dashboard with gateway-level statistics
* CSV export and import of payment health data
* Safe, bounded storage using WordPress options
* No external services or tracking

### What This Plugin Does NOT Do

* It does not replace payment gateways
* It does not intercept payment processing
* It does not track customer or personal data
* It does not modify WooCommerce orders

== Installation ==

1. Upload the `wc-payment-health` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Navigate to **WooCommerce → Payment Health** to view statistics.

== Frequently Asked Questions ==

= Does this plugin store personal customer data? =

No. The plugin stores only aggregated payment outcome data such as gateway name, payment result, and timestamp.

= Does this plugin work with offline payments like bank transfer or cheque? =

Yes. Offline payments are tracked when the order reaches a final successful or failed state.

= Will this plugin slow down my store? =

No. The plugin stores a limited number of records and does not run heavy queries or background processes.

= Can I export my data? =

Yes. Payment health data can be exported and imported using CSV files from the admin interface.

== Screenshots ==

1. Payment health dashboard showing gateway success and failure rates.
2. CSV export and import actions in the admin interface.

== Changelog ==

= 0.1.0 =
* Initial MVP release
* Payment success and failure tracking
* Admin dashboard with gateway statistics
* CSV import and export
* Offline payment support

== Upgrade Notice ==

= 0.1.0 =
Initial release.
