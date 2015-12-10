<?php


if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

/**
 * This class handles the creation of pages.
 */
class SemanticLabEmailingPageCreator {


	public static function create( $title_text, $template, $prefix ) {

		// Template page
		$origin = "MediaWiki:SemanticLabEmailing-$template";

		//Final page
		$pagename = $prefix.":".$title_text;

		// Read template page

		// Substitute

		// Create page

	}

}