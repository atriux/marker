jQuery(document).ready(function($){
	/** Hide/Show modal **/
	function highlight_modal( show = true ){
		if( show ){
			$("#highlighter-modal").show();
			$("#ajax-response").html(`<p>Adding bookmark <i class="fas fa-sync-alt fa-spin"></i></p>`);
			$("body").addClass("highlighter-opened");
		}else{
			$(".highlighter-modal").hide();
			$("body").removeClass("highlighter-opened");
		}
	}
	// Get the modal
	var highlighter_modal = $(".highlighter-modal"),
	close_modal = $(".highlighter-modal-close , .highlighter-modal .close-modal");

	$(close_modal).on("click",function(){
		highlight_modal( false );
	});

	
	$(".post-highligher-get-paragraphs p").on("dblclick",function(){
		highlight_modal();
		var save_paragraph = {
			'action': 'save_paragraph',
			'paragraph_html': $(this).text(),
			'post_id':('post_id' in post_highlighter ? post_highlighter.post_id : '' )
		};
		/** Save paragraph **/
		$.ajax({
			type: "POST",
			url: post_highlighter.ajax_url,
			data: save_paragraph,
			success: function( response ){
				$("#ajax-response").html( response );
			}
		});
	});
	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
		if ( $(event.target).is(highlighter_modal) ) {
			highlight_modal( false );
		}
	}
	$(".open-delete-confirmation").on("click",function( event ){
		event.preventDefault();
		$("#highlighter-delete-modal").show();
		$("body").addClass("highlighter-opened");
		$("#highlighter-delete-modal .highlighter-danger").attr("href",$(this).attr("href"));
	});

	/*$("#posthighlighter-highlightr-user a.paragraph-action").on("click",function( event ){
		var share_on = $(this).data("share-on");
		if( share_on ){
			event.preventDefault();
			var data = {
				'action': 'share_social_network',
				'share_on': share_on,
				'user_paragraph':$(this).data('user-paragraph-id')
			};
			$.post(post_highlighter.ajax_url, data, function(response) {
				alert("Post need to save to " + share_on);
			});
		}
	});*/
});