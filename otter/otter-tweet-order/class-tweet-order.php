<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


Class Otter_Tweet_Importer
{
     private $plugin_name = 'content-otter-tweet';
    private $version = '1.0';
    
	public function __construct() {
		// Load style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );  
                
                //ajax hooks for otter importer
		add_action('wp_ajax_otter_get_tweets',  array($this, 'get_tweets'));
                add_action('init', array($this,'customRSS'));
        }
        
        public function customRSS(){
            add_feed('content-otter', array($this,'customRSSFunc'));
            //Ensure the $wp_rewrite global is loaded
        }

        public function customRSSFunc(){
header('Content-Type: '.feed_content_type('rss-http').'; charset='.get_option('blog_charset'), true);?>
<?php echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';?>
<rss version="2.0"
        xmlns:content="http://purl.org/rss/1.0/modules/content/"
        xmlns:wfw="http://wellformedweb.org/CommentAPI/"
        xmlns:dc="http://purl.org/dc/elements/1.1/"
        xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
        xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
        <?php do_action('rss2_ns'); ?>>
<?php include(WP_PLUGIN_DIR."/content-otter/otter/otter-tweet-order/template/rss-template.xml")?>
            <?php
        }


          
        public function enqueue_scripts(){
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/content-otter-tweet.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( $this->plugin_name.'-moment-js', plugin_dir_url( __FILE__ ) . 'js/moment.min.js', array( 'jquery' ), $this->version, false );
        }
    	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/content-otter-tweet.css', array(), $this->version, 'all' );

	}          
        
        public function get_tweets(){
            // Configuration
            include_once plugin_dir_path(__FILE__) . 'includes/config.php';
            
            // Construct URL
            $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
            $getfield = '?screen_name=' . $_POST['name'] . '&count=3200' . '&include_rts=false'.'&exclude_replies=true';
            $requestMethod = 'GET';
            // Make authenticated call to Twitter API
            $twitter = new TwitterAPIExchange($settings);
            
            // store returned JSON in associative array
            $tweets =  json_decode($twitter->setGetfield($getfield)
                                       ->buildOauth($url, $requestMethod)
                                       ->performRequest(), $assoc = TRUE);            
            
            
            $ordered_tweets = $this->order_tweets($tweets);
            
            $this->updateRSS($ordered_tweets);
            
            
            echo json_encode($ordered_tweets);
            wp_die();
            
        }
        
        private function order_tweets($tweets){
            
           // Assure connection to Twitter API
            $this->checkError($tweets);

            // Iterate through tweets to find the Top Tweet!
            $favs = array();
            $rts = array();
            //sort by 2 things: http://stackoverflow.com/questions/4582649/php-sort-array-by-two-field-values
            foreach ($tweets as $key => $row)
            {
             // Add # of likes to sorted array
             $favs[$key] = $row['favorite_count'];
             // add # of retweets to sorted array
             $rts[$key] = $row['retweet_count'];

            }

            // Sort array in descending order based on likes
            //array_multisort($favs, SORT_DESC, $tweets);   
            
            // Sort the data with volume descending, edition ascending
            array_multisort($rts, SORT_DESC, $favs, SORT_DESC, $tweets);
            
           return $tweets; 
        }
        
        private function updateRSS($tweets){
            //include og data grabber
            include(WP_PLUGIN_DIR."/content-otter/otter/common-libs/og-data/og-data.php");
            //create og_data object
            $OG = new OG_Data();
            
            
            $file = 'test.xml';  // (Can write to this file)
            $file = WP_PLUGIN_DIR."/content-otter/otter/otter-tweet-order/template/rss-template.xml"; 
            
            $rss_header = WP_PLUGIN_DIR."/content-otter/otter/otter-tweet-order/template/rss-header-template.php"; 
            $rss_body = WP_PLUGIN_DIR."/content-otter/otter/otter-tweet-order/template/rss-template-body.php"; 

            // Open the file to get existing content
            ob_start();
            include $rss_header;
            $rss = ob_get_clean();
            $count=0;
            //loop through and create rss
            foreach($tweets as $tweet){
                if($count<11){
                $og = $OG->get_og_data($tweet['entities']['urls'][0]['expanded_url'] );    
                // Open the file to get existing content
                ob_start();
                include $rss_body;
                $rss.= ob_get_clean();
                }
                $count++;
            }
            $rss.='</channel></rss>';
            // Write the contents back to the file
            file_put_contents($file, $rss);
            //update rss by flunsing wp rewrite
            global $wp_rewrite;
            //Call flush_rules() as a method of the $wp_rewrite object
            $wp_rewrite->flush_rules( false );
        }


    /**
      *
      * Assure Twitter API Connection or yell errors
      *
      **/
   private function checkError($tweets) {
        if ($tweets["errors"][0]["message"] != "") {
            echo "<h3>Sorry, there was a problem.</h3>
            <p>Twitter returned the following error message:</p>
            <p><em>". $tweets[errors][0]["message"] . "</em></p>";
            exit();
        }
    }

}