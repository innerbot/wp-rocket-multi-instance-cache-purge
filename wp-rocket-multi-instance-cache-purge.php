<?php
/*
Plugin Name: WP Rocket Multi Instance Cache Purge
Plugin URI: http://wordpress.org/extend/plugins/wp-rocket-multi-instance-cache-purge/
Description: Sets transient so that load-balanced servers receive cache purge requests across all instances
Version: 1.0
Author: Greg Johnson <greg@innerbot.com>
Author URI: http://innerbot.com/
License: http://www.apache.org/licenses/LICENSE-2.0
Text Domain: wp-rocket-multi-instance-cache-purge

Copyright 2016: Greg Johnson <greg@innerbot.com>

    This file is an extension to WP-Rocket, a premium plugin for WordPress available at:
    http://www.wp-rocket.me.

    WP Rocket Multi Instance Cache Purge is free software: you can redistribute it and/or modify
    it under the terms of the Apache License 2.0 license.

    WP Rocket Multi Instance Cache Purge is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/

// add_action('after_rocket_rrmdir', 'wprmicp_issue_purge_to_all_servers', 100, 2);

add_action('after_rocket_clean_domain', 'wprmicp_issue_purge_to_all_servers', 100);
add_action('after_rocket_clean_post', 'wprmicp_issue_purge_to_all_servers', 100, 3);
function wprmicp_issue_purge_to_all_servers($post = null, $purge_urls = null)
{
    global $wprmicp_doing_purge;

    $existing_purge_all_request = get_transient('wprmicp_purge_all_request');

    $transient_expiry = 15 * 60;

    if (!$wprmicp_doing_purge) {
        if (is_array($purge_urls)) {
                $current_purge_urls = get_transient('wprmicp_purge_url_request');
                $current_purge_urls = ( $current_purge_urls !== false ) ? $current_purge_urls : array();
                set_transient('wprmicp_purge_url_request',
                              array_merge($current_purge_urls, $purge_urls),
                              $transient_expiry
                            );
        } else {
            if ($existing_purge_all_request === false) {
                set_transient('wprmicp_purge_all_request', 1, $transient_expiry);
            }
        }
    }
}
