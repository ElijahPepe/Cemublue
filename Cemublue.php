<?php
/**
 * Cemublue -- the new look of wiki.cemu.info
 * MediaWiki implementation based on MonoBook nouveau.
 *
 * Copyright (C) 2021 Elijah Conners <business@elijahpepe.com>
 */

$wgExtensionCredits['skin'][] = array(
	'path' => __FILE__,
	'name' => 'Cemublue',
	'namemsg' => 'skinname-cemublue',
	'descriptionmsg' => 'cemublue-desc',
	'url' => 'https://wiki.cemu.info/',
	'author' => array('Elijah Conners'),
	'license-name' => 'GPLv2',
);

// Register files
$wgAutoloadClasses['SkinCemublue'] = __DIR__ . '/SkinCemublue.php';
$wgAutoloadClasses['CemublueTemplate'] = __DIR__ . '/CemublueTemplate.php';
$wgMessagesDirs['Cemublue'] = __DIR__ . '/i18n';

// Register skin
$wgValidSkinNames['cemublue'] = 'Cemublue';

// Register modules
$wgResourceModules['skins.cemublue.styles'] = array(
	'position' => 'top',
	'styles' => array(
		'main.css' => array('media' => 'screen'),
	),
	'remoteSkinPath' => 'Cemublue',
	'localBasePath' => __DIR__,
);

$wgHooks['OutputPageBeforeHTML'][] = 'injectMetaTags';

function injectMetaTags($out) {
	$out->addMeta('viewport', 'width=device-width, initial-scale=1.0');
	$out->addMeta('theme-color', '#3498db');
	return true;
}
