<?php
/**
 * Cemublue -- the new look of wiki.cemu.info
 * MediaWiki implementation based on MonoBook nouveau.
 *
 * Copyright (C) 2021 Elijah Conners <business@elijahpepe.com>
 */

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @ingroup Skins
 */
class SkinCemublue extends SkinTemplate {
	public $skinname  = 'cemublue';
	public $stylename = 'Cemublue';
	public $template  = 'CemublueTemplate';

	private $output;

	const CSS_CDN_URL = 'https://cemu.info/css/';
	const FA_CDN_URL = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/';
	const JS_CDN_URL = 'https://cemu.info/js/';

	public function initCSS(OutputPage $out) {
		$this->output = $out;

		$out->addStyle(SkinCemublue::CSS_CDN_URL . 'bootstrap.min.css');
		$out->addStyle(SkinCemublue::FA_CDN_URL . 'font-awesome.min.css');

		$out->addModuleStyles([
			'mediawiki.skinning.interface',
			'mediawiki.skinning.content.externallinks',
			'skins.cemublue.styles',
			'skins.cemublue.icons'
		]);
	}
	public static function injectMetaTags($out) {
		$out->addMeta('viewport', 'width=device-width, initial-scale=1.0');
		$out->addMeta('theme-color', '#3498db');
		return true;
	}

	public function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$this->initCSS( $out );
		$cdnURL = self::JS_CDN_URL . 'bootstrap.min.js';
		$script = <<<EOS
function defer(method) {
    if (window.jQuery) {
        method();
    } else {
        setTimeout(function() { defer(method) }, 50);
    }
}
defer(function() { mw.loader.load( '$cdnURL'); });
EOS;
		$out->addInlineScript($script);
	}
}
