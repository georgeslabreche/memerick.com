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

		
		// hide sidebar
		$('#canvas').css({left: "0em"});

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
		function build_and_display_image_dialog(photo_page_url, photo_display_rendition_url, photo_display_rendition_width, photo_display_rendition_height){

			// Calculate random coordinates for the dialog
			var coordinates = generate_random_coordinates();

			//var zIndex = Math.floor(Math.random() * 2001) + 1000;  

			var $image_dialog = $("<div></div>")
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
					
					build_and_display_image_dialog(photo_page_url, photo_display_rendition_url, photo_display_rendition_width, photo_display_rendition_height);
				});
			},
			error : function() {
				//alert('Great Failure!');
			}
		});
			
		
		$("#text_contribution_div").dialog({
			dialogClass: 'headerless_dialog',
			autoOpen: false,
			modal: true,
			height: text_editor_height+120,
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
    
		
		$("#image_contribution_div").dialog({
			dialogClass: 'headerless_dialog',
			autoOpen: false,
			height: 350,
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
			}
		});
		
		$("#contribute_text_button").click(function() {
			$("#text_contribution_div").dialog("open");				
		});
			
		$("#contribute_image_button").click(function() {
			$("#image_contribution_div").dialog("open");
		});

		$('#sidebarslip').toggle(
			function() {
				$('#canvas').animate({left: "12em"});
			}, function() {
				$('#canvas').animate({left: "0em"});
			}
		);


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


	});

	
	</script>
</head>
<body>
	<div id="sidebar">
		<?php include 'view/sidebar.php';?>
	</div>
		
	<div id="canvas">
		<?php include 'view/canvas.php';?>
	</div>
	
	<div id="footer">
		<?php include 'view/footer.php';?>
	</div>

	<div id="forms" style="display:none">
		<?php include 'view/content_contribution_forms.php';?>
	</div>
			
</body>
</html>