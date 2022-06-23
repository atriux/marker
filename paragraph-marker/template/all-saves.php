<div style="margin:15px">
	<h1>All Saves</h1>
	<div>
		<?php
		$wp_list_table = new PostHighlighter\AllSaves();
		$wp_list_table->prepare_items();
		$wp_list_table->display();
		?>
	</div>
</div>