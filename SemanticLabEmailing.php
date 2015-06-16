<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo 'Not a valid entry point';
	exit( 1 );
}

if ( !defined( 'SMW_VERSION' ) ) {
	echo 'This extension requires Semantic MediaWiki to be installed.';
	exit( 1 );
}

#
# This is the path to your installation of SemanticLabEmailing as
# seen from the web. Change it if required ($wgScriptPath is the
# path to the base directory of your wiki). No final slash.
# #
$stScriptPath = $wgScriptPath . '/extensions/SemanticLabEmailing';
#

# Extension credits
$wgExtensionCredits[defined( 'SEMANTIC_EXTENSION_TYPE' ) ? 'semantic' : 'other'][] = array(
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

#$wgSemanticLabEmailingDebug = true;
#$wgSemanticLabEmailingDefaultEmail = "no-reply@crg.es";
#$wgSemanticLabEmailingDefaultName = "Proteomics Wiki";
$wgSemanticLabEmailingUsersNoMail = array("WikiSysop");
$wgSemanticLabEmailingPropsCheck = array("PR_Experiment_Status", "PR_Storage_Status", "PR_SH_DigestionType_Status", "PR_Quantitation_Status", "PR_OtherExp_Status", "PR_MW_Status", "PR_MRM_Status", "PR_LC_Status", "PR_Identification_Status", "PR_Exp_PTMenrichment_Status", "PR_Electrophoresis_Status", "PR_Depletion_Status", "PR_DataAnalysis_Status" );
$wgSemanticLabEmailingPropsName =  array("Experiment", "Sample Storage", "Digestion", "Quantitation", "Other experiment", "Molecular Weight Determination", "MRM", "Liquid Chromatography", "Identification", "PTM", "Electrophoresis", "Depletion", "Data Analysis" );

// i18n
$wgExtensionMessagesFiles['SemanticLabEmailing'] = dirname( __FILE__ ) . '/SemanticLabEmailing.i18n.php';

// Autoloading
$wgAutoloadClasses['SemanticLabEmailingMailer'] = dirname( __FILE__ ) . '/SemanticLabEmailing.classes.php';

// Hooks
$wgHooks['ArticleSaveComplete'][] = 'SemanticLabEmailingMailer::mailUpdatedTask';
$wgHooks['ArticleSave'][] = 'SemanticLabEmailingMailer::findOldValues';
