<?php
/**
* @version 1.5.x
* @package ZooTemplate Project
* @email webmaster@zootemplate.com
* @copyright (c) 2008 - 2011 http://www.ZooTemplate.com. All rights reserved.
*/

$groups = array('bd'=>'Body', 'zt-userwrap4-inner'=>'Footer');
$value  = array();

$prefix = "ota";

//Body Group
$value['bd']['color'] = $ztTools->getParamsValue($prefix, 'color', 'bd');
$value['bd']['text'] = $ztTools->getParamsValue($prefix, 'text', 'bd');
$value['bd']['link'] = $ztTools->getParamsValue($prefix, 'link', 'bd');
$value['bd']['image'] = array($ztTools->getParamsValue($prefix, 'image', 'bd'), array('pattern1', 'pattern2', 'pattern3', 'pattern4', 'pattern5', 'pattern6', 'pattern7', 'pattern8'));

//Body Group
$value['zt-userwrap4-inner']['color'] = $ztTools->getParamsValue($prefix, 'color', 'zt-userwrap4-inner');
$value['zt-userwrap4-inner']['text'] = $ztTools->getParamsValue($prefix, 'text', 'zt-userwrap4-inner');
$value['zt-userwrap4-inner']['image'] = array($ztTools->getParamsValue($prefix, 'image', 'zt-userwrap4-inner'), array('pattern1', 'pattern2', 'pattern3', 'pattern4', 'pattern5', 'pattern6', 'pattern7', 'pattern8'));

?>
<style type="text/css">
	#bd{
		color: <?php echo $ztTools->getParamsValue($prefix, 'text', 'bd'); ?>;
		background-color: <?php echo $ztTools->getParamsValue($prefix, 'color', 'bd'); ?>;
	}
	#bd a {
		color: <?php echo $ztTools->getParamsValue($prefix, 'link', 'bd'); ?>;
	}
	
	#zt-userwrap4-inner{
		color: <?php echo $ztTools->getParamsValue($prefix, 'text', 'zt-userwrap4-inner'); ?>;
		background-color: <?php echo $ztTools->getParamsValue($prefix, 'color', 'zt-userwrap4-inner'); ?>;
	}
	
</style>