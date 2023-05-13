# Accept Bitcoin and 50+ Cryptocurrencies with Bronos Pay

Contributors: BronosPay
Donate link: https://payments.bronos.org
Tags: bronos-pay, bitcoin, accept bitcoin, litecoin, ethereum, ripple, monero, bitcoin cash, bcash, altcoins, altcoin, accept altcoin, bitcoin processor, bitcoin gateway, payment gateway, payment module, bitcoin module, bitcoin woocommerce, btc
Requires at least: 5.3
Tested up to: 6.0
Stable tag: trunk
License: MIT
License URI: https://github.com/bronos-pay/woocommerce-plugin/blob/master/LICENSE

## Description

The easiest and quickest way to accept Bitcoin, Litecoin, Ethereum, Doge and other cryptocurrencies. Receive payouts in Bitcoin and other cryptocurrencies, Euros or U.S. Dollars without exchange rate volatility risk! Made by Bronos Pay

**_BRONOS PAY CRYPTOCURRENCY PAYMENT GATEWAY FOR WOOCOMMERCE_**

Start accepting Bitcoin and other cryptocurrencies in no time! Our official WooCommerce module connects your store to our **_fully automated cryptocurrency processing platform and invoice system_**, giving you access to the rapidly emerging blockchain market.

With Bronos Pay, online businesses can **_accept Bitcoin, Litecoin, Ethereum, Doge_** as well as 70+ other digital currencies (Altcoins) hassle-free. Payments received from customers can be instantly converted to Fiat, and you can receive **_payouts in Euros and U.S. Dollars_** directly to your bank account. Alternatively, store owners can choose to receive **_payouts in Bitcoin_** and other cryptocurrencies such as **_Ethereum, Ripple_** and more!

**_START ACCEPTING BITCOIN IN MINUTES!_**

The extension allows stores that use WordPress WooCommerce shopping cart system to accept Bitcoin payments as well as Altcoins via the Bronos Pay API. Integration and configuration of our WooCommerce module is super simple, while the solution you get is fully automated – you will receive automatic payment confirmations and order status updates.
To find out more about the benefits of Cryptocurrencies for your business, as well as the solution offered by Bronos Pay, [check our website](https://payments.bronos.org).

### Features

- The gateway is fully automatic – set it and forget it.
- Payment amount is calculated using real-time exchange rates.
- Simplicity for you, flexibility for your customers. On our invoices, a customer can choose to pay with Bitcoin, Litecoin, Lightning Network, Ethereum and 70+ other cryptocurrencies at checkout, while your payouts are in a single currency of your choice - EUR/USD, BTC or other cryptocurrencies.
- [Sandbox environment](https://sandbox.bronos.org/) for testing with Tesnet Bitcoin.
- No setup or recurring fees.
- No chargebacks – guaranteed!

### Functionality

- Extend your invoice expiration time (if payouts are in BTC).
- Accept slight underpayments automatically.
- Refunds can be issued directly from the invoice – without involvement of the seller.

### How it works - example

1. An item in the store costs 100 euro.
2. A customer wants to buy the item and selects to pay with Bitcoin.
3. An invoice is generated and, according to the current exchange rate, the price is 50,000 euro per bitcoin, so the customer has to pay 0.002 bitcoins.
4. Once the invoice is paid, the merchant gets a notification and is credited 99 euro (100 euro minus our 1% flat fee), or 0.00198 BTC.

To be able to use the plugin you have to create an account on https://payments.bronos.org

## Installation

First and foremost, you will need to sign up for an account on Bronos Pay. For production, use our main website: [https://payments.bronos.org/](https://payments.bronos.org). But before you go live, you may want to do some testing, to make sure everything works nicely. For this purpose, we have a sandbox environment: [https://sandbox.bronos.org](https://sandbox.bronos.org/) – just remember to create an account here even if you already have one on our main website.

The installation of our WooCommerce payment module is quite easy. In order to ensure that Bronos payment gateway is working properly on your website, we will go over these two quick steps:

- Setup API credentials on Bronos Pay.
- Install the Bronos Pay payment module for WooCommerce.

For the testing mode to work, you must generate separate API credentials on [https://sandbox.bronos.org](https://sandbox.bronos.org), since API credentials generated on [https://payments.bronos.org](https://payments.bronos.org) will not work in the sandbox environment. To create a set of API credentials, log in to your Bronos Pay account, and either complete the auto-setup wizard, or find the API tab on the menu – click “Apps”, and then click “+New App”.

_Please note, this guide was created using WordPress 4.7.4, but is also useful for installing older or upcoming versions._

1. Login to your WordPress admin panel and go to Plugins > Add New.
2. In the Search Plugins field, type in “bronos pay”. When the Bronos Pay for WooCommerce plugin is displayed, click “Install Now” (if asked, enter your FTP credentials).
3. After the plugin is installed, click “Activate”.
4. Go to WooCommerce > Settings > Payments > Method : Bronos Pay – Cryptocurrencies via Bronos Pay (more than 50 supported)” and then click the check box “Enabled”. After Enabled is clicked then press on Bronos Pay on the same page. If needed, change the Description and the Title according to your preferences.
5. Enter your API credentials on the WooCommerce configuration page.
6. If you are using Sandbox API credentials, then turn Test Mode on.
7. Finally, click “Save changes”, and you are good to go!
