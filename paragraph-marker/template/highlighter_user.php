<div id="posthighlighter-highlightr-user">
	<?php if( !empty($_GET['highligher_success']) ): ?>
		<div class="highlighter-success-message">
			<p> <?php echo $_GET['highligher_success'] ?> </p>
		</div>
	<?php endif; ?>
	<div>
		<?php
		if( !is_user_logged_in() ){
			echo '<p class="warning">Paragraph are saved temporarily. Log in to avoid losing it.</p>';
		}
		PostHighlighter\Helpers::get_button_saving();
		?>
		<div class="tabular-design">
			<div class="single-row heading-row">
				<div class="highlighter-paragraph">
					Paragraph
				</div>
				<div class="highlighter-post">
					Post
				</div>
				<div class="highlighter-added-on">
					Added on
				</div>
				<div class="highlighter-action">
					Actions
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
						?>
					</div>
					<div class="highlighter-added-on">
						<?php echo $single_paragraph->highlighted_on; ?>
					</div>
					<div class="highlighter-action">
						<a href="<?php echo add_query_arg( ['action'=>'delete_paragraph','paragraph_id'=>$single_paragraph->id] , get_page_link() ); ?>" class="open-delete-confirmation paragraph-action">
							<i class="fas fa-trash-alt"></i>
						</a>
						<?php 
						if( isset($main_post) && $main_post->post_status == "publish" ){
							include( POST_HIGHLIGHTER_PATH . "template/share-social.php" );
						}
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
<!-- Delete confirmation popup -->
<div id="highlighter-delete-modal" class="highlighter-modal">
	<!-- Modal content -->
	<div class="highlighter-modal-content">
		<div class="highlighter-modal-header">
			<span class="highlighter-modal-close">&times;</span>
			<h3>Delete</h3>
		</div>
		<div class="highlighter-modal-inner-content">
			<p>Are you sure to delete paragraph?</p>
		</div>
		<div class="highlighter-modal-footer">
			<a href="#" class="highlighter-danger highlighter-button close-modal">Yes</a>
			<botton class="highlighter-success highlighter-button close-modal">Cancel</botton>
		</div>
	</div>
</div>