<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 

class SpecialSemanticLabEmailingFeedback extends SpecialPage {

	public function __construct($request = null) {
		parent::__construct('SemanticLabEmailingFeedback');   #The first argument must be the name of your special page

	}

	public function execute($par) {

		global $wgOut;
		global $wgSemanticLabEmailingCreatePage;

		$this->setHeaders();

		// Get this info from 
		$formDescriptorUpload = array();

		$htmlForm = new HTMLForm( $formDescriptorUpload, 'semanticlabemailing_form' );

		$htmlForm->setSubmitText( 'Send' ); //TODO: Change button label

		/* We set a callback function */
		$htmlForm->setSubmitCallback( array( 'SpecialSemanticLabEmailingFeedback', 'processInput' ) );  # Call processInput() in SpecialBioParser on submit

		$htmlForm->suppressReset(false); # Get back reset button

		$wgOut->addHTML( "<div class='semanticlabemailing_section'>" );
		$htmlForm->show(); # Displaying the form
		$wgOut->addHTML( "</div>" );

	}

	static function processInput( $formData ) {

		global $wgSemanticLabEmailingCreatePage;

		// TODO: Parse $formData

		// TODO: API call

		return "DONE";
	}
}