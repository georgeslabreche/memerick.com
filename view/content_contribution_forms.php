<div id="text_contribution_div">
	<form name="text_contribution_form" id="text_contribution_form" action="" method="POST">  
		<!-- The text content form field -->
		<textarea name="text_content" id="text_content"></textarea>
		
		<!-- 
			Set year and month of them we are currently displaying.
			This is so that we store the text with the correct theme year and month value. 
		-->
		<input type="hidden" id="year" name="year" value="<?php echo $theme_manager->getDisplayedThemeYear();?>" />
		<input type="hidden" id="month" name="month" value="<?php echo $theme_manager->getDisplayedThemeMonth();?>" />  
	</form>
</div>

<div id="image_contribution_div">
	<form name="image_contribution_form" id="image_contribution_form" action="controller/submit_image.php" method="POST" enctype="multipart/form-data">
		<!-- Path to the image file to upload -->
		<input type="file" name="image_file" id="image_file"/>
		
		<!-- 
			Set year and month of them we are currently displaying.
			This is so that we tag image with the correct theme tag(s). 
		-->
		<input type="hidden" id="year" name="year" value="<?php echo $theme_manager->getDisplayedThemeYear();?>" />
		<input type="hidden" id="month" name="month" value="<?php echo $theme_manager->getDisplayedThemeMonth();?>" />
	</form>
</div>	
