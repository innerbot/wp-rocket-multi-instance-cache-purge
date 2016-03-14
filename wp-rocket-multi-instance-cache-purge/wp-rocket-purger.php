<?php
if( php_sapi_name() !== "cli" ) exit;

// find and init wordpress sans theme support
function find_wordpress_base_path() {
    $dir = dirname(__FILE__);

    do {
        //it is possible to check for other files here
        if( file_exists($dir."/wp-config.php") ) {
            return $dir;
        }
    } while( $dir = realpath("$dir/..") );

    return null;
}

define( 'BASE_PATH', find_wordpress_base_path() . "/" );

define( 'WP_USE_THEMES', false );

require_once( BASE_PATH . '/wp-load.php' );

function wprmicp_purge_check($all = false)
{
    // hack
    global $wprmicp_doing_purge;
    $wprmicp_doing_purge = true;

    if ($all) {
        $pending_purges = (int) get_transient('wprmicp_purge_all_request');
    } else {
        $pending_purges = get_transient('wprmicp_purge_url_request');
    }

    if ($pending_purges === 1) {
        echo "WP-ROCKET MICP: Cache Purge Request found, dumping cache\n";
        rocket_clean_domain();
        echo "WP-ROCKET MICP: Cache purge completed.\n";
    } elseif (is_array($pending_purges) && !empty($pending_purges)) {
        echo "WP-ROCKET MICP: " . sizeof($pending_purges) . " Urls to purge.\n\t";
        rocket_clean_files($purge_urls);
        foreach ($pending_purges as $purge) {
            echo "DELETED: $purge FROM CACHE\n\t";
        }
    } else {
        echo "WP-ROCKET MICP: No pending purge requests were found\n\n";
        exit;
    }
    echo "\n\nWP-ROCKET MICP: JOB COMPLETE.\n\n";
}
echo "WP-ROCKET MICP: Processing individual cached page purge requests...\n";
wprmicp_purge_check();

echo "\n\nWP-ROCKET MICP: Checking for Cache Purge Requests\n";
wprmicp_purge_check(true); // true == delete all from cache
