   

(function( $ ) {
	'use strict';
        

	$(document).ready(function() {


            /** on button click, get value from input and show preview of article **/
	var post_content = '';
            var tweetContainer = $('#tweets');
     
        $('#get_tweets').click(function(e){
                                 
            tweetContainer.removeClass('orange');

            $('#overlay').fadeIn();
            $.ajax({ 
                 data: {action: 'otter_get_tweets', name:$('#twitter_handle').val()},
                 type: 'post',
                 url: ajaxurl,
                 dataType:'json',
                 success: function(data) {

                        console.log(data)
                                 //mess with the dates
                                    var TODAY, YESTERDAY, A_WEEK_OLD; 
                                    TODAY = moment().format("DD-MM-YYYY");
                                    YESTERDAY =  moment().add(-1, 'days')
                                    A_WEEK_OLD =moment().add(-6, 'days');
                                   var TWO_WEEK_OLD = moment().add(-90, 'days');
                        
                                tweetContainer.html('');
                                var tweetCount = 0;
                                $('#tweets').append("<h1>@"+$('#twitter_handle').val()+"'s Top 20 Tweets from the last 7 days</h1>")
                                $('#tweets').append('<p> The last 7 days tweets ranked by total retweets, and favourites.</p>');
                                $('#tweets').append('<p> From: '+moment(A_WEEK_OLD).format("DD-MM-YYYY")+" to "+TODAY+'</p>')

                                for (var tweet in data) {
                                    //check date
                             
                                    var tweetdate =moment(data[tweet].created_at,'dd MMM DD HH:mm:ss ZZ YYYY', 'en')                                
                           
                                    
                                    //dates
                                    if(tweetdate > A_WEEK_OLD){
                                        if(tweetCount <20){
                                            var count = tweetCount+1
                                            //create tweet container moment().startOf('day').fromNow();        // 12 hours ago
                                             $('#tweets').append('<div class="tweet-box" id="tweet-box-'+count+'"><h1>#'+count+' Tweeted '+moment(data[tweet].created_at).startOf('day').fromNow()+'</h1><h2><span style="color:#888"> 🔁 '+data[tweet].retweet_count+' ❤️ '+data[tweet].favorite_count+'</span></h2></div>')
                                            //create tweet in tweet container
                                            twttr.widgets.createTweet(
                                                data[tweet].id_str,
                                                document.getElementById('tweet-box-'+count),
                                                {
                                                  theme: 'light',
                                                  align:'center'
                                                }
                                              );
                                           tweetCount++; 

                                        }
                                        
                                    }
                            }
                        
                       setTimeout(function(){  
                       
                         $('#overlay').fadeOut();
                       
                       }, 1500);
                               
                            

                       
                            
                            tweetContainer.addClass('orange');
                                
                }
        });
    });

  });
})( jQuery );

        