<?php
/*
Plugin Name: Prototypr OG Thumbnail
Plugin URI: http://prototypr.io
Description: Landing page wordpress action hooks
Version: 1.0
Author: Graeme Fulton
Author URI: http://gfulton.me.uk
*/

Class OG_Data{
    //first get the image url to grab the opengraph image data from
public function get_og_data($link){
  
            //the first url will be the article link
            $longURL = get_headers($link, 7);
            $url = $longURL['Location'];
           
            //and add the post image using plugin hook from prototypr-og-data
            return $this->otter_get_open_graph_image($url );
            
    

 }
 
    //get the image from opengraph
    private function otter_get_open_graph_image($link)
   {
       $otter=array();

       $url = $link;

       include('libs/OpenGraph.php');
       $graph = OpenGraph::fetch($url);
        foreach ($graph as $key => $value) {

               if($key =='title'){
                   $otter['title']= $value;
               }
               if($key=='image'){
                     $otter['image'] = $value;
               }
               if($key =='url'){
                     $otter['url'] = $value;
               }
        }

        return $otter;

}

    
}
?>
