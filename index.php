<?php 
	require_once('config/settings.php');
	require_once('config/database.php');
	require_once('controller/theme_manager.php');
	
	$theme_manager = new ThemeManager();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" href="css/sidebar.css" />
	<link rel="stylesheet" type="text/css" href="css/buttons.css" />
	<link rel="stylesheet" type="text/css" href="css/textbox-dialogs.css" />
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

		var $thank_you_dialog = $('<div></div>')
			.html("Thank you.<br>Your contribution is pending approval.")
			.dialog({
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
		

		// get the month value.
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
						$("#previous_theme_button").hide();
					}
	
					// If we are displaying the present month, hide the next theme/month button.
					if(dateData["month"] == dateData["current_month"]){
						$("#next_theme_button").hide();
					}
				}
				
			},
			error : function() {
				//alert('Great Failure!');
			}
		});

		var text_editor_width = 500;
		var text_editor_height = 250;

		var text_editor = $("#text_content").cleditor({
				controls: "bold italic underline"
			})[0];
		
		// hide sidebar
		$('#canvas').css({left: "0em"});

		var $document_width = $(document).width();
		var $document_height = $(document).height();

		function generate_random_coordinates(){
			var x = Math.floor(Math.random() * ($document_width - 400)) + 50;
			var y = Math.floor(Math.random() * ($document_height - 400))  + 50;
			
			return [x, y];
		}
		
		function build_and_display_text_dialog(content, textbox_dialog_css_index){
			var coordinates = generate_random_coordinates();

			//var zIndex = Math.floor(Math.random() * 2001) + 1000; 
			var div_element = "<div id='textbox_dialog_" + textbox_dialog_css_index + "'></div>";
		
			var $text_dialog = $(div_element)
						.html(content)
						.dialog({
							autoOpen: false,
							position: [coordinates[0], coordinates[1]],
							resizable: false
							//zIndex = zIndex
						});
				
			// Make entire dialog draggable, not just the header
			$text_dialog.data('dialog').uiDialog.draggable('option', {
				cancel: '.ui-dialog-titlebar-close',
				handle: '.ui-dialog-titlebar, .ui-dialog-content'
			})
			
			// Style the text dialog
			//$text_dialog.css('background-color', background_colour);
			//$text_dialog.css('color', font_colour);
			
			$text_dialog.dialog('open');
		}

		function build_and_display_image_dialog(photo_page_url, photo_display_rendition_url, photo_display_rendition_width, photo_display_rendition_height){

			// Calculate random coordinates for the dialog
			var coordinates = generate_random_coordinates();

			//var zIndex = Math.floor(Math.random() * 2001) + 1000;  

			var $image_dialog = $('<div></div>')
				.dialog({
					autoOpen: false,
					position: [coordinates[0], coordinates[1]],
					//zIndex: zIndex,
					height: photo_display_rendition_height,
					width: photo_display_rendition_width,
					resizable: false
				});
		
			// Make entire dialog draggalble, not just the header
			$image_dialog.data('dialog').uiDialog.draggable('option', {
				cancel: '.ui-dialog-titlebar-close',
				handle: '.ui-dialog-titlebar, .ui-dialog-content'
			})
	
			// Set the background to be the image located at the image url
			$image_dialog.css('background', 'url(' +  photo_display_rendition_url + ')');
			$image_dialog.css('background-repeat', 'no-repeat');
			
			$image_dialog.dialog('open');
		}
		
		
		
		/**
		 * Retrieve the text for this month's theme
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
					if(textbox_dialog_css_index > 5){
						textbox_dialog_css_index = 1;
					}
				});
			},
			error : function() {
				//alert('Great Failure!');
			}
		});
		
		
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
			autoOpen: false,
			modal: true,
			height: text_editor_height+120,
			width: text_editor_width+35,
			resizable: false,
			buttons: {
				"contribute": function() {
				
					// apply check length rules at some point
					
					// ajax post
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
					
					$(this).dialog("close");
				},
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
		$("#previous_theme_button").click(function() {
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
		$("#next_theme_button").click(function() {
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

	});
	</script>
</head>
<body>
	<div id="sidebar">
		
		<div id="sidebar_textual_content">
			<br/>
			<div id="project_description">
				<div id="description_title">
					Project Description
				</div>
				<div id="description_content">
					What pleases you or calls you forward from your own daze into enthusiasm? Which are the brightest moments of today and what will you look forward to retelling, over drinks or in a secret message? What things are there that sit inside you and wait, unabashedly looking to be fascinated and swept up?
					<br/>
					<br/>
					The Please Project is looking for your pleasure, as you find it in these monthly themes. We are delighted at your contributions.
				</div>
			</div>
			
			<br/><br/>
			
			<div id="theme_description">
				<div id="description_title">
					Theme Description
				</div>
				<div id="description_content">
					<?php echo $theme_manager->getDisplayedThemeDescription();?>
				</div>
			</div>
		</div>
		
		<div id="sidebar_buttons" align="center">
			<ul id="contribution_buttons">
    			<li><a id="contribute_text_button"><span>contribute text</span></a></li>
   				<li><a id="contribute_image_button"><span>contribute image</span></a></li>
			</ul>
		</div>
	</div>
	
	
	<div id="canvas">
		<div id="sidebarslip"><?php echo $theme_manager->getDisplayedThemeTitle(); ?></div>
		
	</div>
	
	<div id="footer">
		<span id="previous_theme_button">&lt;&lt;</span>&nbsp;<?php echo $theme_manager->getDisplayedThemeDate();?>&nbsp;<span id="next_theme_button">&gt;&gt;</span>
	</div>
	
	<div id="forms" style="display:none">
		<?php include 'view/content_contribution_forms.php';?>
	</div>
</body>
</html>