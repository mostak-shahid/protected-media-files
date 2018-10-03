jQuery(document).ready(function($) {
	$('.p-file-link').click(function(e){
		e.preventDefault();
		var id = $(this).data('id');
		$.ajax({
			type: "POST",                 // use $_POST request to submit data
			url: ajax_url,      // URL to "wp-admin/admin-ajax.php"
			data: {
				action		: 'pmf', // wp_ajax_*, wp_ajax_nopriv_*
				id			: id,      // PHP: $_POST['first_name']
				//last_name	: 'Cena',      // PHP: $_POST['last_name']
			},
			success:function( data ) {
				//console.log(data);
				$( '#pmfModal .modal-title' ).html( data.title );
				$( '#pmfModal .modal-body' ).html( data.content + data.media );	
				console.log(data.media);			
				$('#pmfModal').modal({show:true});
			},
			error: function(){
				console.log(errorThrown); // error
			}
		});

		
    });

});