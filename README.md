wordpress-datalayer
===================

Expose they key data on a page within a JavaScript API.


# What is this for?

More and more web applications and plugins these days are pure JavaScript, working in the browser without any kind of server-side communication.

What this means however, is that by default, they don't have the data they require available for them to use. This library makes that happen.


# How does it work

The library gets all the key information about a page from your Wordpress database and returns it in a JSON encoded format. This data can then be set as whatever global you wish within your theme/plugin.
