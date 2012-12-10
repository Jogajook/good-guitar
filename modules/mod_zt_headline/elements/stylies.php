<?php
/**
 * @package ZT Headline module 
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2010- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/
defined('JPATH_BASE') or die(); 
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
class JFormFieldStylies extends JFormFieldList
{ 
	protected $type = 'stylies'; 

	public function getOptions()
	{
		//Get value of layout style from database
		$document = JFactory::getDocument();
		$document->addStylesheet(JURI::root() . 'modules/mod_zt_headline/admin/css/adminstyle.css');
		$db = &JFactory::getDBO();
		$cId = JRequest::getVar('cid','');
		if($cId !='') $cId = $cId[0];
		if($cId == ''){
			$cId = JRequest::getVar('id');
		}
		$sql = "SELECT params FROM #__modules WHERE id=$cId";
		$db->setQuery($sql);
		$paramsConfigObj = $db->loadObjectList();
		$db->setQuery($sql);
		$data = $db->loadResult();
		$params = new JRegistry($data);  
		$layoutStyle = $params->get('layout_style', 'zt_slideshow');
		//End get value of layout style
		$options = array (); 
		$val = "zt_slideshow";
		$text = "ZT Slideshow";
		$options[] = JHTML::_('select.option', $val, JText::_($text));
		$val = "zt_scroller";
		$text = "ZT Scroller";
		$options[] = JHTML::_('select.option', $val, JText::_($text));
		$val = "zt_featurelist";
		$text = "ZT FeatureList";
		$options[] = JHTML::_('select.option', $val, JText::_($text));
		$val = "zt_carousel";
		$text = "ZT Carousel";
		$options[] = JHTML::_('select.option', $val, JText::_($text));
		$val = "zt_accordion";
		$text = "ZT Accordion";
		$options[] = JHTML::_('select.option', $val, JText::_($text));
		?>
		<script type="text/javascript">	
			var jpaneAutoHeight = function(){
				$$('.jpane-slider').each(function(item, i){
					if(i == 0)item.setStyle('height','auto');				
				});
			};
			window.addEvent('load',function(){		     		     
				setTimeout(jpaneAutoHeight, 400);	 
				var ZT_Slideshow = $('jform_params_zt_slideshow_effect').getParent();
				for(i=0;i<=8;i++){
					ZT_Slideshow.addClass('zt_slideshow');
					ZT_Slideshow = ZT_Slideshow.getNext();
				}
				var ZT_Scroller = $('jform_params_zt_scroller_effect').getParent();
				for(i=0;i<=6;i++){
					ZT_Scroller.addClass('zt_scroller');
					ZT_Scroller = ZT_Scroller.getNext();
				}
				var ZT_Featurelist = $('jform_params_zt_featurelist_thumbwidth').getParent();
				for(i=0;i<=6;i++){
					ZT_Featurelist.addClass('zt_featurelist');
					ZT_Featurelist = ZT_Featurelist.getNext();
				}
				var ZT_Carousel = $('jform_params_zt_carousel_autorun').getParent();
				for(i=0;i<=0;i++){
					ZT_Carousel.addClass('zt_carousel');
					ZT_Carousel = ZT_Carousel.getNext();
				}
				var ZT_Accordion = $('jform_params_zt_accordion_item_expand').getParent();
				for(i=0;i<=2;i++){
					ZT_Accordion.addClass('zt_accordion');
					ZT_Accordion = ZT_Accordion.getNext();
				}
				var zt_slideshowStyle = $$('.zt_slideshow'); 
				var zt_scrollerStyle = $$('.zt_scroller');
				var zt_featurelistStyle = $$('.zt_featurelist');
				var zt_carouselStyle = $$('.zt_carousel');
				var zt_accordionStyle = $$('.zt_accordion');
				var layout = "<?php echo $layoutStyle; ?>"; 
				var selectStyle = function(style){				
					switch(style){               
						case "zt_slideshow": 
							zt_slideshowStyle.each(function(item){
								item.setStyle('display','');
							});
							zt_scrollerStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_featurelistStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_carouselStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_accordionStyle.each(function(item){
								item.setStyle('display','none');
							});
						break;
						case "zt_scroller": 
							zt_slideshowStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_scrollerStyle.each(function(item){
								item.setStyle('display','');
							});
							zt_featurelistStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_carouselStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_accordionStyle.each(function(item){
								item.setStyle('display','none');
							});
						break;
						case "zt_featurelist": 
							zt_slideshowStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_scrollerStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_featurelistStyle.each(function(item){
								item.setStyle('display','');
							});
							zt_carouselStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_accordionStyle.each(function(item){
								item.setStyle('display','none');
							});
						break;
						case "zt_carousel": 
							zt_slideshowStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_scrollerStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_featurelistStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_carouselStyle.each(function(item){
								item.setStyle('display','');
							});
							zt_accordionStyle.each(function(item){
								item.setStyle('display','none');
							});
						break;
						case "zt_accordion": 
							zt_slideshowStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_scrollerStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_featurelistStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_carouselStyle.each(function(item){
								item.setStyle('display','none');
							});
							zt_accordionStyle.each(function(item){
								item.setStyle('display','');
							});
						break;
					};
				} 
				selectStyle(layout);                           
				$('jform_params_layout_style').addEvent('change',function(){ 
					selectStyle(this.value);                
				});           
			});		 
		</script>
		<?php 
		@array_unshift($options, JHtml::_('select.option', '0', '--Select--', 'value', 'text', $this->value, $control_name.$this->name));
		return $options;
	}
}  
?>