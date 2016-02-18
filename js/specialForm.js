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

		var inputs = $(form).children( "input", "select" );
		$(inputs).each(function( index ) {
			valueArr.push( $( inputs[index] ).val() );
		});

		console.log( valueArr.join("*") );

		// Process API from this point
		
	});

}( jQuery, mediaWiki ) );
