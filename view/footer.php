
<div id="theme_navigation_button">	
<!-- 
	When we do not display the previous button, we use jQuery's hide() function on the following
	theme_navigation_button_previous span element.
	
	This means that the display:none style is applied to the button span which eliminates the 
	content of this div and shifts all of the following divs (theme_date_display container div 
	and theme_navigation_button container div for the next theme button).
	
	In order to avoid this shift, we add a dummy invisible content: &nbsp;
-->
	<span>&nbsp;</span><span id="theme_navigation_button_previous">&#8678</span>
</div>

<div id="theme_date_display">
	<?php echo $theme_manager->getDisplayedThemeDate();?>
</div>


<div id="theme_navigation_button">
	<span id="theme_navigation_button_next">&#8680</span>
</div>

