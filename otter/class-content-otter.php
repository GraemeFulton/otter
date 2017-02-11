<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once plugin_dir_path(__FILE__) . 'otter-article-importer/class-article-importer.php';
include_once plugin_dir_path(__FILE__) . 'otter-tweet-order/class-tweet-order.php';
include_once plugin_dir_path(__FILE__) . 'otter-email-template/class-email-template.php';

class Content_Otter {
    
            
        /*
         * Content Otter Importer
         */
        private $Otter_Importer;
                
        /*
         * Content Otter Importer
         */
       
        private $Otter_Tweeter;
        
        private $Otter_Email;

       
	public function __construct() {
            $this->Otter_Importer = new Otter_Article_Importer();
            $this->Otter_Tweeter = new Otter_Tweet_Importer();
            $this->Otter_Email = new Otter_Email_Template();
           // add_action( 'plugins_loaded', array( 'Otter_Email_Template', 'get_instance' ) );
        }
        

    
}