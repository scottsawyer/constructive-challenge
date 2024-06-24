# Constructive Challenge

## Setup

Both WordPress and Drupal installs run on [Lando](https://lando.dev/).  A complete database export of each are provided in their respective root directories.

### Turn on WordPress

```
cd /wordpress/bedrock
lando start
lando db import ./wordpress_database.sql
```
Visit (https://bedrock.lndo.site).

*User name:* admin

*Password:* f%StRLojvPZpjx*CjJ


### Turn on Drupal

```
cd /drupal/drupal
lando start
lando db-import ./drupal_database.sql
```
Visit (https://cdrupal.lndo.site).

*User name:* admin

*Password:* UKYTEvL59w@7j*@E


Everything should just work!

*NOTE* I haven't tried a fresh install from the repo :)

## WordPress plugin.

*constructive-api*

Super simple plugin that just exposes the latest ten published posts at /wp-json/constructive-api/v1/posts.  There is no authentication.  It just exposes the post id, title, published date, and body.

In the real world, the plugin is probably unnecessary, we could just request something like /wp-json/wp/v2/posts?per_page=10.

## Drupal module.

*constructive_remote_data*

This module is a bit more sophisticated.  The service \Drupal\constructive_remote_data\ConstructiveApi handles the request and caches the result.  It's just a static cache tag that invalidates after 900s.  You can view the results at /remote-posts.

I used (Views Remote Data)[https://www.drupal.org/project/views_remote_data] to exposed the API data to Views.  The module is co-maintained by mglaman, who is well-known and respected in the Drupal community, particularly for his work on Drupal Commerce, so I have a high degree of confidence in this module. It provides a views plugin and emits an event that, when subscribed to (after altering the views base table), allows injecting the data from our API request.  While reading the documentation, I found a test module that demonstrated a way to use a node completely in memory, so never writes to the database, pretty slick.  The documentation was quite good and it was fun to set up. 

I created the template override so I could add the `<hr>` after each row (the default template already wraps each row in a `<div>`).  However, since I left the default theme Olivero, which has an interesting styling choice, the `<hr>` is not very visible, but you can see it by inspecting the rendered markup.   

The content type (remote_post), fields, and view configuration is exported to the module config/install directory, so if we wanted to use this module on another site, we should be able to just install it and everything would work.

While the solution of generating nodes in memory is interesting, I think we'd need some performance testing to ensure it scales well.  

A simpler solution with less dependencies might have been to just create a controller to display the list of API results.  This might be more performant, we no longer need to generate nodes in memory, and avoid altering views.  There would be drawbacks, such as, with the views approach we get a lot more control in the UI (display modes, changing the format of the views results, and if we had images, we should be able to use image styles).

This might be done with just core / JSON:API by having the WordPress side send a request when a post is changed.  We'd have to actually store the post data in the Drupal database, set up authentication, etc.  I like our solution better, less work, probably better architecture to only store data in one place.

Another option might be to aggregate a feed from the WordPress site, using a module like [Feeds](https://www.drupal.org/project/feeds).
