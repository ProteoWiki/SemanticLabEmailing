<?php


if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}


// TODO: A lot of msg to pass
/**
 * This class handles the creation of pages.
 */
class SemanticLabEmailingPageCreator {


	public static function actOnPage( $emailing, $titleText, $method="update", $values="-1" ) {

		global $wgSemanticLabEmailingCreatePage;

		if ( ! array_key_exists( $emailing, $wgSemanticLabEmailingCreatePage ) ) {
			return null;
		}

		// Template page
		$templatePage = $wgSemanticLabEmailingCreatePage[$emailing]["template"];

		//Final page
		if ( array_key_exists( "prefix", $wgSemanticLabEmailingCreatePage[$emailing] ) ) {
			$pagename = $wgSemanticLabEmailingCreatePage[$emailing]["prefix"].":".$titleText;
		} else {
			$pagename = $titleText;
		}

		$maxrevs = $wgSemanticLabEmailingCreatePage[$emailing]["maxrevs"];


		// Read template page
		$templateID = Title::newFromText($templatePage)->getArticleID(); //Get the id for the article called Test_page
		$templateArticle = Article::newFromId($templateID); //Make an article object from that id
		$templateText = $templateArticle->getRawText();

		if ( empty($templateText) || empty($titleText) ) {
			return false;
		}

		// Check if null de target pagename, otherwise stop
		$pagenameTitle = Title::newFromText( $pagename );
		$pagenameWiki = WikiPage::factory( $pagenameTitle );
		

		// Substitute
		if ( $method == 'create' ){
			$values = "-1";

			if ( $pagenameWiki->exists() ) {
				return 'Page already exists'; // Pagename exists -> abort
			}

		} else {

			if ( ! $pagenameWiki->exists() ) {
				return 'Page does not exist'; // Pagename does not exist -> abort
			}

			// TODO: First check user -> associated
	
			// Get revisions
			$revs = self::getallRevs( $templateID );
	
			// Count revs
			$numrevs = sizeof( $revs );
	
			// If num revs is lower than proceed
			if ( $numrevs > $maxrevs ) {
				return( "No more changes allowed" );
			}

			// If last rev is too old, block
			$lastRevDate = $revs[0]["r.rev_timestamp"];
			$currentDate = date('YmdHis');

			if ( $currentDate > ( $lastRevDate + $wgSemanticLabEmailingCreatePage[$emailing]["time"] ) ) {
				return( "Too old rev" );
			}
			
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

		// Let's return something
		return "Success!";

	}

	private static function subsText( $templateText, $titleText, $values = "-1" ) {

		// Only one variable
		$templateEnd = str_replace( "#1", $titleText, $templateText );

		// TODO: Allow diferent separators
		$listValues = explode( "*", $values );
		$iter = 1;
		foreach( $listValues as $value ) {
			$iter++;
			$subsVar = "#".$iter;
			$templateEnd = str_replace( $subsVar, $value, $templateText );
		}

		$templateEnd = str_replace("??", "{{", $templateEnd);
		$templateEnd = str_replace("!!", "}}", $templateEnd);

		return $templateEnd;

	}

	private static function getallRevs( $pageid ) {

		$db = wfGetDB( DB_SLAVE );
		$columns = array();
		$condoptions = array();

		array_push( $columns, 'r.rev_id' );
		array_push( $columns, 'r.rev_timestamp' );

		$from = array( 'r' => 'revision', 'p' => 'page' );

		$where = array( 'r.rev_page = p.page_id' );

		array_push( $where, "p.page_id = ".$pageid);

		$options['ORDER BY'] = 'r.rev_timestamp DESC';
		$options['DISTINCT'] = true;

		$revs = self::searchDB( $db, $from, $columns, $where, $options, $condoptions );

		return $revs;
	}


    private static function searchDB( $db, $table, $vars, $conds, $options, $condoptions=array(), $count ) {
		
		$result = $db->select( $table, $vars, $conds, 'SemanticLabEmailingPageCreator::searchDB', $options, $condoptions );
		if ( !$result ) {
			// echo ( wfMsgExt( "listchanges-db-invalid-query", array( 'parse', 'escape' ) ) );
			return false;
		} else {
			$rows = array();
			while ( $row = $db->fetchRow( $result ) ) {
				// Create a new row object, that uses the
				// passed-in column names as keys, so that
				// there's always an exact match between
				// what's in the query and what's in the
				// return value (so that "a.b", for instance,
				// doesn't get chopped off to just "b").
				$new_row = array();
				foreach ( $vars as $i => $column_name ) {
					// Convert the encoding to UTF-8
					// if necessary - based on code at
					// http://www.php.net/manual/en/function.mb-detect-encoding.php#102510
					$dbField = $row[$i];
					if ( !function_exists( 'mb_detect_encoding' ) ||
						mb_detect_encoding( $dbField, 'UTF-8', true ) == 'UTF-8' ) {
							$new_row[$column_name] = $dbField;
						} else {
							$new_row[$column_name] = utf8_encode( $dbField );
						}
				}
				$rows[] = $new_row;
			}
			
			return $rows;
		}
	}

}