// Javascript for handling form calls 

( function ( $, mw ) {

	// On click button
	$(".semanticlabemailing_form form").on( "click", "button", function( event ) {
	
		event.preventDefault();
		var form = $(this).parents("form")[0];
	
		var emailing = $(form).data("emailing");
		var target = $(form).data("target");
	
		// Parse all default stuff as well
		var valueArr = [];

		var inputs = $(form).children( "input, select" );
		$(inputs).each(function( index ) {
			valueArr.push( $( inputs[index] ).val() );
		});

		// Process API from this point

		var params = {};
		params.action = "semanticlabemailing";
		params.emailing = emailing;
		params.target = target;
		params.method = "update";
		params.values = valueArr.join("*");
		params.format = "json"; // Let's put JSON
		
		var posting = $.get( wgScriptPath + "/api.php", params );
		posting.done(function( out ) {

			if ( out && out.hasOwnProperty("semanticlabemailing")) {

				if ( out["semanticlabemailing"].hasOwnProperty("status") ) {
					var status = out["semanticlabemailing"]["status"];
					$(".semanticlabemailing_form").empty();
					$(".semanticlabemailing_form").append( "<p class='status'>" + status + "</p>" );

				}
			}

		})
		.fail( function( out ) {
			console.log("Error!");
		});

	});

}( jQuery, mediaWiki ) );
