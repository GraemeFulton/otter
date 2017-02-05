<?php
    // Configuration
    require('includes/config.php');

    // Construct URL
    $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    $getfield = '?screen_name=' . $handle . '&count=3200' . '&include_rts=1';
    $requestMethod = 'GET';

    // Make authenticated call to Twitter API
    $twitter = new TwitterAPIExchange($settings);

    // store returned JSON in associative array
    $tweets =  json_decode($twitter->setGetfield($getfield)
                                       ->buildOauth($url, $requestMethod)
                                       ->performRequest(), $assoc = TRUE);

    // Assure connection to Twitter API
    checkError($tweets);

    // Iterate through tweets to find the Top Tweet!
    $sorted = array();

    foreach ($tweets as $key => $row)
    {
     // Add # of likes to sorted array
     $sorted[$key] = $row['favorite_count'];
    }

    // Sort array in descending order based on likes
    array_multisort($sorted, SORT_DESC, $tweets);
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel='stylesheet' type='text/css' href='css/stylesheet.css'>
        <link href='https://fonts.googleapis.com/css?family=Raleway:500' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdn.rawgit.com/konpa/devicon/master/devicon.min.css">
        <script type='text/javascript' src='includes/widgets.js'></script>
        <title>topTweet | Home</title>
    </head>
    <body>

        <!--   NAVBAR   -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar-header">
                    <a href="index.php" class="pull-left"><img src="images/twitter-logo.png" height="50px" width="50px"></a>
                    <a href="index.php" class="navbar-brand">topTweet</a>
                    <button class="navbar-toggle" data-toggle="collapse" data-target=".navHeaderCollapse">
                        <span class="glyphicon glyphicon-menu-hamburger"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse navHeaderCollapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="index.php">Home</a></li>
                        <li class="active"><a href="explore.html">Explore</a></li>
                        <li><a href="faq.html">FAQ</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <h1>@<?php echo $handle; ?>'s Top 10!</h1>
            <hr class="featurette-divider">

            <?php
                // Set Counter
                $tweetCount = 0;

                // Loop through array of sorted Tweets
                foreach($tweets as $tweet) {
                    if ($tweetCount != 10) {

                        // Get ID of most recent tweet
                        $tweet_Id = $tweet['id'];

                        // Embed current tweet
                        echo embedTweet($handle, $tweet_Id, $twitter);
                        echo '</div>';

                        $tweetCount++;
                    }
                    else {
                        break;
                    }
                }
            ?>

        </div>

        <!--  FOOTER  -->
        <div class="navbar navbar-default navbar-fixed-bottom" role="navigation" id="footer">
            <div class="container">
                <div class="navbar-text pull-left">
                    <ul>
                        <li>Application built by Jake Wiesler</li>
                        <li><p>Copyright &copy <?php date_default_timezone_set('UTC'); echo date("Y");?></p></li>
                    </ul>
                </div>
                <div class="navbar-text pull-right">
                        <a href="https://twitter.com/jake_wies" target='_blank'><i class="fa fa-twitter fa-2x" style="color: #4099FF"></i></a>
                        <a href="https://github.com/jake-wies" target='_blank'><i class="fa fa-github fa-2x" style="color: black"></i></a>
                        <a href="http://greatfljake.tumblr.com/" target='_blank'><i class="fa fa-tumblr fa-2x" style="color: #35465c"></i></a>
                        <a href="https://soundcloud.com/if_else" target='_blank'><i class="fa fa-soundcloud fa-2x" style="color: #ff7700"></i></a>
                </div>
            </div>
        </div>

        <!--  jQuery & Bootstrap.js files  -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    </body>
</html>
