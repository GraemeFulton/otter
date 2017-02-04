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

<body class="single single-post single-format-standard">

  <section class="content-otter-main">
    <header>
        <h1 class="c-slate fontSize-xxl lineHeight-xxl breakPointM-fontSize-xxxl breakPointM-lineHeight-xxxl breakPointL-fontSize-xxxxl breakPointL-lineHeight-xxxxl fontWeight-5">Content Otter</h1>
        <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/otty-face.jpg';?>">
    </header>

   <?php 
   //include article importer template
   include( WP_PLUGIN_DIR.'/content-otter/otter/otter-article-importer/template/template-article-importer.php');
   ?>

  </section>
  <!-- content-otter-main -->

</body>
