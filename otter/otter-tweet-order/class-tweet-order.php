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
        
        /************************************************************
  *                                                         *
  *****  LIST OF FUNCTIONS TO ACCESS TWEET INFORMATION  *****
  *                                                         *
  ************************************************************/

  /**
    *
    * Print out Associative Array containing tweets from given user
    *
    **/
   private function tweetArray($tweets) {
        // Assure Twitter API connection
        $this->checkError($tweets);

        // Initialize array to hold tweets
        $newTweets = array();

        // Exclude retweets from being pushed
        foreach($tweets as $tweet) {
            array_push($newTweets, $tweet);
        }
        // Print out associative array of tweets
        echo "<pre>";
        print_r($newTweets);
        echo "</pre>";
    }

   /**
     *
     * Check if Tweet was liked
     *
     **/
   private function tweetLikes($tweets) {
        // Assure Twitter API connection
        $this->checkError($tweets);

        foreach($tweets as $tweet) {
            if ($tweet["favorite_count"] > 0) {
                echo 'This tweet was liked ' . $tweet["favorite_count"] . ' time(s)!';
            }
            else {
                echo "This tweet was not liked!";
            }
            echo "<br />";
        }
    }

    /**
      *
      * Check if Tweet is a retweet
      *
      **/
     private function isRetweet($tweet) {
          if ($tweet['retweeted_status']) {
              return true;
          }
          else {
              return false;
          }
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