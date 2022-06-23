<div style="margin:15px">
	<h1>Saves by popularity</h1>
	<div>
		<?php
		$wp_list_table = new PostHighlighter\SavesByPopularity();
		$wp_list_table->prepare_items();
		$wp_list_table->display();
		?>
	</div>
</div>