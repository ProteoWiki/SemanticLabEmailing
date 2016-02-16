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

		$output->addHTML( "<div class='semanticlabemailing_section'>" );

		if ( $request->getCheck("emailing") && $request->getCheck("target") ) {
			// Let's start talking
			$emailing = $request->getVal("emailing");
			$target = $request->getVal("target");

			if ( $request->getCheck("values") ) {
				// We get values
				$values = $request->getVal("values");
				// API process
			} else {
				if ( array_key_exists( $emailing, $wgSemanticLabEmailingCreatePage ) ) {
					if ( array_key_exists( "options", $wgSemanticLabEmailingCreatePage[$emailing] ) ) {
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
								
								foreach ( $value as $valuee ) {
									

								}
							}
						}

					}
				}

			}
		}

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