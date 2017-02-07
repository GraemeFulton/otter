<?php
/*
Plugin Name: Prototypr OG Thumbnail
Plugin URI: http://prototypr.io
Description: Landing page wordpress action hooks
Version: 1.0
Author: Graeme Fulton
Author URI: http://gfulton.me.uk
*/
include('libs/OpenGraph.php');
Class OG_Data{
    //first get the image url to grab the opengraph image data from
public function get_url($link){
             //the first url will be the article link
            $headers = get_headers($link, true);
            $link =$headers['Location'];
            
            //swap ampersand
            $url = str_replace('&', '&amp;', $link ); 
            //swap https for http
            $url = str_replace('https://', 'http://', $url ); 
            
            if(is_array($url)){
                $link= $url[0];
            }
            else{
              $link= $url;
            }
            return $link;
}    
    
    
public function get_og_data($url){
           
            //and add the post image using plugin hook from prototypr-og-data
           return $this->otter_get_open_graph_image($url );
 }
 
 
    //get the image from opengraph
    private function otter_get_open_graph_image($link)
   {
       $otter=array();

       $url = $link;


        $graph = OpenGraph::fetch($url);
        foreach ($graph as $key => $value) {

               if($key =='title'){
                   $otter['title']= $value;
               }
               if($key=='image'){
                     $otter['image'] = $value;
               }
               if($key =='url'){
                     $otter['link'] = $value;
               }
        }

        return $otter;

}

    
}
?>
