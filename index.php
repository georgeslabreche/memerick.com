<?php 
	require_once('config/settings.php');
	require_once('config/database.php');
	require_once('controller/theme_manager.php');
	
	$theme_manager = new ThemeManager();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/layout.css" />
	<link rel="stylesheet" type="text/css" href="css/textbox-dialogs.css" />
	<link rel="stylesheet" type="text/css" href="css/sidebar.css" />
	<link rel="stylesheet" type="text/css" href="css/footer.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo $theme_manager->getDisplayedThemeCssFilename()?>" /> 
	
	<link rel="stylesheet" type="text/css" href="js/cleditor/jquery.cleditor.css" />
	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="css/memerick-jquery-ui.css" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>

	<!--<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>-->
	<script type="text/javascript" src="js/jquery.form.js"></script>
	
	<script type="text/javascript" src="js/cleditor/jquery.cleditor.js"></script>
	<script type="text/javascript" src="js/cleditor/jquery.cleditor.min.js"></script>

	
	<script type="text/javascript">

	// Hotfix for this issue: http://bugs.jqueryui.com/ticket/4163
	document.onselectstart = function () { return false; };

	// Resize background when window loads
	$(window).load(function() {    
		
		var theWindow        = $(window),
		    $bg              = $("#background"),
		    aspectRatio      = $bg.width() / $bg.height();

		function resizeBg() {

			if ( (theWindow.width() / theWindow.height()) < aspectRatio ) {
			    $bg
			    	.removeClass()
			    	.addClass('background_height');
			} else {
			    $bg
			    	.removeClass()
			    	.addClass('background_width');
			}

			// Only display background after its resized
			$bg.css('display', 'block');

		}

		theWindow.resize(function() {
			resizeBg();
		}).trigger("resize");

	});
	
	// on page ready
	$(document).ready(function() {

		var NUMBER_OF_TEXT_BOX_COLOURS = 5;


		// Dialog thanking the user for contributing to the project.
		var $thank_you_dialog = $('<div></div>')
			.html("Thank you.<br>Your contribution is pending approval.")
			.dialog({
				dialogClass: 'headerless_dialog',
				autoOpen: false,
				modal: true,
				resizable: false,
				buttons: {
					"ok": function() {
						$(this).dialog("close");
					}
				}
			
		});

		/**
		 * Return an associative array of the page's url parameters.
		 * For our index page this will be date data.
		 *
		 * e.g. memerick.com?year=2012&month=2
		 * will return {"year":"2012", "month":"2"} 		  
		 */
		function getUrlVars() {
			var vars = {};
			var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, 
					function(m,key,value) {
						vars[key] = value;
					});

		    return vars;
		}

		/**
		 * Get a specific param value from the url.
		 */
		function getUrlVar(paramName){
			return getUrlVars()[paramName];
		}

		function displayThankYouDialog(){
			$thank_you_dialog.dialog('open');
		}
					 

		// Get the date data associative array from the url parameters.
		var dateData = getUrlVars();

		
		// By default we display the current theme if date params are not set in the url.
		// but we still want to set the date datas in the dataData array for footer functionality
		// purposes. The footer works as theme navigator based on date.

		// Get the current year
		$.ajax({
			url: 'controller/get_current_year.php',
			async: false,
			dataType: 'json',
			success: function(year){

				// If year to display is in the future, then display current year.
				if(dateData["year"] != null && dateData["year"] > year){

					// reload the page so that we display the appropriate url params
					var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
					newURL = newURL + "?year=" + year;
	
					// gross for now. Todo: use smooth ajax
					window.location.href = newURL;
					
				}else{
					// Set year to display if it was not set in url params.
					if(dateData["year"] == null){
						dateData["year"] = year;
					}

					// Set the current year.
					dateData["current_year"] = year; 
				}

				
			},
			error : function() {
				//alert('Great Failure!');
			}
		});
		

		// Get the current month.
		$.ajax({
			url: 'controller/get_current_month.php',
			async: false,
			dataType: 'json',
			success: function(month){

				// If month to display is in the future, then display current month.
				if(dateData["month"] != null && dateData["month"] > month){
					// reload the page so that we display the appropriate url params
					var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
					newURL = newURL + "?year=" + dateData["year"] + "&month=" + month;
	
					// gross for now. Todo: use smooth ajax
					window.location.href = newURL;
				}else{

					// Set month to display if it was not set in url params.
					if(dateData["month"] == null){
						dateData["month"] = month;	
					}	 

					// Set the current month.
					dateData["current_month"] = month;
	
					// If we are displaying the first month of the year then hide previous theme/month button.
					if(dateData["month"] == 1){
						$("#theme_navigation_button_previous").hide();
					}
	
					// If we are displaying the present month, hide the next theme/month button.
					if(dateData["month"] == dateData["current_month"]){
						$("#theme_navigation_button_next").hide();
					}
				}
				
			},
			error : function() {
				//alert('Great Failure!');
			}
		});

		var text_editor_width = 500;
		var text_editor_height = 250;

		// Created cleditor text editor.
		var text_editor = $("#text_content").cleditor({
				controls: "bold italic underline"
			})[0];

		

		var $document_width = $(document).width();
		var $document_height = $(document).height();

		/*
		 * Generate random x,y coordinates to be used by the dialogs what will
		 * display user submitted contributions
		 */
		function generate_random_coordinates(){
			var x = Math.floor(Math.random() * ($document_width - 400)) + 50;
			var y = Math.floor(Math.random() * ($document_height - 400))  + 50;
			
			return [x, y];
		}

		/**
		 * Build and display dialogs for user submitted text contributions. 
		 */
		function build_and_display_text_dialog(content, textbox_dialog_css_index){
			var coordinates = generate_random_coordinates();

			//var zIndex = Math.floor(Math.random() * 2001) + 1000; 
			var dialog_class ="text_dialog text_dialog_" + textbox_dialog_css_index;
			
			var $text_dialog = $("<div></div>")
						.html(content)
						.dialog({
							dialogClass: dialog_class, // give dialog class name so we can theme it in css
							autoOpen: false,
							position: [coordinates[0], coordinates[1]],
							resizable: true,
							closeOnEscape: false,
							height: 200
							//zIndex = zIndex
						});
				
			// Make entire dialog draggable, not just the header
			/*
			$text_dialog.data('dialog').uiDialog.draggable('option', {
				cancel: '.ui-dialog-titlebar-close',
				handle: '.ui-dialog-content',
			})*/
			
			
			// Style the text dialog
			//$text_dialog.css('background-color', background_colour);
			//$text_dialog.css('color', font_colour);
			
			$text_dialog.dialog('open');
		}

		/*
		 * Build and display dialogs for user submitted image contributions.
		 */
		function build_and_display_image_dialog(photo_title, photo_description, photo_page_url, photo_display_rendition_url, photo_display_rendition_width, photo_display_rendition_height){

			// Calculate random coordinates for the dialog
			var coordinates = generate_random_coordinates();

			var dialog_content = "<div><div id='image_dialog_text'><div id='photo_title'>" + photo_title + "</div><br /><div id='photo_author'>" + photo_description + "</div></div></div>";

			//var zIndex = Math.floor(Math.random() * 2001) + 1000;  

			var $image_dialog = $(dialog_content)
				.dialog({
					dialogClass: 'image_dialog headerless_dialog',  // give dialog class name so we can theme it in css
					autoOpen: false,
					position: [coordinates[0], coordinates[1]],
					//zIndex: zIndex,
					height: photo_display_rendition_height,
					width: photo_display_rendition_width,
					resizable: false,
					closeOnEscape: false
				});

			// Hide header
			
			// Make entire dialog draggalble, not just the header
			$image_dialog.data('dialog').uiDialog.draggable('option', {
				cancel: '.ui-dialog-titlebar-close',
				handle: '.ui-dialog-content'
				//handle: .ui-dialog-content'
			})
	
			// Set the background to be the image located at the image url
			$image_dialog.css('background', 'url(' +  photo_display_rendition_url + ')');
			$image_dialog.css('background-repeat', 'no-repeat');  
			
			$image_dialog.dialog('open');
		}
		

		/**
		 * Retrieve the text for the theme being displayed
		 */
		$.ajax({
			url: 'controller/get_text.php',
			data: dateData,
			dataType: 'json',
			success: function(data){

				var textbox_dialog_css_index = 1;

				// For each text fetched
				jQuery.each(data, function(index, object) {
			
					// Get actual textual content 
					var text_content = object['content'];

					// Build the text dialog box
					build_and_display_text_dialog(text_content, textbox_dialog_css_index);

					// Increment text dialog colour index so that the next dialog we will create
					// will have the next colour pair.
					textbox_dialog_css_index++;

					// Reset the text box dialog css index if we have gone through all the css colour pairs.
					// Restart from the first pair of colours.
					if(textbox_dialog_css_index > NUMBER_OF_TEXT_BOX_COLOURS){
						textbox_dialog_css_index = 1;
					}
				});
			},
			error : function() {
				//alert('Great Failure!');
			}
		});
		

		/**
		 * Retrieve the images for the theme being displayed
		 */
		$.ajax({
			url: 'controller/get_images.php',
			data: dateData,
			dataType: 'json',
			success: function(images_json){

				// query each photo object in the retrieve json.
				jQuery.each(images_json, function(index, object) {

					// Get the flickr photo_id for each photo.
					var photo_page_url = object['photo_page_url'];

					// Get the flickr user_id for each photo (it's the same for each one).
					var photo_display_rendition_url = object['photo_display_rendition_url'];
					var photo_display_rendition_width = object['photo_display_rendition_width'];
					var photo_display_rendition_height = object['photo_display_rendition_height'];

					var photo_title = object['photo_title'];
					var photo_description = object['photo_description'];
					
					build_and_display_image_dialog(photo_title, photo_description, photo_page_url, photo_display_rendition_url, photo_display_rendition_width, photo_display_rendition_height);
				});
			},
			error : function() {
				//alert('Great Failure!');
			}
		});
			
		/*
		 * Text contribution form dialog.
		 */
		$("#text_contribution_div").dialog({
			title: 'contribute text',
			draggable: false,
			autoOpen: false,
			modal: true,
			height: text_editor_height+140,
			width: text_editor_width+35,
			resizable: false,
			buttons: {
				"contribute": function() {
				
					// apply check length rules at some point
					
					// ajax post to submit text
					$.ajax({
						type : 'POST',
						url : 'controller/submit_text.php',
						data: $("#text_contribution_form").serialize(),
						success : function($data){
						
							// We've successfully contributed a text, let's display the dialog thanking the user
							// for his contribution
							displayThankYouDialog();
							
						},
						error : function() {
							//alert('Great Failure!');
						}
					});

					// Close dialog after text is submitted
					$(this).dialog("close");
				},

				// Close dialog if we hit on cancel button
				"cancel": function(){
					$(this).dialog("close");
				}
			},
			open: function(){
				text_editor.refresh();
				text_editor.focus(); 
			},
			close: function() {
				$('textarea#text_content').val('');
			}
		});
		
		
		
        // bind image contribution form and provide a simple callback function.
        // We use ajaxForm because we want to submit a multipart/form-data form
        $('#image_contribution_form').ajaxForm(function() { 
        }); 
    
		/*
		 * Image contribution form dialog.
		 */
		$("#image_contribution_div").dialog({
			title: 'contribute image',
			draggable: false,
			autoOpen: false,
			width: 350,
			modal: true,
			buttons: {
				"contribute": function() {
					$('#image_contribution_form').submit();
					$(this).dialog("close");

					displayThankYouDialog();
				},
				"cancel": function(){
					$(this).dialog("close");
				}
			},
			close: function() {
				// Clear form when closing image contribution dialog
				$('#image_title').val('');
				$('#image_author').val('');
				$('#image_file').val('');
			}
		});

		/*
		 * Open dialog form to contribute text when clicking the
		 * contribute text button.
		 */	
		$("#contribute_text_button").click(function() {
			$("#text_contribution_div").dialog("open");				
		});

		/*
		 * Open dialog form to contribute image when clicking the
		 * contribute image button.
		 */	
		$("#contribute_image_button").click(function() {
			$("#image_contribution_div").dialog("open");
		});

		/**
		 * Reload page with previous month's theme.
		 */
		$("#theme_navigation_button_previous").click(function() {
			var month_displayed = parseInt(dateData["month"]);
			
			if(month_displayed > 1){
				var month_to_display = month_displayed - 1;

				var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
				newURL = newURL + "?month=" + month_to_display;

				// gross for now. Todo: use smooth ajax
				window.location.href = newURL;

			}
			
		});

		/**
		 * Reload page with next month's theme.
		 */
		$("#theme_navigation_button_next").click(function() {
			// Current month, as the actual present month
			var current_month = parseInt(dateData["current_month"]);

			// Month that is currently displayed
			var month_displayed = parseInt(dateData["month"]);

			if(month_displayed < 12){
				var month_to_display = month_displayed + 1;

				// We don't want to go past the current month.
				if(month_to_display <= current_month){

					var newURL = window.location.protocol + "//" + window.location.host + window.location.pathname;
					newURL = newURL + "?month=" + month_to_display;
	
					// gross for now. Todo: use smooth ajax
					window.location.href = newURL;

				}
			}
		});

		// Contribution button hover behaviour.
		$(".contribution_button").hover(
			function(){ 
				$(this).addClass("ui-state-hover"); 
			},
			function(){ 
				$(this).removeClass("ui-state-hover"); 
			}
		)


		/* 
		 * Clicking on the sidebar  will toggle it out/in of the canvas.
		 *
		 * When toggled out, a far right vertical portion of the sidebar is
		 * still visible so that we can click on it to make the menu appear
		 * again. 
		 */
		$('.sidebar').toggle(
			function() {
				$('.sidebar').animate({"left": "+=12em"}, "slow");
			}, function() {
				$('.sidebar').animate({"left": "-=12em"}, "slow");
			}
		);

		/* 
		 * Use this if we want to use a sidebar slip to toggle the sidebar instead
		 * of a sidebar's vertical slip. 
		 */
		/*
		$('.sidebar_slip').toggle(
			function() {
				$('.sidebar').animate({"left": "+=15em"}, "slow");
				$('.sidebar_slip').animate({"left": "-=15em"}, "slow");
			}, function() {
				$('.sidebar').animate({"left": "-=15em"}, "slow");
				$('.sidebar_slip').animate({"left": "+=15em"}, "slow");
			}
		);*/

		
		// Hide sidebar
		$('.sidebar').animate({"left": "-=12em"}, 2000);

		// If using sidebar slip, use this instead:
		//$('.sidebar').animate({"left": "-=15em"}, "slow");
		//$('.sidebar_slip').animate({"left": "+=15em"}, "slow");

	});

	
	</script>	
</head>

<body>
	<!-- Background image  -->
	<img src="<?php echo $theme_manager->getDisplayedThemeBackgroundPath()?>" id="background" alt="" />

	<div class="sidebar">
		<div class="sidebar_overlay"></div>
		<div class="sidebar_content">
			<?php include 'view/sidebar.php';?>
		</div>
		
	</div>
	
	<!-- 
		Uncomment this if we want to toggle the sidebar with a
		sidebar slip instead of clicking on the sidebar itself.
		
		Needs to be styled. Too ugly right now to use this approach.
	
	 -->
	<?php /*
	<div class="sidebar_slip"><?php echo $theme_manager->getDisplayedThemeTitle(); ?></div>
	*/ ?>
	 
	<div id="logo_container">Please&nbsp;Project</div>

	
	<div id="footer">
		<?php include 'view/footer.php';?>
	</div>


	<div id="forms" style="display:none">
		<?php include 'view/content_contribution_forms.php';?>
	</div>
			
</body>
</html>