<?php


if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

/**
 * This class handles the creation of pages.
 */
class SemanticLabEmailingPageCreator {


	public static function create( $titleText, $template, $prefix ) {

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

		// Substitute
		$finalText = self::subsText( $templateText, $titleText );

		// Create page. Since only one, let's do straight
		$article = new Article( Title::newFromText( $pagename ) );

		$content = \ContentHandler::makeContent(
				$titleText,
				$article->getTitle(),
				CONTENT_MODEL_WIKITEXT
		);

		$article->doEditContent( $content, $edit_summary );

		// Let's return page name
		return $article->getTitle;

	}

	private static function subsText( $templateText, $titleText ) {

		// Only one variable
		$templateEnd = str_replace( "#1", $titleText, $templateText );

		$templateEnd = str_replace("??", "{{", $templateEnd);
		$templateEnd = str_replace("!!", "}}", $templateEnd);

		return $templateEnd;

	}


}