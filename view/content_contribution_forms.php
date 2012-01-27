<div id="text_contribution_div">
	<form name="text_contribution_form" id="text_contribution_form" action="" method="POST">  
		<!-- The text content form field -->
		<textarea name="text_content" id="text_content"></textarea>  
	</form>
</div>

<div id="image_contribution_div">
	<form name="image_contribution_form" id="image_contribution_form" action="controller/submit_image.php" method="POST" enctype="multipart/form-data">
		<!-- Path to the image file to upload -->
		<input type="file" name="image_file" id="image_file"/>
	</form>
</div>	
