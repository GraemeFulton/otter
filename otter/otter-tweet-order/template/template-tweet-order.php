 <script>
 window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = "https://platform.twitter.com/widgets.js";
  fjs.parentNode.insertBefore(js, fjs);

  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };

  return t;
}(document, "script", "twitter-wjs"));
 </script>
        <section class="container content-otter-main padding twitter-form" style="max-width: 100%;">
               <div id="overlay">
      <img src="<?php echo plugin_dir_url( __FILE__ ) . 'images/otter.gif';?>">
    </div>
            <h1>Find top tweets</h1>
            <p>Enter a user's twitter handle (it's not necessary to include the '@' symbol):</p>

            <!--  INPUT  -->
                <div class="form-group">
                    <div class="col-xs-4">
                        <input id="twitter_handle" name="twitter_handle" type="text" class="form-control" placeholder="Username" aria-describedby="sizing-addon2">
                    </div>
                    <button  id="get_tweets" class="btn btn-default btn-primary col-xs-4">Search Twitter</button>
                </div>
            
        </section>
 
        <section id="tweets"class='padding' >
            <p>You've not searched for anything</p>
        </section>
<!-- 
<form method="POST" action="admin-post.php">
    <label for="awesome_text">Awesome Text</label>
    <input id="twitter_handle" name="twitter_handle" type="text" class="form-control" placeholder="Username" aria-describedby="sizing-addon2">
    <input name='action' type="hidden" value='custom_form_submit'>
    <input type="submit" value="Save" class="button button-primary button-large">
</form>-->