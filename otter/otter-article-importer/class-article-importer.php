<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Otter_Article_Importer
{
    private $plugin_name = 'content-otter-import';
    private $version = '1.0';
    
	public function __construct() {
		// Load style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );  
                
                //ajax hooks for otter importer
		add_action('wp_ajax_otter_fetch_post',  array($this, 'otter_fetch_post'));
		add_action('wp_ajax_otter_save_post',  array($this, 'otter_save_post'));
        }
        
 	/**
	* Get the posts
	*/
	public function otter_fetch_post() {
            $json_data = $this->get_article($_POST['site']);
		echo json_encode($json_data);
		wp_die(); // this is required to return a proper result
	}


	/**
	* Save the post and images as draft article when save button pressed
	*/
	 public function otter_save_post() {
             
                 $this->save_post($_POST);
         }       
        
        
        
        public function enqueue_scripts(){
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/content-otter-importer.js', array( 'jquery' ), $this->version, false );
        }
    	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/content-otter-importer.css', array(), $this->version, 'all' );

	}    
        
        
	/**
	 * Clean the article tags, and use Readability to grab the article
	 */
	public function get_article($url)
	{
		
		include_once plugin_dir_path(__FILE__) . 'libs/Readability.php';
		
		// (change this URL to whatever you'd like to test)
		$html = file_get_contents($url);
		
		$html = $this->remove_divs_with_class($html, 'section-divider');
		//$html = $this->remove_divs_with_class($html, 'section-content');
		
		//only allow these tags
		$html = strip_tags($html, '<a><b><article><header><blockquote><code><dd><dl><dt><em><caption><figure><aside><q><figcaption><iframe><h1><h2><h3><h4><i><img><li><ol><p><pre><s><sup><sub><strong><section><strike><ul><br><span><svg><button><table><form>');
		
		// If we've got Tidy, let's clean up input.
		// This step is highly recommended - PHP's default HTML parser
		// often doesn't do a great job and results in strange output.
		if (function_exists('tidy_parse_string'))
		{
			$tidy = tidy_parse_string($html, array(), 'UTF8');
			$tidy->cleanRepair();
			$html = $tidy->value;
		}
		
		// give it to Readability
		$readability                          = new Readability($html, $url);
		// print debug output?
		// useful to compare against Arc90's original JS version -
		// simply click the bookmarklet with FireBug's console window open
		$readability->debug                   = false;
		// convert links to footnotes?
		$readability->convertLinksToFootnotes = false;
		
		// process it
		$result     = $readability->init();
		$json_array = array();
		if ($result)
		{
			//print the title
			$title               = $readability->getTitle()->textContent;
			//push title to json array
			$json_array['title'] = $title;
			//grab the content
			$content             = $readability->getContent()->innerHTML;
			// if we've got Tidy, let's clean it up for output
			if (function_exists('tidy_parse_string'))
			{
				$tidy = tidy_parse_string($content, array(
					'indent' => true,
					'show-body-only' => true
				), 'UTF8');
				$tidy->cleanRepair();
				$content = $tidy->value;
			}
			//remove any classes and IDs from the HTML
			$content = preg_replace('/class=".*?"/', '', $content);
			$content = preg_replace('/name=".*?"/', '', $content);
			$content = preg_replace('/id=".*?"/', '', $content);
			$content = preg_replace('/readability=".*?"/', '', $content);
			
			$DOM = new DOMDocument();
			$DOM->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
			
			//add marvel classes to the html (comment this out to keep html plain)
			//$DOM = $this->add_marvel_html_classes($DOM);
			
			$json_array['content'] = $DOM;
			return $json_array;
			
		}
		else
		{
			return 'Looks like we couldn\'t find the content. :(';
		}
	}
	
	public function save_post($post_data)
	{
		
		$post_title   = $post_data['post_title'];
		//filter out all html tags except src and href
		$allowed_tags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'p' => array(),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
			'blockquote' => array(),
			'img' => array(
				'src' => array()
			),
			'h1' => array(),
			'h2' => array(),
			'h3' => array(),
			'h4' => array(),
			'ol' => array(),
			'li' => array(),
			'ul' => array()
		);
		$content      = wp_kses($post_data['post_content'], $allowed_tags);
		//add image classes
		$doc          = new DOMDocument();
		$doc->loadHTML(htmlspecialchars_decode(utf8_decode(htmlentities($content, ENT_COMPAT, 'utf-8', false))));
		
		$imgs = $doc->getElementsByTagName('img');
		foreach ($imgs as $img)
		{
			$img->setAttribute('class', 'size-full');
		}
		//remove doctype
		$content = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(array(
			'<html>',
			'</html>',
			'<body>',
			'</body>'
		), array(
			'',
			'',
			'',
			''
		), html_entity_decode($doc->saveHTML())));
		
		$new_post = array(
			'ID' => '',
			'post_author' => $user->ID,
			'post_title' => $post_title,
			'post_content' => $content,
			'post_status' => 'draft'
		);
		//insert post with content
		$post_id  = wp_insert_post($new_post);
		
		//filter out images
		$filtered_content = $this->post_save_image($post_id, $content);
		//reinsert content with filtered images and tags
		$my_post          = array(
			'ID' => $post_id,
			'post_content' => $filtered_content,
			'post_status' => 'draft'
		);
		// Update the post into the database
		wp_update_post($my_post);
		
		// This will redirect you to the newly created post
		$post                = get_post($post_id);
		$post_admin_edit_url = admin_url() . 'post.php?post=' . $post_id . '&action=edit';
		
		echo json_encode(array(
			'post' => $post,
			'admin_url' => $post_admin_edit_url
		));
		
		die();
	}
	
	
	/**
	 * Used to loop through the content for a post, grab remote images, and
	 * save them locally in the media gallery.
	 * Code adapted from this plugin: https://wordpress.org/plugins/dx-auto-save-images/
	 */
	private function post_save_image($post_id, $content)
	{
		
		$preg = preg_match_all('/<img.*?src="(.*?)"/', stripslashes($content), $matches);
		if ($preg)
		{
			$i = 1;
			foreach ($matches[1] as $image_url)
			{
				if (empty($image_url))
					continue;
				$pos = strpos($image_url, get_bloginfo('url'));
				if ($pos === false)
				{
					$res     = $this->save_images($image_url, $post_id, $i);
					$replace = $res['url'];
					$content = str_replace($image_url, $replace, $content);
				}
				$i++;
			}
		}
		return $content;
	}
	
	/**
	 * Save remote images locally in the media gallery.
	 * Code adapted from this plugin: https://wordpress.org/plugins/dx-auto-save-images/
	 */
	private function save_images($image_url, $post_id, $i)
	{
		$file     = file_get_contents($image_url);
		$filename = basename($image_url);
		$im_name  = $filename;
		
		$res = wp_upload_bits($im_name, '', $file);
		
		$attach_id = $this->insert_attachment($res['file'], $post_id);
		
		if ($options['post-tmb'] == 'yes' && $i == 1)
		{
			set_post_thumbnail($post_id, $attach_id);
		}
		return $res;
	}
	
	/**
	 * Insert saved image as attachment to post
	 * Code adapted from this plugin: https://wordpress.org/plugins/dx-auto-save-images/
	 */
	private function insert_attachment($file, $id)
	{
		$dirs        = wp_upload_dir();
		$filetype    = wp_check_filetype($file);
		$attachment  = array(
			'guid' => $dirs['baseurl'] . '/' . _wp_relative_upload_path($file),
			'post_mime_type' => $filetype['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($file)),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id   = wp_insert_attachment($attachment, $file, $id);
		$attach_data = wp_generate_attachment_metadata($attach_id, $file);
		wp_update_attachment_metadata($attach_id, $attach_data);
		return $attach_id;
	}
	
	
	
	/**
	 * remove, from the given xhtml string, all divs with the given class.
	 * Source: http://stackoverflow.com/a/12860211
	 */
	private function remove_divs_with_class($xhtml, $class)
	{
		$doc = new DOMDocument();
		
		// Hack to force DOMDocument to load the HTML using UTF-8:
		$doc->loadHTML($xhtml);
		$this->removeElementsByTagName('script', $doc);
		$this->removeElementsByTagName('style', $doc);
		$xpath    = new DOMXpath($doc);
		$elements = $xpath->query("//*[contains(@class,'$class')]");
		
		foreach ($elements as $element)
			$element->parentNode->removeChild($element);
		
		return $doc->saveHTML();
	}
	
	
	
	/**
	 * remove elements with certain tag
	 */
	private function removeElementsByTagName($tagName, $document)
	{
		$nodeList = $document->getElementsByTagName($tagName);
		for ($nodeIdx = $nodeList->length; --$nodeIdx >= 0;)
		{
			$node = $nodeList->item($nodeIdx);
			$node->parentNode->removeChild($node);
		}
	}
	
	
	/**
	 * Add marvel css to content for previewing (before save as draft)
	 */
	private function add_marvel_html_classes($DOM)
	{
		//add marvel css to the html
		
		//format p tags
		$list = $DOM->getElementsByTagName('p');
		foreach ($list as $p)
		{
			$p->setAttribute('class', 'pageWrap pageWrap--s marginBottom-m paddingBottom-s c-slate lineHeight-l fontSize-l fontWeight-3 breakPointM-fontSize-xl breakPointM-lineHeight-xl');
		}
		
		//swap all htags for h2
		$titles = array(
			"h2",
			"h3",
			"h4",
			"h5"
		);
		//loop through a few times, because it misses some tags
		for ($i = 0; $i < 6; $i++)
		{
			//loop through all types of titles and replace with h2
			foreach ($titles as $title)
			{
				//replace with h2
				foreach ($DOM->getElementsByTagName($title) as $titlenode)
				{
					$h2node = $DOM->createElement("h2", $titlenode->nodeValue);
					//swap the nodes
					$titlenode->parentNode->replaceChild($h2node, $titlenode);
					//add the classes
					$h2node->setAttribute('class', 'pageWrap pageWrap--s marginTop-xl marginBottom-l c-slate lineHeight-xl fontSize-xl fontWeight-5 breakPointM-lineHeight-xxl breakPointM-fontSize-xxl');
				}
			}
		}
		
		//format blockquotes
		$list = $DOM->getElementsByTagName('blockquote');
		foreach ($list as $quote)
		{
			$quote->setAttribute('class', 'blog-quote position-relative textAlign-center c-marvel');
		}
		
		//format list items
		$listtags = array(
			"ol",
			"ul"
		);
		foreach ($listtags as $tags)
		{
			$tags = $DOM->getElementsByTagName($tags);
			foreach ($tags as $tag)
			{
				$tag->setAttribute('class', 'pageWrap pageWrap--s list list--ordered marginBottom-l lineHeight-l fontSize-l fontWeight-3 breakPointM-fontSize-xl breakPointM-lineHeight-xl');
			}
		}
		//format lists
		return $DOM = $DOM->saveHTML($DOM->documentElement);
	}
	
}