<?php 
use PostHighlighter\Helpers;
?>
<a class="paragraph-action" href="<?php echo Helpers::get_social_share_link($single_paragraph,'linkedin'); ?>" target="_blank">
	<i class="fab fa-linkedin"></i>
</a>
<a class="paragraph-action" href="<?php echo Helpers::get_social_share_link($single_paragraph,'twitter'); ?>" target="_blank">
	<i class="fab fa-twitter fa-2x"></i>
</a>
<a class="paragraph-action" href="<?php echo Helpers::get_social_share_link($single_paragraph,'facebook'); ?>" target="_blank">
	<i class="fab fa-facebook-f fa-2x"></i>
</a>
<a href="<?php echo Helpers::get_social_share_link($single_paragraph,'whatsapp'); ?>" class="paragraph-action" target="_blank">
	<i class="fab fa-whatsapp fa-2x"></i>
</a>
<a class="paragraph-action" href="<?php echo Helpers::get_social_share_link($single_paragraph,'email'); ?>" target="_blank">
	<i class="fas fa-envelope-square fa-2x"></i>
</a>