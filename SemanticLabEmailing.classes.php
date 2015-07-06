<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

if ( !defined( 'SMW_VERSION' ) ) {
	echo 'This extension requires Semantic MediaWiki to be installed.';
	exit( 1 );
}

// TODO: Update this for something easier to handle
// constants for message type
define( 'NEW_REQUEST', 0 );
define( 'UPDATE', 1 );
define( 'ACCEPTED', 2 );
define( 'CLOSED', 3 );
define( 'DISCARDED', 4 );
define( 'UPDATE_TEAM', 5 );
define( 'ACCEPTED_TEAM', 6 );
define( 'CLOSED_TEAM', 7 );
define( 'DISCARDED_TEAM', 8 );
define( 'ASSIGNED', 9 );
define( 'NEW_EXPERIMENT', 10 );
define( 'UPDATE_EXPERIMENT', 11 );
define( 'CLOSED_EXPERIMENT', 12 );
define( 'WEEK_EXTENSION', 13 );


/**
 * This class handles the creation and sending of notification emails.
 */
class SemanticLabEmailingMailer {

	private static $request_assignees;	
	private static $request_status;
	private static $request_week;
	private static $request_sample;
	private static $experiment_status;
	private static $user_verbose;

	public static function findOldValues( &$article, &$user, &$text, &$summary, $minor, $watch, $sectionanchor, &$flags ) {
	
		global $wgCanonicalNamespaceNames;

		$title = $article->getTitle();
		$title_text = $title->getFullText();
		$ns = $article->getTitle()->getNamespace();

		// we get parameters from user
		global $wgSemanticLabEmailingVerboseProp;
		self::$user_verbose = self::getUserParam( $wgSemanticLabEmailingVerboseProp, $user );
		
		if ($wgCanonicalNamespaceNames[$ns] == 'Request' ) {

			// Status of the request
			global $wgSemanticLabEmailingPropsCheck;
			global $wgSemanticLabEmailingExtensionProp;

			// We assume only one prop for Request
			$status = self::getStatus( $wgSemanticLabEmailingPropsCheck['Request'][0], $title_text, $user );
			$week = self::getStatus( $wgSemanticLabEmailingExtensionProp, $title_text, $user );
			
			if ( count( $status ) > 0 ) {
				self::$request_status = $status[0];
			} else {
				self::$request_status = "";
			}

			if ( count( $week ) > 0 ) {
				self::$request_week = $week[0];
			} else {
				self::$request_week = "";
			}
			

			// Assigned to
			global $wgSemanticLabEmailingAssignedProp;
			$assignees = self::getAssignees( $wgSemanticLabEmailingAssignedProp['Request'], $title_text, $user );

			if ( count( $assignees ) > 0 ) {
					self::$request_assignees = $assignees;
			} else {
					self::$request_assignees = "";
			}

		}
		
		if ( $wgCanonicalNamespaceNames[$ns] == 'Experiment' ) {

			global $wgSemanticLabEmailingPropsCheck;
			
			// We get all props
			foreach ( $wgSemanticLabEmailingPropsCheck['Experiment'] as $prop2Check ) {
			
				$prop2Check = str_replace( " ", "_", $prop2Check );
				// Status of the request
				$status = self::getStatus( $prop2Check, $title_text, $user );
				
				if ( count( $status ) > 0 ) {
					self::$experiment_status[$prop2Check] = $status[0];
				} 
			}

		}

		return true;
	}

	public static function mailUpdatedTask( $article, $current_user, $text, $summary, $minoredit, $watchthis, $sectionanchor, $flags, $revision ) {
		
		global $wgCanonicalNamespaceNames;
	
		if ( !$minoredit ) {

 			if ( ( $flags & EDIT_NEW ) && !$article->getTitle()->isTalkPage() ) {
 				$status = NEW_REQUEST;
 			} else {
 				$status = UPDATE;
 			}
			
			// We will process as well depending on Namespace: Request and Experiment
			$ns = $article->getTitle()->getNamespace();

			self::printDebug( $ns );

			if ( $wgCanonicalNamespaceNames[$ns] == 'Request' ) {
		
				if ( $status == NEW_REQUEST ) {
				self::mailNewRequest( $article, $text, $current_user, $status );
				}
				if ( $status == UPDATE ) {
					self::mailEditRequest( $article, $text, $current_user, $status );
				}
			}

			if ( $wgCanonicalNamespaceNames[$ns] == 'Experiment' ) {
			
			
			 if ( $status == NEW_REQUEST ) {
				// Change status
				$status = NEW_EXPERIMENT;
				self::mailNewExperiment( $article, $text, $current_user, $status );
			 }
			 if ( $status == UPDATE ) {
				// Change status
				$status = UPDATE_EXPERIMENT;
				self::mailEditExperiment( $article, $text, $current_user, $status );
			 }
			
			}
		}

		return true;
	}

	
	// Function for user props
	static function getUserParam( $param, $user ) {
		
		$userpage = $user->getUserPage();
		$status = self::getStatus( $param, $userpage->getFullText(), $user );
		
		return $status[0];
	}
	
	
	static function listUsersGroup( $group ) {

		$context = new RequestContext();
		$list = array();
		
		// Get email addresses
		$upsm = new UsersPager($context, $group );
		$usersbodysm = $upsm->getBody();
		
		if (preg_match_all('/title="User:(\S+)"/', $usersbodysm, $groupusers)) {
		   $list = $groupusers[1];
		}
		
		return $list;
	}
	
	
	// Function for Requesters
	static function mailNewRequest( $article, $text, $user, $status ) {

		$title = $article->getTitle();
		$title_text = $title->getPrefixedText();
		self::printDebug( "Title text: $title_text" );

		global $wgSemanticLabEmailingOwnerProp;
		$requesters = self::getAssignees( $wgSemanticLabEmailingOwnerProp['Request'], $title_text, $user );

		// Get supermanager email addresses
		$listusers = self::listUsersGroup("supermanager");

		self::mailNotification( $listusers, "New", $title, $requesters, $status );

		return true;
	}
	
	// Function for Experiments
	static function mailNewExperiment( $article, $text, $user, $status ) {

		$title = $article->getTitle();
		$title_text = $title->getPrefixedText();
		self::printDebug( "Title text: $title_text" );
		
		// Get associated request
		global $wgSemanticLabEmailingReferenceProp;
		$title_request = self::getAssignees( $wgSemanticLabEmailingReferenceProp['Experiment'], $title_text, $user );
		
		// Get requesters
	    // we expect only one request to match experiment -> array
		global $wgSemanticLabEmailingOwnerProp;
		$requesters = self::getAssignees( $wgSemanticLabEmailingOwnerProp['Request'], "Request:".$title_request[0], $user );
		
		self::mailNotification( $requesters, "New", $title, '', $status, "Request:".$title_request[0] );

		return true;
	}
	
	
	// Function for Requests edits
	static function mailEditRequest( $article, $text, $user, $status ) {

		$title = $article->getTitle();
		$title_text = $title->getPrefixedText();

		global $wgUser; // We get User who acts
		$username = $wgUser->getName();	
		$userlink = $wgUser->getUserPage()->escapeFullURL();
		$from_user = array();
		array_push($from_user, $username);	

		global $wgSemanticLabEmailingOwnerProp;
		$requesters = self::getAssignees( $wgSemanticLabEmailingOwnerProp['Request'], $title_text, $user );
		// We put current assignes
		$text = $requesters[0];

		global $wgSemanticLabEmailingPropsCheck;
		$present_status = self::getStatus( $wgSemanticLabEmailingPropsCheck['Request'][0], $title_text, $user );

		if ( $present_status[0] != self::$request_status ) {
			
			if ( $present_status[0] == 'Accepted' ) {
				$status = ACCEPTED;

				// Let's check Extension time
				global $wgSemanticLabEmailingDeliveryTimeProp;
				$week = self::getStatus( $wgSemanticLabEmailingDeliveryTimeProp, $title_text, $user );

				$extra = $week[0];
				
				self::mailNotification( $requesters, $text, $title, $from_user, $status, $extra );
			
				// For the whole team	
				$status = ACCEPTED_TEAM;
				self::mailNotification( self::listUsersGroup("team"), $text, $title, $from_user, $status );

			}

			elseif ( $present_status[0] == 'Closed' ) {
				$status = CLOSED;

				global $wgSemanticLabEmailingClosureProp;
				$creation_date = self::getStatus( '_CDAT', $title_text, $user );
				$closure_date = self::getStatus( $wgSemanticLabEmailingClosureProp, $title_text, $user );
	
				$extra = $creation_date[0]."@".$closure_date[0];

				self::mailNotification( $requesters, $text, $title, $from_user, $status, $extra );
			}
			
			elseif ( $present_status[0] == 'Discarded' ) {
				$status = DISCARDED;

				self::mailNotification( $requesters, $text, $title, $from_user, $status );
			}

			else {
		
				// Temporary out
				// Let's check Extension time
				// $week = self::getStatus( 'PR_Request_WeekExtension', $title_text, $user );

				// if ( $week[0] != self::$request_week ) {
					// We send email about more week
					// Mail notification about change

				//	$status = WEEK_EXTENSION;
				//	$week_comment = self::getStatus( 'PR_Request_ExtensionComment', $title_text, $user );
				//	$extra = $week."@".$week_comment;
				//	self::mailNotification( $requesters, $text, $title, $from_user, $status, $extra );
				//}
			}

		}

		$present_assignees = self::getAssignees( 'PR_Request_AssignedTo', $title_text, $user );
	
		if ($present_assignees != self::$request_assignees) {

			self::mailNotification( $present_assignees, $text, $title, $from_user, ASSIGNED );
		}

		return true;
	}

	   // Function for Experiment edits
	static function mailEditExperiment( $article, $text, $user, $status ) {

		$title = $article->getTitle();
		$title_text = $title->getPrefixedText();
		self::printDebug( "Title text: $title_text" );
		
		// Get associated request
		global $wgSemanticLabEmailingReferenceProp;
		$title_request = self::getAssignees( $wgSemanticLabEmailingReferenceProp['Experiment'], $title_text, $user );
	  
		// Get requesters
		// we expect only one request to match experiment -> array
		global $wgSemanticLabEmailingOwnerProp;
		$requesters = self::getAssignees( $wgSemanticLabEmailingOwnerProp['Request'], "Request:".$title_request[0], $user );
	  
		// Time to get all properties
		global $wgSemanticLabEmailingPropsCheck;
		global $wgSemanticLabEmailingPropsName;
	  
		$modifiedprops = array();
		
		// We get all props
		$iter = 0; // we use it for matching
		
		foreach ( $wgSemanticLabEmailingPropsCheck['Experiment'] as $prop2Check ) {
		
			$prop2Check = str_replace( " ", "_", $prop2Check );
			// Status of the request
			$propstatus = self::getStatus( $prop2Check, $title_text, $user );
			
			if ( count( $propstatus ) > 0 ) {
				if ( self::$experiment_status[$prop2Check] != $propstatus[0] ) {
					
					// First more important
					if ( $prop2Check ==  $wgSemanticLabEmailingPropsCheck['Experiment'][0] ) {
						$status = CLOSED_EXPERIMENT;
						self::mailNotification( $requesters, "", $title, "", $status, "Request:".$title_request[0] );
						return true;
					} else {
						array_push( $modifiedprops, $wgSemanticLabEmailingPropsName[$iter] );
						array_push( $modifiedprops, $propstatus[0] );
					}
					
				}
			}
			
			$iter++;
		}

		if ( count( $modifiedprops ) > 0 ) {
			self::mailNotification( $requesters, "", $title, "", $status, "Request:".$title_request[0]."*".implode(";", $modifiedprops) );   
		}
		
		return true;

	}

	/**
	 * Returns an array of properties based on $query_word
	 * @param $query_word String: the property that designate the users to notify.
	 */
	static function getAssignees( $query_word, $title_text, $user ) {
		// Array of assignees to return
		$assignee_arr = array();

		// get the result of the query "[[$title]][[$query_word::+]]"
		$properties_to_display = array();
		$properties_to_display[0] = $query_word;
		$results = self::getQueryResults( "[[$title_text]][[$query_word::+]]", $properties_to_display, false );

		// In theory, there is only one row
		while ( $row = $results->getNext() ) {
			$task_assignees = $row[1];
		}

		// If not any row, do nothing
		if ( !empty( $task_assignees ) ) {
			while ( $task_assignee = $task_assignees->getNextObject() ) {
				$assignee_name = $task_assignee->getTitle();
				$assignee_name = $assignee_name->getText();
				$assignee_name = explode( ":", $assignee_name );
				$assignee_name = $assignee_name[0];

				array_push( $assignee_arr, $assignee_name );
			}
		}

		return $assignee_arr;
	}

	/**
	 * Returns an array of properties based on $query_word
	 * @param $query_word String: the property that designate the users to notify.
	 */
	static function getStatus( $query_word, $title_text, $user ) {
		// Array of assignees to return
		$assignee_arr = array();

		// get the result of the query "[[$title]][[$query_word::+]]"
		$properties_to_display = array();
		$properties_to_display[0] = $query_word;
		$results = self::getQueryResults( "[[$title_text]][[$query_word::+]]", $properties_to_display, false );

		// In theory, there is only one row
		while ( $row = $results->getNext() ) {
			$task_assignees = $row[1];
		}

		// If not any row, do nothing
		if ( !empty( $task_assignees ) ) {

			while ( $task_assignee = $task_assignees->getNextObject() ) {

				$assignee_name = $task_assignee->getWikiValue();
				$assignee_name = $assignee_name;

				array_push( $assignee_arr, $assignee_name );
			}
		}

		return $assignee_arr;
	}

	/**
	 * Sends mail notifications
	 */
	function mailNotification( $assignees, $text, $title, $user_list, $status, $extra="" ) {
		global $wgSitename;
		global $wgServer;
		global $wgScript;

		global $wgSemanticLabEmailingUsersNoMail; //User that do not receive email

		// If no receivers
		if ( !empty( $assignees ) ) {

			$title_text = $title->getFullText();
			$link = $title->escapeFullURL();

			$to_addresses = array();
			$from_addresses = array();
			$creator = "";
			$creatorlink = "";

			// If no from -> use default
			if ($user_list == '') {
				global $wgSemanticLabEmailingDefaultEmail;
				global $wgSemanticLabEmailingDefaultName;

				array_push($from_addresses, new MailAddress( $wgSemanticLabEmailingDefaultEmail, $wgSemanticLabEmailingDefaultName ));
			}

			else {
				// Get from addresses, should be one
				foreach ($user_list as $user) {

					if ( in_array( $user, $wgSemanticLabEmailingUsersNoMail ) ) {
						continue; // If in list avoid
					}

					$userobj = User::newfromName($user);
					$creator = $userobj->getRealName();
					$creatorlink = $userobj->getUserPage()->escapeFullURL();
					array_push($from_addresses, new MailAddress( $userobj->getEmail(), $userobj->getName() ));
				}
			}

			// Get to adddresses, can be many
			foreach ($assignees as $assignee) {
			
				$to_addresses = array();
				if ( in_array( $assignee, $wgSemanticLabEmailingUsersNoMail ) ) {
					continue; // If in list avoid
				}

				$assigneeobj = User::newfromName($assignee);
				array_push($to_addresses, new MailAddress( $assigneeobj->getEmail(), $assigneeobj->getName() ));
				
				// Let's put extra information to user
				$username = $assigneeobj->getName();
				$userlink = $wgServer.$wgScript."?title=User:".str_replace(" ", "_", $username)."&action=formedit";
				$userwarn = wfMessage( 'semanticlabemailing-user-verbose-check', $userlink )->text();
				$verbose = self::getUserParam( "Has_Verbose_Mailing", $assigneeobj ); // Let's get param from user
				
				$body = "";
				
				if ( $status == NEW_REQUEST ) {
						$subject = wfMessage( 'semanticlabemailing-user-request-new-header', $wgSitename, $title_text, $creator )->text();
						$body =  wfMessage( 'semanticlabemailing-user-request-new', $creator, $creatorlink, $link )->text();
					} elseif ( $status == ACCEPTED ) {
						$subject = wfMessage( 'semanticlabemailing-user-request-accepted-header', $wgSitename, $title_text, $username )->text();
						// We add extra -> Weeks
						$body = wfMessage( 'semanticlabemailing-user-request-accepted', $username, $title_text, $link, $creator, $creatorlink, $extra )->text();
					} elseif ( $status == ACCEPTED_TEAM ) {
						$subject = wfMessage( 'semanticlabemailing-user-request-accepted-header', $wgSitename, $title_text, $text)->text();
						$body = wfMessage( 'semanticlabemailing-user-request-accepted-team', $text, $title_text, $link, $creator, $creatorlink)->text();
					} elseif ( $status == CLOSED ) {
	   
						$creation_date = "";
						$closure_date = "";
				
						if ( $extra ) {
							$parts = explode("@", $extra);
							$creation_date = $parts[0];
							$closure_date = $parts[1];  
						}

						$subject = wfMessage( 'semanticlabemailing-user-request-closed-header', $wgSitename, $title_text, $username)->text();
						$body = wfMessage( 'semanticlabemailing-user-request-closed', $username, $title_text, $link, $creator, $creatorlink, $creation_date, $closure_date)->text();

					} elseif ( $status == CLOSED_TEAM ) {
						$subject = wfMessage( 'semanticlabemailing-user-request-closed-header', $wgSitename, $title_text, $text)->text();
						$body = wfMessage( 'semanticlabemailing-user-request-closed-team', $text, $title_text, $link, $creator, $creatorlink)->text();
					} elseif ( $status == DISCARDED ) {
						$subject = wfMessage( 'semanticlabemailing-user-request-discarded-header', $wgSitename, $title_text, $username)->text();
						$body = wfMessage( 'semanticlabemailing-user-request-discarded', $username, $title_text, $link, $creator, $creatorlink)->text();
					} elseif ( $status == DISCARDED_TEAM ) {
						$subject = wfMessage( 'semanticlabemailing-user-request-discarded-header', $wgSitename, $title_text, $text)->text();
						$body = wfMessage( 'semanticlabemailing-user-request-discarded-team', $text, $title_text, $link, $creator, $creatorlink)->text();
					} elseif ( $status == ASSIGNED ) {
						$subject = wfMessage( 'semanticlabemailing-user-request-assigned-header', $wgSitename, $title_text, $text)->text();
						$body = wfMessage( 'semanticlabemailing-user-request-assigned', $text, $title_text, $link, $creator, $creatorlink)->text();
					} elseif ( $status == WEEK_EXTENSION ) {

						$weeks = "";
						$comment = "";
				
						if ( $extra ) {
							$parts = explode("@", $extra);
							$weeks = $parts[0];
							$comment = $parts[1];  
						}

						$subject = wfMessage( 'semanticlabemailing-user-request-week-header', $wgSitename, $title_text, $username )->text();
						$body = wfMessage( 'semanticlabemailing-user-request-week', $username, $title_text, $link, $creator, $creatorlink, $weeks, $comment )->text();
					} elseif ( $status == NEW_EXPERIMENT ) {
					
						if ( $verbose != 'true') {
						  continue;
						}
						
						$request = $extra;
						$requestobj = Title::newFromText($request);
						$requestlink = $requestobj->escapeFullURL();
					   
						$subject = wfMessage( 'semanticlabemailing-user-experiment-new-header', $wgSitename, $title_text)->text();
						$body = wfMessage( 'semanticlabemailing-user-experiment-new', $username, $title_text, $request, $requestlink)->text();
						$body = $body."\r\n\r\n".$userwarn;

					} elseif ( $status == CLOSED_EXPERIMENT  ) {
						
						if ( $verbose != 'true') {
							continue;
						}
						
						$request = $extra;
						$requestobj = Title::newFromText($request);
						$requestlink = $requestobj->escapeFullURL();
						
						$subject = wfMessage( 'semanticlabemailing-user-experiment-closed-header', $wgSitename, $title_text)->text();
						$body = wfMessage( 'semanticlabemailing-user-experiment-closed', $username, $title_text, $request, $requestlink )->text();
						$body = $body."\r\n\r\n".$userwarn;
					} elseif ( $status == UPDATE_EXPERIMENT  ) {
						
						if ( $verbose != 'true') {
						  continue;
						}
					
						$process_str ="UPDATE:\n\n";
   
						if ($extra) {
	
							$partextra = explode("*", $extra);
							$request = $partextra[0];
							$requestobj = Title::newFromText($request);
							$requestlink = $requestobj->escapeFullURL();

							$process = explode(";", $partextra[1]);
							
							$i = 0;
							
							while ($i < count($process) ) {
								
								$process_str.=$process[$i].": ".$process[$i+1]."\n";
	  
								$i = $i+2;
							}
						
						}
						
						 $subject = wfMessage( 'semanticlabemailing-user-experiment-updated-header', $wgSitename, $title_text)->text();
						 $body = wfMessage( 'semanticlabemailing-user-experiment-updated', $username, $title_text, $process_str, $request, $requestlink)->text();
						 $body = $body."\r\n\r\n".$userwarn;

					} else {
						$subject = wfMessage( 'semanticlabemailing-user-request-updated-header', $wgSitename, $title_text, $username)->text();
						$body = wfMessage( 'semanticlabemailing-user-request-updated', $username, $title_text, $link, $creator, $creatorlink)->text();
					}

					if ( count($to_addresses) > 0 && count($from_addresses) > 0 && $body!="" ) {
						$user_mailer = new UserMailer();
						$user_mailer->send( $to_addresses, $from_addresses[0], $subject, $body );
					}

				}
			}
	}

	/**
	* This function returns to results of a certain query
	* Thank you Yaron Koren for advices concerning this code
	* @param $query_string String : the query
	* @param $properties_to_display array(String): array of property names to display
	* @param $display_title Boolean : add the page title in the result
	* @return TODO
	*/
	static function getQueryResults( $query_string, $properties_to_display, $display_title ) {
	
		// We use the Semantic MediaWiki Processor
		// $smwgIP is defined by Semantic MediaWiki, and we don't allow
		// this file to be sourced unless Semantic MediaWiki is included.
		global $smwgIP;
		
		if ( file_exists( $smwgIP . "/includes/SMW_QueryProcessor.php") ) {
			include_once( $smwgIP . "/includes/SMW_QueryProcessor.php" );
		} else {
			include_once( $smwgIP . "/includes/query/SMW_QueryProcessor.php" );
		}
		$params = array();
		$inline = true;
		$printlabel = "";
		$printouts = array();
		// add the page name to the printouts
		if ( $display_title ) {
			$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_THIS, $printlabel );
			array_push( $printouts, $to_push );
		}
		// Push the properties to display in the printout array.
		foreach ( $properties_to_display as $property ) {
			if ( class_exists( 'SMWPropertyValue' ) ) { // SMW 1.4
				$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, $printlabel, SMWPropertyValue::makeProperty( $property ) );
			} else {
				$to_push = new SMWPrintRequest( SMWPrintRequest::PRINT_PROP, $printlabel, Title::newFromText( $property, SMW_NS_PROPERTY ) );
			}
			array_push( $printouts, $to_push );
		}
		if ( version_compare( SMW_VERSION, '1.6.1', '>' ) ) {
			SMWQueryProcessor::addThisPrintout( $printouts, $params );
			$params = SMWQueryProcessor::getProcessedParams( $params, $printouts );
			$format = null;
		}
		else {
			$format = 'auto';
		}
		$query = SMWQueryProcessor::createQuery( $query_string, $params, $inline, $format, $printouts );
		$results = smwfGetStore()->getQueryResult( $query );
		return $results;
	}

	/**
	 * Prints debugging information. $debugText is what you want to print, $debugVal
	 * is the level at which you want to print the information.
	 *
	 * @param string $debugText
	 * @param string $debugVal
	 * @access private
	 */
	static function printDebug( $debugText, $debugArr = null ) {
		global $wgSemanticLabEmailingDebug;
		$wgSemanticLabEmailingDebug = true;
		if ( $wgSemanticLabEmailingDebug ) {
			if ( isset( $debugArr ) ) {
				$text = $debugText . ' ' . implode( '::', $debugArr );
				wfDebugLog( 'semantic-labemailing', $text, false );
			} else {
				wfDebugLog( 'semantic-labemailing', $debugText, false );
			}
		}
	}
	
}
