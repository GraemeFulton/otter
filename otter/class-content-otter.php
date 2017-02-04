<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once plugin_dir_path(__FILE__) . 'otter-article-importer/class-article-importer.php';

class Content_Otter {
    
            
        /*
         * Content Otter Importer
         */
        private $Otter_Importer;
                
        /*
         * Content Otter Importer
         */
       
        private $Otter_Tweeter;

       
	public function __construct() {
            $this->Otter_Importer = new Otter_Article_Importer();
        }
        

    
}