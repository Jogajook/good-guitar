<?php
/**
 * @package ZT Accordion Menu module for Joomla! 2.5
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
?>
<script language="javascript">
<?php if($params->get('is_exppand_active')){ ?>
 <?php
 	if(count($pids))
	{
		$li		= array();
		for($i 	= 0; $i < count($pids); $i++)
		{
			$li[]	= '"jv_amenu'.$module->id.'_'.$pids[$i].'"';
		}
		$idActive	= implode(", ", $li);
		echo 'var exppand_active = true;';
		echo 'var active	= Array('.$idActive.');';
	}
	else
	{
		echo 'var exppand_active = false;';
	}
?>
<?php } else {echo 'var exppand_active = false;';}?>
<?php if($params->get('is_slide')){echo 'var slide = true;';} else {echo 'var slide = false;';}?>
</script>
<?php
	$eventType = $params->get('event_type');
	if($eventType == 0)
	{
		JHTML::_('stylesheet','style_hover.css','modules/mod_zt_accordion_menu/assets/css/');
		JHTML::_('script','zt.accordion_hover.js','modules/mod_zt_accordion_menu/assets/js/');
	}
	else
	{
		JHTML::_('stylesheet','style_click.css','modules/mod_zt_accordion_menu/assets/css/');
		JHTML::_('script','zt.accordion_click.js','modules/mod_zt_accordion_menu/assets/js/');
	}
	$itemId = JRequest::getVar('Itemid',1);
?>

<?php if($eventType == 0){ ?>
<div class="jv_ahovermenu_wrap">
<?php } else { ?>
<div class="jv_aclickmenu_wrap">
<?php } ?>
	<div id="jv_amenu_side<?php echo $module->id; ?>">
		<?php $jvAMenuHelper->showMenu($params,$itemId); ?>
	</div>
</div>