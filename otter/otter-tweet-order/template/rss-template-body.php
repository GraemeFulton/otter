<item>
    <title><?php echo ($og['title']) ? $og['title'] : $tweet['text']?></title>
    <link><?php echo $url; ?></link>
    <pubDate><?php echo $tweet['created_at']; ?></pubDate>
    <dc:creator><?php the_author(); ?></dc:creator>
    <guid isPermaLink="false"><?php echo $tweet['id']; ?></guid>
    <description>
         <![CDATA[<a href="<?php echo $url ?>" width="200" height="150"><img src="<?php echo ($og['image']) ? $og['image'] : $url;?>" /></a>
        <?php echo $tweet['text'];?>
         ]]>
    </description>
    <?php rss_enclosure(); ?>
    <?php do_action('rss2_item'); ?>
</item>
                

