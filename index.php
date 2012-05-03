<?php
/**
 * wmfBugZillaPortal: Portal for Wikimedia's BugZilla
 * Created on June 2, 2012
 *
 * @author Timo Tijhof <krinklemail@gmail.com>, 2012
 * @license CC-BY-SA 3.0 Unported: creativecommons.org/licenses/by/3.0/
 */

/**
 * Configuration
 * -------------------------------------------------
 */
require_once('/home/krinkle/common/InitTool.php'); // BaseTool

$toolConfig = array(
	'displayTitle'	=> 'wmfBugZillaPortal',
	'simplePath'	=> '/wmfBugZillaPortal/',
	'revisionId'	=> '0.0.1',
	'revisionDate'	=> '2012-05-02',
	'styles' => array(
		'main.css',
	),
);

$Tool = BaseTool::newFromArray($toolConfig);

$Tool->doHtmlHead();
$Tool->doStartBodyWrapper();



/**
 * Settings
 * -------------------------------------------------
 */
$bugZillaStuff = array(
	'mediawiki' => array(
		'versions' => array(
			'1.18.0',
			'1.18.1',
			'1.18.2',
			'1.18.3',
			'1.19',
			'1.19.0',
			'1.19beta1',
			'1.19beta2',
			'1.19.0rc1',
			'1.20-git',
			'unspecified',
		),
		'milestones' => array(
			'1.18.0 release',
			'1.18.x release',
			'1.19.0 release',
			'1.20.0 release',
		),
	),
	'wikimedia' => array(
		'deployment' => array(
			// Map Wikimedia deployment milestones to the tracker bug for MediaWiki bugs
			'1.18wmf1' => '29068',
			'1.19wmf1' => '31217',
			'1.20wmf1' => '36464',
			'1.20wmf2' => '36465',
			'1.20wmf3' => null,
		),
	),
);

function wbpBuglistLinks( $buglistQuery, $label ) {
	return  '('
	. Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/buglist.cgi?' . http_build_query(array(
				'resolution' => '---',
			) + $buglistQuery),
			'target' => '_blank',
			'title' => $label
		), 'unresolved'
	)
	. ' &bull; '
	. Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/buglist.cgi?' . http_build_query($buglistQuery),
			'target' => '_blank',
			'title' => $label
		), 'all'
	)
	. ')';
}

function wbpTrackingBugLinks( $bugID ) {
	return  Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/show_bug.cgi?' . http_build_query(array(
				'id' => $bugID
			)),
			'target' => '_blank',
			'title' => "bug $bugID"
		), "bug $bugID"
	)
	. ' ('
	. Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/showdependencytree.cgi?' . http_build_query(array(
				'id' => $bugID,
				'hide_resolved' => 1,
			)),
			'target' => '_blank',
			'title' => "dependency tree for bug $bugID"
		), 'dependency tree'
	)
	. ')';
}

$Tool->addOut( 'MediaWiki core', 'h2' );
$html = '<table class="wikitable krinkle-wmfBugZillaPortal-overview">'
	. '<thead><tr><th>Version</th><th>Target Milestone</th></tr></thead>'
	. '<tbody><tr>';

// Versions
$html .= '<td><p>Unresolved bugs new in a MediaWiki version</p><ul>';
foreach ( $bugZillaStuff['mediawiki']['versions'] as $mwVersion ) {
	$html .= '<li>' . Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/buglist.cgi?' . http_build_query(array(
				'query_format' => 'advanced',
				'product' => 'MediaWiki',
				'resolution' => '---',
				'version' => $mwVersion,
			)),
			'target' => '_blank',
			'title' => 'Unresolved bugs new in MediaWiki ' . $mwVersion
		), $mwVersion
	) . '</li>';
}
$html .= '</ul></td>';

// Milestones
$html .= '<td><p>Unresolved tickets targetted for a MediaWiki milestone</p><ul>';
foreach ( $bugZillaStuff['mediawiki']['milestones'] as $mwMilestone ) {
	$html .= '<li>' . Html::element( 'a', array(
			'href' => 'https://bugzilla.wikimedia.org/buglist.cgi?' . http_build_query(array(
				'query_format' => 'advanced',
				'product' => 'MediaWiki',
				'resolution' => '---',
				'target_milestone' => $mwMilestone,
			)),
			'target' => '_blank',
			'title' => 'Unresolved tickets targetted for MediaWiki ' . $mwMilestone
		), $mwMilestone
	) . '</li>';
}
$html .= '</ul></td>';

$html .= '</tr></tbody></table>';

$Tool->addOut( $html );

$Tool->addOut( 'Wikimedia', 'h2' );
$html = '<table class="wikitable krinkle-wmfBugZillaPortal-overview krinkle-wmfBugZillaPortal-overview-wm">'
	. '<thead><tr><th>Deployment milestone</th><th>MediaWiki core/extensions (tracking)</th></tr></thead>'
	. '<tbody>';

// Deployment
$html .= '<tr>'
	. '<td>General tasks in "Wikimedia" category for a deployment milestone</td>'
	. '<td><p>Tickets in MediaWiki core/extensions<br> blocking this deployment</td>'
	. '</tr>';

foreach ( $bugZillaStuff['wikimedia']['deployment'] as $wmDeploy => $mwTrackingBug ) {
	$html .= '<tr>'
	. '<td>'
	. $wmDeploy
	. ' '
	. wbpBuglistLinks(
		array(
			'query_format' => 'advanced',
			'product' => 'Wikimedia',
			'target_milestone' => "$wmDeploy deployment",
		),
		'General tasks for ' . $wmDeploy
	)
	. '</td>'
	. '<td>';

	if ( $mwTrackingBug ) {
		$html .= wbpTrackingBugLinks( $mwTrackingBug );
	} else {
		$html .= '<em>(no tracking bug yet)</em>';
	}
	$html .= '</td></tr>';
}

$html .= '</tbody></table>';

$Tool->addOut( $html );


/**
 * Close up
 * -------------------------------------------------
 */
$Tool->flushMainOutput();

