<!-- The bookmark Modal -->
<div id="highlighter-modal" class="highlighter-modal">
	<!-- Modal content -->
	<div class="highlighter-modal-content">
		<div class="highlighter-modal-header">
			<span class="highlighter-modal-close">&times;</span>
			<h3>Bookmark pragraph..</h3>
		</div>
		<div class="highlighter-modal-inner-content">
			<div id="ajax-response">

			</div>
		</div>
		<div class="highlighter-modal-footer">
			<botton class="highlighter-success highlighter-button close-modal">Okay</botton>
			<?php echo PostHighlighter\Helpers::get_button_saving(); ?>
			<a class="highlighter-danger highlighter-button" href="#" id="delete-saved-paragraph">Delete</a>
		</div>
	</div>
</div>

<?php if( PostHighlighter\Helpers::show_double_tapping_modal() ): ?>
<!-- Show double tapping explaining modal -->
<div id="double-tapping-explaining" class="highlighter-modal">
	<!-- Modal content -->
	<div class="highlighter-modal-content">
		<div class="highlighter-modal-header">
			<span class="highlighter-modal-close">&times;</span>
			<h3>Double tapping any paragraph..</h3>
		</div>
		<div class="highlighter-modal-inner-content">
			Double tapping any paragraph stores it in a personal library and that they can deactivate this feature
		</div>
		<div class="highlighter-modal-footer">
			<botton class="highlighter-success highlighter-button close-modal">Okay</botton>
		</div>
	</div>
</div>
<?php endif; ?>