<?php
/**
 * Passion - Responsive UI theme for MediaWiki.
 * 
 * @author Guo Yunhe
 * @version 0.1 2014-01-27
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

// inherit main code from SkinTemplate, set the CSS and template filter
class SkinPassion extends SkinTemplate {
	var $useHeadElement = true;
 
	function initPage( OutputPage $out ) {
		parent::initPage( $out );
		$this->skinname  = 'passion';
		$this->stylename = 'passion';
		$this->template  = 'PassionTemplate';
	}
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		// Append to the default screen common & print styles...
		$out->addStyle( 'passion/screen.css', 'screen' );
		$out->addStyle( 'passion/screen-lg.css', 'screen and (min-width: 1600px)' );
		$out->addStyle( 'passion/screen-sm.css', 'screen and (max-width: 800px)' );
	}
}

class PassionTemplate extends BaseTemplate {

	/* Functions */

	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		// Build additional attributes for navigation urls
		$nav = $this->data['content_navigation'];

		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}

				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
				' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
					' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
					Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
					Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $this->data['rtl'] ) {
			$this->data['view_urls'] =
			array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
			array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
			array_reverse( $this->data['personal_urls'] );
		}
		// Output HTML Page
		$this->html( 'headelement' );
		?>
		
		<!-- global-bar -->
		<div id="global-bar">
			<div class="container">
				<!-- logo -->
				<div id="p-logo" role="banner">
					<a href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>"
						<?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) ?>></a>
				</div>	
				<!-- /logo -->
				
				<!-- menu -->
				<?php $this->renderPortals( $this->data['sidebar'] ); ?>
				<!-- /menu -->

				<!-- search -->
				<div id="p-search" role="search">
					<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">					
						<?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ); ?>
					</form>
				</div>
				<!-- /search -->
				
				<!-- personal -->
				<div id="p-personal" role="navigation" class="portal<?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
					<?php if ( $this->data['loggedin'] )
						echo $this->data['username'];
					else
						echo '登录';
					?>
					<ul<?php $this->html( 'userlangattributes' ) ?>>
					<?php
						$personalTools = $this->getPersonalTools();
						foreach ( $personalTools as $key => $item ) {
							echo $this->makeListItem( $key, $item );
						}
					?>
					</ul>
				</div><!-- /p-personal -->
			</div><!--/container-->
		</div><!-- /global-bar -->
		
		<!-- navigation -->
		<div id="navigation">
			<div class="container">
				<!-- left navigation -->
				<div id="left-navigation">
					<ul<?php $this->html( 'userlangattributes' ) ?>>
					<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
						<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
					<?php endforeach; ?>
					</ul>
				</div>
				<!-- /left navigation -->
				<!-- right navigation -->
				<div id="right-navigation">
					<div id="p-views" role="navigation" class="<?php if ( count( $this->data['view_urls'] ) == 0 ) { echo 'emptyPortlet'; } ?>">
						<ul<?php $this->html('userlangattributes') ?>>
							<?php foreach ( $this->data['view_urls'] as $link ): ?>
								<li<?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
									// $link['text'] can be undefined - bug 27764
									if ( array_key_exists( 'text', $link ) ) {
										echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
									}
									?></a></span></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<div id="p-cactions" role="navigation" class="<?php if ( count( $this->data['action_urls'] ) == 0 ) echo 'emptyPortlet'; ?>">
						<div class="menu">
							<ul<?php $this->html( 'userlangattributes' ) ?>>
								<?php foreach ( $this->data['action_urls'] as $link ): ?>
									<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>
				<!-- /right navigation -->
			</div>
		</div>
		<!-- navigation -->
		
		<!-- content -->
		<div id="content" class="mw-body" role="main">
			<a id="top"></a>
			<div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
			<?php if ( $this->data['sitenotice'] ): ?>
			<!-- sitenotice -->
			<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
			<!-- /sitenotice -->
			<?php endif; ?>
			<!-- firstHeading -->
			<h1 id="firstHeading" class="firstHeading" lang="<?php
				$this->data['pageLanguage'] = $this->getSkin()->getTitle()->getPageViewLanguage()->getCode();
				$this->html( 'pageLanguage' );
			?>"><span dir="auto"><?php $this->html( 'title' ) ?></span></h1>
			<!-- /firstHeading -->
			<!-- bodyContent -->
			<div id="bodyContent">
				<!-- subtitle -->
				<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
				<!-- /subtitle -->
				<?php if ( $this->data['undelete'] ): ?>
				<!-- undelete -->
				<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
				<!-- /undelete -->
				<?php endif; ?>
				<?php if( $this->data['newtalk'] ): ?>
				<!-- newtalk -->
				<div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
				<!-- /newtalk -->
				<?php endif; ?>
				<?php if ( $this->data['showjumplinks'] ): ?>
				<!-- jumpto -->
				<div id="jump-to-nav" class="mw-jump">
					<?php $this->msg( 'jumpto' ) ?>
					<a href="#mw-navigation"><?php $this->msg( 'jumptonavigation' ) ?></a><?php $this->msg( 'comma-separator' ) ?>
					<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
				</div>
				<!-- /jumpto -->
				<?php endif; ?>
				<!-- bodycontent -->
				<?php $this->html( 'bodycontent' ) ?>
				<!-- /bodycontent -->
				<?php if ( $this->data['printfooter'] ): ?>
				<!-- printfooter -->
				<div class="printfooter">
				<?php $this->html( 'printfooter' ); ?>
				</div>
				<!-- /printfooter -->
				<?php endif; ?>
				<?php if ( $this->data['catlinks'] ): ?>
				<!-- catlinks -->
				<?php $this->html( 'catlinks' ); ?>
				<!-- /catlinks -->
				<?php endif; ?>
				<?php if ( $this->data['dataAfterContent'] ): ?>
				<!-- dataAfterContent -->
				<?php $this->html( 'dataAfterContent' ); ?>
				<!-- /dataAfterContent -->
				<?php endif; ?>
				<div class="visualClear"></div>
				<!-- debughtml -->
				<?php $this->html( 'debughtml' ); ?>
				<!-- /debughtml -->
			</div>
			<!-- /bodyContent -->
		</div>
		<!-- /content -->
		
		
		
		<!-- footer -->
		<div id="footer" role="contentinfo"<?php $this->html( 'userlangattributes' ) ?>>
			<?php $footericons = $this->getFooterIcons("icononly");
			if ( count( $footericons ) > 0 ): ?>
				<ul id="footer-icons" class="noprint">
<?php			foreach ( $footericons as $blockName => $footerIcons ): ?>
					<li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
<?php				foreach ( $footerIcons as $icon ): ?>
						<?php echo $this->getSkin()->makeFooterIcon( $icon ); ?>

<?php				endforeach; ?>
					</li>
<?php			endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php foreach( $this->getFooterLinks() as $category => $links ): ?>
				<ul id="footer-<?php echo $category ?>">
					<?php foreach( $links as $link ): ?>
						<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endforeach; ?>
			
			<div style="clear:both"></div>
		</div>
		<!-- /footer -->
		<?php $this->printTrail(); ?>

	</body>
</html>
<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	protected function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( !isset( $portals['SEARCH'] ) ) {
			$portals['SEARCH'] = true;
		}
		if ( !isset( $portals['TOOLBOX'] ) ) {
			$portals['TOOLBOX'] = true;
		}
		if ( !isset( $portals['LANGUAGES'] ) ) {
			$portals['LANGUAGES'] = true;
		}
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false )
				continue;

			echo "\n<!-- {$name} -->\n";
			switch( $name ) {
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] ) {
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}

	/**
	 * @param $name string
	 * @param $content array
	 * @param $msg null|string
	 * @param $hook null|string|array
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ) {
			$msg = $name;
		}
		?>
<div class="portal" role="navigation" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
	<?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?>
<?php
		if ( is_array( $content ) ): ?>
		<ul>
<?php
			foreach( $content as $key => $val ): ?>
			<?php echo $this->makeListItem( $key, $val ); ?>

<?php
			endforeach;
			if ( $hook !== null ) {
				wfRunHooks( $hook, array( &$this, true ) );
			}
			?>
		</ul>
<?php
		endif; ?>
</div>
<?php
	}

}
