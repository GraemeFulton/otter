<item>
    <title><?php echo($url);?><?php echo $tweet['text']?></title>
    <link><?php echo $tweet['urls'][0]['url']; ?></link>
    <pubDate><?php echo $tweet['created_at']; ?></pubDate>
    <dc:creator><?php the_author(); ?></dc:creator>
    <guid isPermaLink="false"><?php echo $tweet['id']; ?></guid>
    <description>
         <![CDATA[<a href="https://dribbble.com/shots/3157421-Google-Logo-Animation" width="200" height="150"><img alt="Google Logo Animation" width="800" height="600" src="https://d13yacurqjgara.cloudfront.net/users/1180195/screenshots/3157421/google_logo.gif" /></a>
        <?php echo $tweet['text'];?>
         ]]>
    </description>
    <?php rss_enclosure(); ?>
    <?php do_action('rss2_item'); ?>
</item>
                

