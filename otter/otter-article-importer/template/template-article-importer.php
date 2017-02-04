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
    <div id="overlay">
      <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/otter-insert.gif';?>">
    </div>
    <!-- Form to handle the upload - The enctype value here is very important -->
    <section class="user-input">
        <p>Paste Article URL:</p>
        <input type="text" id="urlinput" name="fname" placeholder="Paste article URL"><br>
        <br>
       <button class="btn" id="nbs-ajax-get">
         Fetch Article
       </button>

      <!-- Show our loading otter when the article is being fetched -->
      <div id='loading-otter'>
        <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/loading-otter.gif';?>">
        <h3>I'm ottering your content!</h3>
      </div>

    </section>


      <!-- insert content section -->
      <section class="insert-draft otter-input marginb-20">
          <hr>
           <p>Content Otter Preview:</p>
           <!--title goes here -->
           <h1 class="title c-slate fontSize-xxl lineHeight-xxl breakPointM-fontSize-xxxl breakPointM-lineHeight-xxxl breakPointL-fontSize-xxxxl breakPointL-lineHeight-xxxxl fontWeight-5"></h1>
           <!-- post content goes here -->
            <div class="ottered-content">
            </div>


          <!-- insert draft button -->
          <section class="insert-draft padding-top-15">
            <hr>
            <button class="btn" id="nbs-ajax-save">Import as draft</button>
            <img src="<?php echo plugin_dir_url( __FILE__ ) .'/images/otter-done.gif';?>">
          </section>

      </section>
      <!-- insert content section -->

</body>
