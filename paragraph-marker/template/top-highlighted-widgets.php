<?php
	//print_r( $all_paragraphs );
?>
<div class="paragraphs-listing">
<?php foreach( $all_paragraphs as $single_paragraph ): ?>
	<div>
		<div> 
			<?php echo get_the_title($single_paragraph->paragraph_post_id); ?>
			<?php echo $single_paragraph->counter; ?>
		</div>
	</div>
<?php endforeach; ?>
</div>

<p>
	<?php
	
	?>
</p>