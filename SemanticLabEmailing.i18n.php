<?php
/**
 * Internationalization messages file for SemanticLabEmailing extension
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English (English)
 * @author Toniher, Steren Giannini
 */
$messages['en'] = array(
	'semanticlabemailing-desc' => 'E-mail notifications for assigned or updated tasks',
	'semanticlabemailing-newtask' => 'New task:',
	'semanticlabemailing-taskassigned' => 'Task assigned:',
	'semanticlabemailing-taskupdated' => 'Task updated:',
	'semanticlabemailing-taskclosed' => 'Task closed:',
	'semanticlabemailing-newtask-msg' => 'The task "$1" has just been created.',
	'semanticlabemailing-taskclosed-msg' => 'The task "$1" has just been closed.',
	'semanticlabemailing-assignedtoyou-msg2' => 'The task "$1" has just been assigned to you.',
	'semanticlabemailing-updatedtoyou-msg2' => 'The task "$1" has just been updated.',
	'semanticlabemailing-reminder' => 'Reminder:',
	'semanticlabemailing-reminder-message2' => "Just to remind you that the task \"$1\" ends in $2 {{PLURAL:$2|day|days}}.

$3",
	'semanticlabemailing-text-message' => 'Here is the task description:',
	'semanticlabemailing-diff-message' => 'Here are the differences:',
	'semanticlabemailing-user-request-new-header' => '[$1] New Request: $2 ([$3])',
	'semanticlabemailing-user-request-new' => '$1 ($2) has submitted a new request $3',
	'semanticlabemailing-user-request-accepted-header' => '[$1] Accepted Request: $2 ([$3])',
	'semanticlabemailing-user-request-accepted' => 'Dear $1,

Your request $2 has been accepted and briefly you will receive a message with the codes of your samples. Please, don\'t submit your samples to the Proteomics Unit before receiving the codes, as samples need to be properly labeled before being accepted for further processing.

The expected delivery time for you results is $6 weeks, starting from sample delivery.

Find detailed information about your request in the following link:
$3

The Proteomics Team',
	'semanticlabemailing-user-request-accepted-team' => 'Request $2 ($3) (submitted by $1) has been accepted by $4 ($5)
',
	'semanticlabemailing-user-request-closed-header' => '[$1] Closed Request: $2 ([$3])',
	'semanticlabemailing-user-request-closed' => 'Dear $1,

Your request $2 has been closed.  Find detailed information about your request, including your results and raw files, in the following link: $3

Submitted Date: $6
Closing Date: $7

The Proteomics Team
',
	'semanticlabemailing-user-request-closed-team' => 'Request $2 ($3) (submitted by $1) has been closed by $4 ($5)
',
	'semanticlabemailing-user-request-discarded-header' => '[$1] Discarded Request: $2 ([$3])',
	'semanticlabemailing-user-request-discarded' => 'Dear $1,

Your request $2 ($3) has been discarded by $4 ($5).  Please, contact the team about the reasons.

The Proteomics Team
',
	'semanticlabemailing-user-request-discarded-team' => 'Request $2 ($3) (submitted by $1) has been discarded by $4 ($5)
',
	'semanticlabemailing-user-request-updated-header' => '[$1] Updated Request: $2 ([$3])',
	'semanticlabemailing-user-request-updated' => 'Dear $1,
Your request $2 ($3) has been updated by $4 ($5)

The Proteomics Team
',
	'semanticlabemailing-user-request-assigned-header' => '[$1] Assigned Request: $2 ([$3])',
	'semanticlabemailing-user-request-assigned' => 'Request $2 ($3) (submitted by $1) has been assigned to you by $4 ($5)
',
	'semanticlabemailing-user-experiment-new-header' => '[$1] New Experiment: $2',
	'semanticlabemailing-user-experiment-new' => 'Dear $1,

A new experiment $2 has been created.

$3 ($4)

The Proteomics Team
',

	'semanticlabemailing-user-request-week-header' => '[$1] Extended Request: $2 ([$3]) ',
	'semanticlabemailing-user-request-week' => 'Dear $1,

The expected delivery time of your request $2 has has been extended for $6 weeks due the following issue: $7

Find detailed information about your request in the following link:
$3',

	'semanticlabemailing-user-experiment-closed-header' => '[$1] Closed Experiment: $2',
	'semanticlabemailing-user-experiment-closed' => 'Dear $1,

Experiment $2 has been closed.

$3 ($4)

The Proteomics Team
',

	'semanticlabemailing-user-experiment-updated-header' => '[$1] Updated Experiment: $2',
	'semanticlabemailing-user-experiment-updated' => 'Dear $1,
Your experiment $2 has been updated.

$3

$4 ($5)

The Proteomics Team
',
	
	'semanticlabemailing-user-verbose-check' => 'Note: If you would like to change the frequency of these email communications and reduce it to a single communication at the start and at the end of a request, please follow this link: $1'

);

