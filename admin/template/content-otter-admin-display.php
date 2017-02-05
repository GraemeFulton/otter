<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */

?>
<!-- Include marvel stylesheets -->
<link rel="stylesheet" href="https://blog.marvelapp.com/wp-content/themes/marvel/style.css" type="text/css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Bitter" type="text/css">

<!-- Create a header in the default WordPress 'wrap' container -->
<div class="wrap">
    
        <h2><span class="dashicons dashicons-palmtree"></span> Content Otter</h2>
      <?php settings_errors(); ?>
         
        <?php
            if( isset( $_GET[ 'tab' ] ) ) {
                $active_tab = $_GET[ 'tab' ];
            } // end if
        ?>        
        
<h2 class="nav-tab-wrapper">
    <a href="?page=content-otter&tab=content_import" class="nav-tab <?php echo $active_tab == 'content_import' ? 'nav-tab-active' : ''; ?>">Content Import</a>
    <a href="?page=content-otter&tab=popular_tweets" class="nav-tab <?php echo $active_tab == 'popular_tweets' ? 'nav-tab-active' : ''; ?>">Popular Tweets</a>
</h2>
        
        <?php
        
         if( $active_tab == 'content_import' ) {
                
             include( WP_PLUGIN_DIR.'/content-otter/otter/otter-article-importer/template/template-article-importer.php');

        } else {
            
            include( WP_PLUGIN_DIR.'/content-otter/otter/otter-tweet-order/template/template-tweet-order.php');

        } // end if/else
        ?>

</div>