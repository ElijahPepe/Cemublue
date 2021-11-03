<?php
/**
 * Cemublue -- the new look of wiki.cemu.info
 * MediaWiki implementation based on MonoBook nouveau.
 *
 * Copyright (C) 2021 Elijah Conners <business@elijahpepe.com>
 */
class CemublueTemplate extends BaseTemplate {
	public function execute() {
		Wikimedia\AtEase\AtEase::suppressWarnings();

		$this->html( 'headelement' );

		$this->header();
		?>

		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div id="content" class="mw-body" role="main">
						<a id="top"></a>
						<?php if ( $this->data['sitenotice'] ) {
							echo '<div id="siteNotice">'; $this->html( 'sitenotice' ); echo '</div>';
							}	?>

						<h1 id="firstHeading" class="first-header" lang="<?php $this->data['pageLanguage'] = $this->getSkin()->getTitle()->getPageViewLanguage()->getHtmlCode(); $this->text( 'pageLanguage' );	?>">
							<span dir="auto"><?php $this->html( 'title' ); ?></span>
						</h1>

						<div id="bodyContent" class="mw-body-content">
							<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
							<div id="contentSub"<?php	$this->html( 'userlangattributes' ); ?>>
								<?php $this->html( 'subtitle' )	?>
							</div>
							<?php	if ( $this->data['undelete'] ) {
									echo '<div id="contentSub2">'; $this->html( 'undelete' ); echo '</div>';
								}
								if ( $this->data['newtalk'] ) {
									echo '<div class="usermessage">'; $this->html( 'newtalk' ); echo '</div>';
								}	?>
					                <div id="jump-to-nav"></div>
					                <a class="mw-jump-link" href="#mw-head"><?php $this->msg('jumpto'); $this->msg( 'jumptonavigation' ); ?></a>
					                <a class="mw-jump-link" href="#searchInput"><?php $this->msg('jumpto'); $this->msg( 'jumptosearch' ); ?></a>

							<?php
							//<!-- start content -->
								$this->html( 'bodytext' );
								if ( $this->data['catlinks'] ) {
									$this->html( 'catlinks' );
								}
							//<!-- end content -->

								if ( $this->data['dataAfterContent'] ) {
									$this->html( 'dataAfterContent'	);
								}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		$this->footer();
		$this->printTrail();
		echo Html::closeElement( 'body' );
		echo Html::closeElement( 'html' );
		Wikimedia\AtEase\AtEase::suppressWarnings(true);
	} // end of execute() method

	/*************************************************************************************************/

	private function header() {
	?>
	<div class="mw-jump sr-only">
		<?php	$this->msg( 'jumpto' ); ?>
		<a href="#top">content</a>
	</div>
	<header>
		<?php $this->cactions(); ?>
	</header>
	<?php
	}

	private function footer() {
		$validFooterIcons = $this->getFooterIcons( "icononly" );
		$validFooterLinks = $this->getFooterLinks( "flat" ); // Additional footer links
		$currentYear = date('Y');
	?>
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-md-offset-2 col-md-7">
					<?php if ( count( $validFooterLinks ) > 0 ): ?>
						<div class="spacer"></div>
						<ul id="f-list">
							<?php	foreach ( $validFooterLinks as $aLink ) {
									if ($aLink === 'copyright') continue;
									echo "<li id=\"{$aLink}>\">"; $this->html( $aLink ); echo '</li>';
							} ?>
							<li id="randompage>"><a href="/wiki/Special:Random" title="Load a random page [Alt+Shift+x]" accesskey="x">Random page</a></li>
							<li id="recentchanges>"><a href="/wiki/Special:RecentChanges" title="A list of recent changes in the wiki [Alt+Shift+r]" accesskey="r">Recent changes</a></li>
						</ul>
					<?php endif; ?>
				</div>
				<div class="col-xs-12 col-md-3">
					<!-- No questions or comments, the Wiki has enough information on how to contact us. -->
				</div>
			</div>
		</div>
	</footer>
	<?php
		echo $footerEnd;
	}

	/**
	 * @param array $sidebar
	 */
	protected function renderPortals( array $sidebar ) {
		// These are already rendered elsewhere
		$sidebar['SEARCH'] = false;
		$sidebar['TOOLBOX'] = false;
		$sidebar['LANGUAGES'] = false;

		array_walk($sidebar, [$this, 'customBox'] );
	}

	private function searchBox() {
		?>
		<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform" class="navbar-form navbar-right" role="search">
			<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>

				<div class="input-group">
					<?php echo $this->makeSearchInput( [ "id" => "searchInput", "class" => "form-control", "placeholder" => "Search a game or page..." ] ); ?>
					<div class="input-group-btn"><?php
						echo $this->makeSearchButton(
						"fulltext",
						[ "id" => "mw-searchButton", "class" => "searchButton btn btn-default" ]
					);
				?></div>
				</div>
		</form>
	<?php
	}

	private function cactions() {
		$context_actions = [];
		$primary_actions = [];
		$secondary_actions = [];

		$assign_active = function(array &$actionTab) {
			if ( strpos( $actionTab['class'], 'selected' ) !== false ) {
				$actionTab['class'] .= ' active';
			}
		};

		foreach ( $this->data['content_actions'] as $key => $tab ) {
			// TODO: handling form_edit separately here is a hack, no idea how it works in Vector.
			if ( isset($tab['primary']) && $tab['primary'] == '1' || $key == 'form_edit' || $key == 'formedit' ) {
				if ( isset($tab['context']) ) {
					$context_actions[$key] = $tab;
					$assign_active($context_actions[$key]);
				} else {
					$primary_actions[$key] = $tab;
					$assign_active($primary_actions[$key]);
				}
			} else {
				$secondary_actions[$key] = $tab;
				$assign_active($secondary_actions[$key]);
			}
		}
		?>

		<nav class="navbar navbar-default navbar-stick" id="wiki-actions" role="navigation">
			
			<ul class="nav navbar-nav navbar-right hidden-xs">
				<?php
					$this->toolbox();
					$this->personaltools();
				?>
			</ul>
			<div class="container"><div class="row">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#gw-toolbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div class="collapse navbar-collapse" id="gw-toolbar">
					<ul class="nav navbar-nav">
					<li id="ca-nstab-mainpage"><a href="/wiki/Main_Page" title="Visit the main page [Alt+Shift+z]" accesskey="z">Cemu Wiki</a></li>
					<li id="ca-nstab-help"><a href="/wiki/CEMU_Wiki:Contributing" title="The place to find out">Contributing</a></li>
					<?php
						foreach ( $context_actions as $key => $tab ) {
							echo $this->makeListItem( $key, $tab );
						}
					?>
					</ul>
					<?php
						$this->searchBox();
					?>
					<ul class="nav navbar-nav">
					<?php
						foreach ( $primary_actions as $key => $tab ) {
							echo $this->makeListItem( $key, $tab );
						}
					?><li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">More <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
					<?php
						foreach ( $secondary_actions as $key => $tab ) {
							echo $this->makeListItem( $key, $tab );
						}
					?>
							</ul>
						</li>
					</ul>
				</div>
			</div></div>
		</nav>
	<?php
	}

	/*************************************************************************************************/
	private function toolbox() {
		?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i> <?php $this->msg( 'toolbox' ) ?> <span class="caret"></span></a>
			<ul class="dropdown-menu" role="menu">
				<?php
					foreach ( $this->getToolbox() as $key => $tbitem) {
						echo $this->makeListItem( $key, $tbitem );
					}

					Hooks::Run( 'MonoBookTemplateToolboxEnd', [&$this] );
					Hooks::Run( 'SkinTemplateToolboxEnd', [ &$this, true ] );
				?>
			</ul>
		</li>
	<?php
	}

	/*************************************************************************************************/
	private function personaltools() {
		$personal_tools = $this->getPersonalTools();

		?>
		<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
				<span class="fa fa-user" aria-label="<?php $this->msg( 'personaltools' ) ?>"></span>
				<?php
					if (isset($personal_tools['userpage'])) {
						echo $personal_tools['userpage']['links'][0]['text'];
					} else {
						$this->msg( 'listfiles_user' );
					} ?>
				<span class="caret"></span>
			</a>
			<ul class="dropdown-menu" role="menu">
				<?php
					foreach ( $personal_tools as $key => $item ) {
						if ($key === 'notifications-alert') {
							$notifications_alert_tool = $item;
						} else if ($key === 'notifications-message') {
							$notifications_message_tool = $item;
						} else if ($key === 'notifications-notice') {
							$notifications_notice_tool = $item;
						} else {
							echo $this->makeListItem( $key, $item );
						}
					}
				?>
			</ul>
		</li>
		<?php

		if (isset($notifications_message_tool)) {
			echo $this->makeListItem('notifications-message', $notifications_message_tool);
		}

		if (isset($notifications_notice_tool)) {
			echo $this->makeListItem('notifications-notice', $notifications_notice_tool);
		}

		if (isset($notifications_alert_tool)) {
			echo $this->makeListItem('notifications-alert', $notifications_alert_tool);
		}
	}

	/*************************************************************************************************/
	private function languageBox() {
		if ( $this->data['language_urls'] !== false ):
			?>
			<div id="p-lang" class="portlet" role="navigation">
				<h3<?php $this->html( 'userlangattributes' ) ?>><?php $this->msg( 'otherlanguages' ) ?></h3>

				<div class="pBody">
					<ul>
						<?php foreach ( $this->data['language_urls'] as $key => $langlink ) {
								echo $this->makeListItem( $key, $langlink );
							}
						?>
					</ul>

					<?php $this->renderAfterPortlet( 'lang' ); ?>
				</div>
			</div>
		<?php
		endif;
	}

	/*************************************************************************************************/
	/**
	 * @param string $bar
	 * @param array|string $cont
	 */
	private function customBox( $cont, $bar ) {
		if ($cont === false) {
			return;
		}
		$msgObj = wfMessage( $bar );

		if ( $bar !== 'navigation' ) {
			$barQuoted = htmlspecialchars( $bar );
			$messageQuoted = htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $bar );
			echo <<<EOT
			<li class="dropdown">
				<a href="/wiki/Cemu_Wiki:Menu-{$barQuoted}" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{$messageQuoted} <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
EOT;
		}

		if ( is_array ( $cont ) ) {
			foreach ( $cont as $key => $val ) {
				if ( $val['text'] === '---' ) {
					echo '<li role="presentation" class="divider"></li>';
				} elseif ( substr( $val['text'], 0, 7 ) === 'header:' ) {
					echo '<li role="presentation" class="dropdown-header">' . htmlspecialchars( substr( $val['text'], 7 ) ) . '</li>';
				} else {
					echo $this->makeListItem( $key, $val );
				}
			}
		} else {
			echo "<!-- This would have been a box, but it contains custom HTML which is not supported. -->";
		}

		if ( $bar !== 'navigation' ) {
			echo <<<EOT
				</ul>
			</li>
EOT;
		}
	}
}
