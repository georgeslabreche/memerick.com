<?php 
	require_once('config/settings.php');
	require_once('config/database.php');
	require_once('controller/theme_manager.php');
	
	$theme_manager = new ThemeManager(); 

?>

	<link rel="stylesheet" type="text/css" href="css/sidebar.css">
	<link rel="stylesheet" type="text/css" href="js/cleditor/jquery.cleditor.css">
	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="css/memerick-jquery-ui.css">

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>

	<!--<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.validate/1.7/jquery.validate.min.js"></script>-->
	<script type="text/javascript" src="js/jquery.form.js"></script>
	
	<script type="text/javascript" src="js/cleditor/jquery.cleditor.js"></script>
	<script type="text/javascript" src="js/cleditor/jquery.cleditor.min.js"></script>

	
	<script type="text/javascript">
	
	// on page ready
	$(document).ready(function() {
		var text_editor_width = 500;
		var text_editor_height = 250;

		var text_editor = $("#text_content").cleditor({
				controls: "bold italic underline"
			})[0];
		
		// hide sidebar
		$('#canvas').css('left', '0');


		// Array of a colour pair.
		// A colour pair consists of the text dialog's background colours and its respective font colour
		var text_box_colour_array = new Array(
				new Array ("#E066FF", "#000000"),
				new Array ("#D8BFD8", "#000000"),
				new Array ("#CDC1C5", "#000000"),
				new Array ("#FF7D40", "#000000"),
				new Array ("#E9C2A6", "#000000"),
				new Array ("#FFE4C4", "#000000")
		)
		
		var $document_width = $(document).width();
		var $document_height = $(document).height();
		
		function generate_random_coordinates(){
			var x = Math.floor(Math.random() * ($document_width - 400)) + 50;
			var y = Math.floor(Math.random() * ($document_height - 400))  + 50;
			
			return [x, y];
		}
		
		function build_and_display_text_dialog(content, background_colour, font_colour){
			var coordinates = generate_random_coordinates();

			//var zIndex = Math.floor(Math.random() * 2001) + 1000; 
		
			var $text_dialog = $('<div></div>')
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
			$text_dialog.css('background-color', background_colour);
			$text_dialog.css('color', font_colour);
			
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
			dataType: 'json',
			success: function(data){

				var text_dialog_colours_index = 0;
				
				jQuery.each(data, function(index, object) {

					// Get colours and text content for the dialogue
					var background_color = text_box_colour_array[text_dialog_colours_index][0];
					var font_color =  text_box_colour_array[text_dialog_colours_index][1];;
					var text_content = object['content'];

					// Build the dialog
					build_and_display_text_dialog(text_content, background_color, font_color);

					// Increment text dialog colour index so that the next dialog we will create
					// will have the next colour pair.
					text_dialog_colours_index++;

					// Reset the text dialog colours index if we have gone through all the colour pairs.
					// Restart from the first pair of colours.
					if(text_dialog_colours_index >= text_box_colour_array.length){
						text_dialog_colours_index = 0;
					}
				});
			},
			error : function() {
				//alert('Great Failure!');
			}
		});
		
		
		$.ajax({
			url: 'controller/get_images.php',
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
						
							// We've successfully contributed a text, let's create a dialog for it
							// so that it can be displayed.
							build_and_display_text_dialog($data);
							
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
				},
				"cancel": function(){
					$(this).dialog("close");
				}
			},
			close: function() {
			}
		});
		
		$("#contribute_text").click(function() {
			$("#text_contribution_div").dialog("open");				
		});
			
		$("#contribute_image").click(function() {
			$("#image_contribution_div").dialog("open");
		});

		$('#sidebarslip').toggle(
			function() {
				$('#canvas').animate({left: 200})
			}, function() {
				$('#canvas').animate({left:0})
			}
		);

	});
	</script>

<div id="sidebar">
	<button id="contribute_text">contribute text</button>
	<button id="contribute_image">contribute image</button>
</div>


<div id="canvas">
	<div id="sidebarslip"><?php echo $theme_manager->getCurrentThemeTitle(); ?></div>
	<?php include 'view/content_contribution_forms.php'; ?>
</div>
