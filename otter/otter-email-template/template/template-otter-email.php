<?php
/* Template Name: Content Otter Email */
?>

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
<body style="margin:0; -webkit-text-size-adjust:none;">
    
   
<!-- RSS Parser -->
<?php
if(function_exists('fetch_feed')) {
    include_once(ABSPATH . WPINC . '/feed.php'); // the file to rss feed generator
    $feed = fetch_feed(get_home_url().'/feed/content-otter'); // specify the rss feed
    $limit = $feed->get_item_quantity(10); // specify number of items
    $items = $feed->get_items(0, $limit); // create an array of items
}
if ($limit == 0) {
    echo '<div>The feed is either empty or unavailable.</div>';
}
else {
    include( WP_PLUGIN_DIR.'/content-otter/otter/common-libs/og-data/libs/simplehtmldom/simple_html_dom.php');
    foreach ($items as $item) :
  $htmlDOM = new simple_html_dom();
$htmlDOM->load($item->get_content());
$image = $htmlDOM->find('img', 0); 
$img= '<img src="' . $image->src . '" border="0" style="height:100px;"/>';

        ?>

<!-- How the RSS posts display -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="border-bottom:#2591c7 solid 1px; border-top: #54b9ec solid 1px; background:#3ca7dd" width="1%">&nbsp;</td>
        <td width="90%" align="left" style="padding:12px; border-right: #e1e1e1 solid 1px; border-bottom:#CDCDCD solid 1px; border-top: #FCFCFC solid 1px; background: #f5f5f5;">
            <!-- Title and hyperlink -->                      
            <div style="margin-top:0; margin-bottom:0px; padding-top:0; padding-bottom:0;"><a target="_blank" href="<?php echo $item->get_permalink(); ?>" alt="<?php echo $item->get_title(); ?>" style="color:#4E4946; text-decoration:none; font-size:100%; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; font-weight:normal; font-weight:bold;"><?php echo $item->get_title(); ?></a></div>
        </td>
        <td width="9%" align="center" style="background: #eeeeee; display: table-cell; vertical-align: middle; border-bottom:#CDCDCD solid 1px; border-top: #FCFCFC solid 1px;"><a target="_blank" href="<?php echo $item->get_permalink(); ?>" alt="<?php echo $item->get_title(); ?>"><?php echo $img; ?></a>
        </td>
    </tr>
</table> <!-- End of post display -->
<?php
    endforeach;
}
?> <!-- End of RSS Parser -->