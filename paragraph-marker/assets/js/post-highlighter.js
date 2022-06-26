jQuery(document).ready(function($){
	/** Show double tapping explaining modal **/
	if( $("#double-tapping-explaining").length ){
		$("#double-tapping-explaining").show();
		$("body").addClass("highlighter-opened");
	}
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
				if( $("#ajax-response #delete-paragraph-link").length ){
					$("#highlighter-modal .highlighter-modal-footer #delete-saved-paragraph").show();
					$("#highlighter-modal .highlighter-modal-footer #delete-saved-paragraph").attr("href", $("#ajax-response #delete-paragraph-link").attr("href") );
				}else{

					$("#highlighter-modal .highlighter-modal-footer #delete-saved-paragraph").hide();
				}
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
	/** Change paragraph saving setting **/
	$(".change-paragraph-saving").on("click",function(){
		var current_button = $(this),
		data = {
			'action': 'paragraph_saving_setting',
			'paragraph_saving': current_button.data("paragraph-saving")
		};
		$.ajax({
			url: post_highlighter.ajax_url,
			data: data,
			method: 'POST',
			success: function( response ){
				current_button.data("paragraph-saving",response.data.saving)
				.text(response.data.button_title);
				location.reload(); 
			}
		});
	});
	/** Bar chart in widget **/
	if( $(".widget_post-highlighter-chart .chart-outer canvas.canvas-chart").length ){
		$.ajax({
			url: post_highlighter.ajax_url,
			data:{
				action:'post_highlighter_chart_data'
			},
			success:function( response ){
				console.log( response );
			}
		});

		const labels = [
			'Per Day',
			'Last 7 Days',
			'Current Week',
			'Previous Week'
		];
		const data = {
			labels: labels,
			datasets: [{
				label: 'Number of Paragraphs saved',
				backgroundColor: 'rgb(255, 99, 132)',
				borderColor: 'rgb(255, 99, 132)',
				borderWidth: 1,
				barThickness: 25,
				data: [100, 450,350,200],
			}]
		};
		const config = {
			type: 'bar',
			data: data,
			options: {}
		};
		$(".widget_post-highlighter-chart .chart-outer canvas.canvas-chart").each(function(){
			console.log( $(this) );
			new Chart($(this),config);
		});
	}
	/**/
});
