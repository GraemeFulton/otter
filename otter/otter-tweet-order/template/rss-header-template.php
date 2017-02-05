<channel>
<title><?php bloginfo_rss('name'); ?> - Feed</title>
<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
<link><?php bloginfo_rss('url') ?></link>
<description><?php bloginfo_rss('description') ?></description>
<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
<language><?php echo get_option('rss_language'); ?></language>
<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>