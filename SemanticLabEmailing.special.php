<?php
if (!defined('MEDIAWIKI')) { die(-1); } 
 

class SpecialSemanticLabEmailingFeedback extends SpecialPage {

	public function __construct($request = null) {
		parent::__construct('SemanticLabEmailingFeedback');   #The first argument must be the name of your special page

	}

	public function execute($par) {

		$output = $this->getOutput();
		global $wgSemanticLabEmailingCreatePage;

		$this->setHeaders();

		$request = $this->getRequest();

		$output->addModules( 'ext.SemanticLabEmailingForm' );

		$targetURL = "";
		if ( $request->getCheck("target") ) {
			global $wgServer;
			global $wgArticlePath;
			
			$targetPage = $request->getVal("target");

			$targetURL = $wgServer.str_replace( "$1", $targetPage, $wgArticlePath );

		}

		$output->addHTML( "<div class='semanticlabemailing_feedback'>".wfMessage( 'semanticlabemailingfeedback-intro' )->params( $targetURL )->plain()."</div>" );

		$output->addHTML( "<div class='semanticlabemailing_form'>" );

		$formContent = "";

		if ( $request->getCheck("emailing") && $request->getCheck("target") ) {
			// Let's start talking
			$emailing = $request->getVal("emailing");
			$target = $request->getVal("target");

			if ( $request->getCheck("values") ) {
				// We get values
				$values = $request->getVal("values");
				// API process - trigger directly

			} else {
				if ( array_key_exists( $emailing, $wgSemanticLabEmailingCreatePage ) ) {
					if ( array_key_exists( "options", $wgSemanticLabEmailingCreatePage[$emailing] ) ) {

						// Fix method and action
						$formContent = $formContent . "<form data-emailing='".$emailing."' data-target='".$target."'>";

						$options = $wgSemanticLabEmailingCreatePage[$emailing]["options"];

						foreach ( $options as $option ) {
							$name = $option["name"];

							$default = $option["default"];
							$value = $option["value"];
							
							if ( is_array($value) ) {

								$label = array();

								if ( array_key_exists( "label", $option ) ) {
									$label = $option["label"];
									if ( is_array( $label ) && ( count( $option["label"] ) == count( $option["value"] ) ) ) {
										// Do nothing
									} else {
										$label = array();
									}
								}
								
								$formContent = $formContent."<label>".$name."</label>";
								$formContent = $formContent."<select>";

								$v = 0;
								foreach ( $value as $valuee ) {

									$labelstr = null;
									if ( array_key_exists( $v, $label ) ) {
										$labelstr = $label[$v];
									}

									$defaultstr = "";

									if ( $valuee == $default ) {
										$defaultstr = " selected=selected";
									}

									if ( $labelstr ) {
										$formContent = $formContent."<option value='".$valuee."'".$defaultstr.">".$labelstr."</option>";
									} else {
										$formContent = $formContent."<option".$defaultstr.">".$valuee."</option>";
									}

									$v++;

								}

								$formContent = $formContent."</select>";
							} else {
								// Handle input
								// TODO: Pending case
							}
						}

						$formContent = $formContent . "<button>Submit</button>";
						$formContent = $formContent . "</form>";
					}
				}
			}
		}

		$output->addHTML( $formContent );
		$output->addHTML( "</div>" );

		return true;

	}

	static function processInput( $formData ) {

		global $wgSemanticLabEmailingCreatePage;

		// TODO: Parse $formData

		// TODO: API call

		return "DONE";
	}
}