# [WordPress Pushbots](http://www.sej-ko.dk/)

Let [PushBots](https://pushbots.com) push your WordPress posts
## Purpose
[PushBots](https://pushbots.com) is a neat, highly integratable, service to send push notifications to your app, whether it be iOS, Android or Chrome.
When WordPress is the backbone of your data, this WP plugin keeps your app's users posted. Receive push notification when a new post is published, with a customized payload.
### ToDo
* Push when comments are made...
## Pre-Installation
Setup your app with PushBots. Make sure it works, and test your payload by using the composer on the PushBots dashboard
## Installation
1. Install this plugin the WP way
2. Activate it
3. Go to: Settings / WordPushBots
4. Enter your PushBots App-ID and -secret
5. Now configure your notifications..
## Configure Push Notifications
### Notification's content
Define the alert-message:
* Post's title
* Post's content (140 chars)
* Custom text (available variables: $author, $title)
### Target
Narrow down your audience: Push notification are received by everyone..
* With pre-defined alias
* Tagged with one of post's categories (slugs)
* Tagged with one of post's tags (slugs)
* Tagged with pre-defined tags
### Payload
In your payload you can post:
* Post's ID or slug
* Post's categories IDs or slug
* Post's tags IDs or slug
You can specify the key you're sending, for example `post_item` or `post_categories`.
You can specify the value you're sending with either ID or slug.
A result could for example become: `{ "post_item": 8343 , "post_categories": [ 25, 10, 11 ] }` or `{ "post_id": 8343 , "post_cats": [ "sale", "sport" ] }`