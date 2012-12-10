<?php
/**
 * @copyright	Copyright (C) 2008 - 2011 ZooTemplate.com. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>">
<head>
	<jdoc:include type="head" />
	<?php JHTML::_('behavior.mootools'); ?>
	<?php JHTML::_('behavior.caption', true); ?>
	<?php
		$document = JFactory::getDocument();
		$document->addStyleSheet($ztTools->templateurl() . 'css/system.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/general.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/default.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/template.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/modules.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/css3.css');
		$document->addStyleSheet($ztTools->templateurl() . 'css/typo.css');
		if($ztrtl == 'rtl') {
			$document->addStyleSheet($ztTools->templateurl() . 'css/template_rtl.css');
			$document->addStyleSheet($ztTools->templateurl() . 'css/typo_rtl.css');
		}
		if($this->params->get('zt_change_color')) {
			$document->addScript($ztTools->templateurl() . 'js/zt.script.js');
		}
		if($ztTools->getParam('zt_fontfeature')) {
			$document->addStyleSheet($ztTools->templateurl() . 'css/fonts.css');
		}
	?>
    <?php if($ztTools->getParam('zt_fontfeature')) : ?>
	<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css" />
	<link href='http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT&amp;subset=latin' rel='stylesheet' type='text/css' />
	<link href='http://fonts.googleapis.com/css?family=Vollkorn&amp;subset=latin' rel='stylesheet' type='text/css' />
	<?php endif; ?>
	<?php if($this->params->get('zt_change_color')) { ?>
   <script type="text/javascript">	
		var baseurl = "<?php echo $ztTools->baseurl() ; ?>";
		var ztpathcolor = '<?php echo $ztTools->templateurl(); ?>css/colors/';
		var tmplurl = '<?php echo $ztTools->templateurl();?>';
		var CurrentFontSize = parseInt('<?php echo $ztTools->getParam('zt_font');?>');
	</script>
	<link href="<?php echo $ztTools->parse_ztcolor_cookie($ztcolorstyle); ?>" rel="stylesheet" type="text/css" />
	<?php } ?>
	<!--[if lte IE 7]>
	<link rel="stylesheet" href="<?php echo $ztTools->templateurl(); ?>css/ie7.css" type="text/css" />
	<![endif]-->
	<!--[if IE 8]>
	<link rel="stylesheet" href="<?php echo $ztTools->templateurl(); ?>css/ie8.css" type="text/css" />
	<![endif]-->
	<!--[if IE 9]>
	<link rel="stylesheet" href="<?php echo $ztTools->templateurl(); ?>css/ie9.css" type="text/css" />
	<![endif]-->
</head>
<body id="bd" class="fs<?php echo $ztTools->getParam('zt_font'); ?> <?php echo $ztTools->getParam('zt_display'); ?> <?php echo $ztTools->getParam('zt_display_style'); ?> <?php echo $ztrtl; ?> <?php echo $ztTools->getParamsValue($prefix, 'image', 'bd');?> ">
<div id="zt-wrapper">
	<div id="zt-wrapper-inner">
	
		<div id="zt-header"  class="clearfix">
			<div id="zt-header-inner">
				<div class="zt-wrapper">
				<a id="zt-logo" href="<?php echo $ztTools->baseurl() ; ?>" title="<?php echo $ztTools->sitename() ; ?>"><span><?php echo $ztTools->sitename() ; ?></span></a>
					<?php if($this->countModules('top')) : ?>	
					<div id="zt-top" >					
						<jdoc:include type="modules" name="top"   />
					</div>	 
					<?php endif; ?>   
				</div>
			</div>
		</div>

		<div id="zt-mainmenu" class="png">
			<div id="zt-mainmenu-inner">
				<div class="zt-wrapper">
					  <?php $menu->show(); ?>
				 </div>
			</div>
		</div>	
	
		<?php if($this->countModules('slideshow')) : ?>
			<div id="zt-userwrap2" class="clearfix">
				<div id="zt-userwrap2-inner">
					<div class="zt-wrapper">
						<jdoc:include type="modules" name="slideshow" />
					</div>
				</div>
			</div>		
		<?php endif; ?>	
	
		<?php
		$spotlight = array ('user1','user2','user3','user4');
		$consl = $ztTools->calSpotlight($spotlight,$ztTools->isOP()?100:100,'%');
		if( $consl) :
		?>
		<div id="zt-userwrap3" class="clearfix">
			<div id="zt-userwrap3-inner">			
				<div class="zt-wrapper">
			
					<?php if($this->countModules('user1')) : ?>
					<div id="zt-user1" class="zt-user zt-box<?php echo $consl['user1']['class']; ?>" style="width: <?php echo $consl['user1']['width']; ?>;">
						<div class="zt-box-inside">
							<jdoc:include type="modules" name="user1" style="ztxhtml3" />
						</div>
					</div>
					<?php endif; ?>
					
					<?php if($this->countModules('user2')) : ?>
					<div id="zt-user2" class="zt-user zt-box<?php echo $consl['user2']['class']; ?>" style="width: <?php echo $consl['user2']['width']; ?>;">
						<div class="zt-box-inside">
							<jdoc:include type="modules" name="user2" style="ztxhtml3" />
						</div>
					</div>
					<?php endif; ?>
					
					<?php if($this->countModules('user3')) : ?>
					<div id="zt-user3" class="zt-user zt-box<?php echo $consl['user3']['class']; ?>" style="width: <?php echo $consl['user3']['width']; ?>;">
						<div class="zt-box-inside">
							<jdoc:include type="modules" name="user3" style="ztxhtml3" />
						</div>
					</div>
					<?php endif; ?>
					
					<?php if($this->countModules('user4')) : ?>
					<div id="zt-user4" class="zt-user zt-box<?php echo $consl['user4']['class']; ?>" style="width: <?php echo $consl['user4']['width']; ?>;">
						<div class="zt-box-inside">
							<jdoc:include type="modules" name="user4" style="ztxhtml3" />
						</div>
					</div>
					<?php endif; ?>

				</div>
			</div>	
		</div>	
		<?php endif; ?>
	
	
		<!-- MAINBODY -->
		<div id="zt-mainbody" class="clearfix">
			<div id="zt-mainbody-inner">
				
				<!-- CONTAINER -->
				<div class="zt-wrapper">
					<div id="zt-container<?php echo $zt_width;?>" class="clearfix">
						
						<div id="zt-content-one">
						<div id="zt-content-two">
						<div id="zt-content">
						<div id="zt-maincontent">
						
							<?php if($this->countModules('user5')) : ?>
								<div id="zt-user5" class="clearfix">
									<jdoc:include type="modules" name="user5" style="ztxhtml" />
								</div>
							<?php endif; ?>
								
							<div id="zt-component" class="clearfix">
								<jdoc:include type="message" />
								<jdoc:include type="component" />
							</div>

						   <?php if($this->countModules('user6')) : ?>
							   <div id="zt-user6" class="clearfix">
									<jdoc:include type="modules" name="user6" style="ztxhtml" />
							   </div>
							<?php endif; ?>
						
						</div>
						</div>
						</div>
						</div>
					
						<?php if($this->countModules('right')) : ?>
						<div id="zt-right">
							<div id="zt-right-inner">
								<jdoc:include type="modules" name="right" style="ztxhtml3" />
							</div>
						</div>
						<?php endif; ?>
					</div>	
				
				<!-- END CONTAINER -->
				</div>
			</div>
		</div>
		<!-- END MAINBODY -->

		<div id="zt-userwrap5" class="clearfix">
			<div id="zt-userwrap5-inner" >
			
				<?php
				$spotlight = array ('col1','col2','col3');
				$botsl1 = $ztTools->calSpotlight($spotlight,$ztTools->isOP()?100:99,'%');
				if( $botsl1 ) :
				?>

				<div class="zt-wrapper">
						
					<?php if($this->countModules('col1')) : ?>
						<div id="zt-col1" class="zt-user zt-box<?php echo $botsl1['col1']['class']; ?>" style="width: <?php echo $botsl1['col1']['width']; ?>;">
							<div class="zt-box-inside">
								<jdoc:include type="modules" name="col1" style="ztxhtml" />
							</div>
						</div>
					<?php endif; ?>
					
					<?php if($this->countModules('col2')) : ?>
						<div id="zt-col2" class="zt-user zt-box<?php echo $botsl1['col2']['class']; ?>" style="width: <?php echo $botsl1['col2']['width']; ?>;">
							<div class="zt-box-inside">
								<jdoc:include type="modules" name="col2" style="ztxhtml" />
							</div>
						</div>
					<?php endif; ?>
					
					<?php if($this->countModules('col3')) : ?>
						<div id="zt-col3" class="zt-user zt-box<?php echo $botsl1['col3']['class']; ?>" style="width: <?php echo $botsl1['col3']['width']; ?>;">
							<div class="zt-box-inside">
								<jdoc:include type="modules" name="col3" style="ztxhtml" />
							</div>
						</div>
					<?php endif; ?>
					
				</div>		
				<?php endif; ?>	
				
			</div>
		</div>

		<div id="zt-bottom" class="clearfix">
			<div class="zt-wrapper">
				<div id="zt-bottom-inner">

					<div id="zt-userwrap7" class="clearfix">
						<div id="zt-copyright">
							<div id="zt-copyright-inner">
								
							</div>
						</div>
						<?php if($showTools) : ?>        
						<div id="zt-tools">
						 <?php echo $changecolor; ?>
						</div>
					   <?php endif; ?>
					   
						<?php if($this->countModules('footer')) : ?>
						<div id="zt-footer" class="clearfix">
							<div id="zt-footer-inner"><jdoc:include type="modules" name="footer" /></div>
						</div>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>

	</div>
</div>
</body>
</html>

