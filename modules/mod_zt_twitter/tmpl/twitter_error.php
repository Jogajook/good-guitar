<?php
/**
 * @package ZT Twitter module for Joomla! 1.6
 * @author http://www.zootemplate.com
 * @copyright (C) 2011- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
$document 	= &JFactory::getDocument();
$css 		= JURI::base().'modules/mod_zt_twitter52/assets/css/zt_twitter.css';
$document->addStyleSheet($css);
?>
<div style="display: none;"><a href="http://www.joomvision.com" title="Joomla Templates">Joomla Templates</a> and Joomla Extensions by JoomVision.Com</div>
<div class="jv_wrap_twitter">
<div class="twitter_content">    
    <?php foreach($aryUser as $user){ ?>
        <div class="jv_twit_follow">
        	<div class="jv_twit_follow_cont"><?php echo JText::_('FOLLOW_TITLE')?>&nbsp;<a href="http://twitter.com/<?php echo $user; ?>"><?php echo $user; ?></a> on twitter</div>
       </div>
     <?php } ?>   
    </ul>
</div>
</div>