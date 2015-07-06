<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

if ( !defined( 'SMW_VERSION' ) ) {
	echo 'This extension requires Semantic MediaWiki to be installed.';
	exit( 1 );
}


# Extension credits
$GLOBALS['wgExtensionCredits'][defined( 'SEMANTIC_EXTENSION_TYPE' ) ? 'semantic' : 'other'][] = array(
	'path' => __FILE__,
	'name' => 'SemanticLabEmailing',
	'author' => array(
		'Toniher',
		'Steren Giannini',
		'Ryan Lane',
		'[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]'
	),
	'version' => '1.5',
	'url' => 'http://www.mediawiki.',
	'descriptionmsg' => 'semanticlabemailing-desc',
);

#$GLOBALS['wgSemanticLabEmailingDebug'] = true;
#$GLOBALS['wgSemanticLabEmailingDefaultEmail'] = "no-reply@crg.es";
#$GLOBALS['wgSemanticLabEmailingDefaultName'] = "Proteomics Wiki";
$GLOBALS['wgSemanticLabEmailingUsersNoMail'] = array("WikiSysop");
$GLOBALS['wgSemanticLabEmailingPropsCheck'] = array(
	"Request" =>
		array ( "PR_Request_Status" ),
	"Experiment" =>
		array( "PR_Experiment_Status", "PR_Storage_Status", "PR_SH_DigestionType_Status", "PR_Quantitation_Status", "PR_OtherExp_Status", "PR_MW_Status", "PR_MRM_Status", "PR_LC_Status", "PR_Identification_Status", "PR_Exp_PTMenrichment_Status", "PR_Electrophoresis_Status", "PR_Depletion_Status", "PR_DataAnalysis_Status" ) );

$GLOBALS['wgSemanticLabEmailingPropsName'] =  array(
	"Request" => array( "Status" ),
	"Experiment" => array("Experiment", "Sample Storage", "Digestion", "Quantitation", "Other experiment", "Molecular Weight Determination", "MRM", "Liquid Chromatography", "Identification", "PTM", "Electrophoresis", "Depletion", "Data Analysis" ) );

$GLOBALS['wgSemanticLabEmailingAssignedProp'] =  array(
	"Request" => "PR_Request_AssignedTo"
);
$GLOBALS['wgSemanticLabEmailingOwnerProp'] =  array(
	"Request" => "PR_Request_UserName"
);

$GLOBALS['wgSemanticLabEmailingReferenceProp'] =  array(
	"Experiment" => "Request_Reference"
);

$GLOBALS['wgSemanticLabEmailingVerboseProp'] = "Has_Verbose_Mailing";

// i18n
$GLOBALS['wgMessagesDirs']['SemanticLabEmailing'] = __DIR__ . '/i18n';
$GLOBALS['wgExtensionMessagesFiles']['SemanticLabEmailing'] = dirname( __FILE__ ) . '/SemanticLabEmailing.i18n.php';

// Autoloading
$GLOBALS['wgAutoloadClasses']['SemanticLabEmailingMailer'] = dirname( __FILE__ ) . '/SemanticLabEmailing.classes.php';

// Hooks
$GLOBALS['wgHooks']['ArticleSaveComplete'][] = 'SemanticLabEmailingMailer::mailUpdatedTask';
$GLOBALS['wgHooks']['ArticleSave'][] = 'SemanticLabEmailingMailer::findOldValues';
