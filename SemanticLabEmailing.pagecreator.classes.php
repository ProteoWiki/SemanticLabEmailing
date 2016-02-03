<?php


if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

/**
 * This class handles the creation of pages.
 */
class SemanticLabEmailingPageCreator {


	public static function actOnPage( $titleText, $template, $prefix, $method, $values ) {

		// Template page
		$templatePage = "MediaWiki:SemanticLabEmailing-$template";

		//Final page
		$pagename = $prefix.":".$titleText;

		// Read template page
		$templateID = Title::newFromText($templatePage)->getArticleID(); //Get the id for the article called Test_page
		$templateArticle = Article::newFromId($templateID); //Make an article object from that id
		$templateText = $templateArticle->getRawText();

		if ( empty($templateText) || empty($titleText) ) {
			return false;
		}

		// Check if null de target pagename, otherwise stop


		// Substitute
		if ( $method = 'create' ){
			$values = -1;

			// If null target pagename OK

		} else {

			// If null target pagename -> STOP

			// First check user -> associated
	
			// Get revisions
			$revs = self::getallRevs( $templateID );
	
			// Count revs
	
			// If num revs is lower than proceed

		}

		$finalText = self::subsText( $templateText, $titleText, $values );

		// Create page. Since only one, let's do straight
		$article = new Article( Title::newFromText( $pagename ) );

		$content = \ContentHandler::makeContent(
				$finalText,
				$article->getTitle(),
				CONTENT_MODEL_WIKITEXT
		);

		$article->doEditContent( $content, $edit_summary );

		// Let's return page name
		return $article->getTitle;

	}

	private static function subsText( $templateText, $titleText, $values = "-1" ) {

		// Only one variable
		$templateEnd = str_replace( "#1", $titleText, $templateText );

		// TODO: Allow diferent separators
		$listValues = split( "-", $values );
		$iter = 1;
		foreach( $values as $value ) {
			$iter++;
			$subsVar = "#" + $iter;
			$templateEnd = str_replace( $subsVar, $value, $templateText );
		}

		$templateEnd = str_replace("??", "{{", $templateEnd);
		$templateEnd = str_replace("!!", "}}", $templateEnd);

		return $templateEnd;

	}

	private static function getallRevs( $pageid ) {

	}

}