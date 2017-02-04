(function( $ ) {
	'use strict';

	$(document).ready(function() {

	/** on button click, get value from input and show preview of article **/
	var post_content = '';

			$('#nbs-ajax-get').click(function(){

					//show loding Otter
					$('#loading-otter').show();

					var siteurl = $('#urlinput').val()
					var mydata = {
							action: "otter_fetch_post",
							site: siteurl
					};

					$.ajax({
							type: "POST",
							url: ajaxurl,
							data: mydata,
							dataType: "json",
							success: function (data) {
								post_content = data;
								console.log(data);

									$('#loading-otter').hide();
									$('.ottered-content').html(data.content);
									$('.insert-draft').show();
									$('.title').html(data.title);

							},
							error: function (errorMessage) {
								console.log(errorMessage.responseText)

									console.log(errorMessage);
							}

					});
			});

			/** on save article button click, pull all data into wordpress as draft **/
			$('#nbs-ajax-save').click(function(){

				$('#overlay').fadeIn();

					var post_data = {
							post_content: post_content.content,
							post_title: post_content.title,
							action: "otter_save_post"
					};

					$.ajax({
							type: "POST",
							url: ajaxurl,
							dataType:'json',
							data: post_data,
							success: function (data) {

										console.log(data)
										window.location.replace(data.admin_url);

							},

							error: function (errorMessage) {

									console.log(errorMessage);
							}

						});
					});

			});


})( jQuery );
