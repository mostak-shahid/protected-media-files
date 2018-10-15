jQuery(document).ready(function($) {
	$('.p-file-link').click(function(e){
		e.preventDefault();
		var id = $(this).data('id');
		$.ajax({
			type: "POST",                 // use $_POST request to submit data
			url: ajax_obj.ajax_url,      // URL to "wp-admin/admin-ajax.php"
			data: {
				action		: 'pmf', // wp_ajax_*, wp_ajax_nopriv_*
				id			: id,
				security	: ajax_obj.ajax_nonce
			},
			success:function( data ) {
				//console.log(data);
				$( '#pmfModal .modal-title' ).html( data.title );
				$( '#pmfModal .modal-body' ).html( data.content + data.media );	
				//console.log(data.media);			
				$('#pmfModal').modal({show:true});
			},
			error: function(){
				console.log(errorThrown); // error
			}
		});
		
    });
    $('#pmf_poup').click(function() {
	    var pmf_pcode = prompt("Please enter your code:", "");
	    if (pmf_pcode == null || pmf_pcode == "") {
	        alert("User cancelled the prompt.");
	    } else {
	    	//txt = "Hello " + pmf_pcode + "! How are you today?";

			$.ajax({
				type: "POST",                 // use $_POST request to submit data
				url: ajax_obj.ajax_url,      // URL to "wp-admin/admin-ajax.php"
				data: {
					action		: 'pmf_login', // wp_ajax_*, wp_ajax_nopriv_*
					pmf_pcode	: pmf_pcode,
					security	: ajax_obj.ajax_nonce
				},
				success:function( data ) {
					console.log(data);
					if (data.pmf_access) {
						//alert('Logged in');
						setPmfCookie('pmf_access', 1, 1);
						//redirect
						window.location.href = data.redirect;
					}
				},
				error: function(){
					console.log(errorThrown); // error
				}
			});	        
	    }
	});
    $('#pmf_logout').click(function(e) {
		e.preventDefault();
		$url = $(this).attr('href');
    	setPmfCookie('pmf_access', '', '-1');
    	window.location.href = $url;
	});
});

function setPmfCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getPmfCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}