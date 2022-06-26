<div id="posthighlighter-highlightr-top">
	<div>
		<div class="tabular-design">
			<div class="single-row heading-row">
				<div class="highlighter-paragraph">
					Paragraph
				</div>
				<div class="highlighter-post">
					Post
				</div>
			</div>
			<?php foreach( $all_paragraphs as $single_paragraph ): 
				$post_id = get_post_meta($single_paragraph->paragraph_post_id,'post_id',true);
				if( $post_id )
					$main_post = get_post($post_id);
				?>
				<div class="single-row">
					<div class="highlighter-paragraph">
						<?php echo get_post_meta($single_paragraph->paragraph_post_id,'paragraph',true); ?>
					</div>
					<div class="highlighter-post">
						<?php if( isset($main_post) && $main_post->post_status == "publish" ): ?>
							<a href="<?php echo get_permalink($main_post->ID); ?>" target="_blank">
								<?php echo $main_post->post_title; ?>
							</a>
						<?php else: ?>
							Post removed
							<?php 
						endif;//
						
						echo "({$single_paragraph->counter})";
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if( $previous_link || $next_link ): ?>
			<div class="highlighter-pagination">
				<a href="<?php echo $previous_link ?>" class="<?php echo ( $previous_link ? "" : "disabled" ) ?>">❮</a>
				<a href="<?php echo $next_link ?>" class="<?php echo ( $next_link ? "" : "disabled" ) ?>">❯</a>
			</div>
		<?php endif; ?>
	</div>
</div>