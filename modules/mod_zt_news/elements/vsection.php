<?php 
/**
 * @package ZT News Module for Joomla! 2.5.0
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2011- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/

defined('_JEXEC') or die('Restricted access');

defined('JPATH_BASE') or die();
jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');

class JFormFieldVsection extends JFormField
{ 
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'vsection';

	protected function getInput()
	{ 
		$db =& JFactory::getDBO();
		$cId = JRequest::getVar('id','');
		$sql = "SELECT params FROM #__modules WHERE id=$cId"; 
		$db->setQuery($sql);
		$data 		= $db->loadResult();
		$params 	= new JRegistry($data); 
		$template 	= $params->get('template_type', 'horizon');
		$source 	= $params->get('source', 'category');
?>	
		<script type="text/javascript">	
		var jpaneAutoHeight = function(){
			 $$('.jpane-slider').each(function(item){
			      item.setStyle('height','auto'); 
			  });
		};
		
		function sourceChange(val) {
			if(val == 'category') {
				$('jform_params_k2_catid').getParent().setStyle('display', 'none');
				$('jform_params_catid').getParent().setStyle('display', 'block');
			}
			else {
				$('jform_params_k2_catid').getParent().setStyle('display', 'block');
				$('jform_params_catid').getParent().setStyle('display', 'none');
			}
		}
		
		window.addEvent('load',function(){
			 setTimeout(jpaneAutoHeight,400);
			 var layout = "<?php echo $template; ?>";
			 var rowNewsCat = $('jform_params_cat_ordering').getParent();
		     for(i=0;i<=5;i++){
		    	 rowNewsCat.addClass('jv_news_category');		    	  
		    	 rowNewsCat = rowNewsCat.getNext();		    	 
			}
			var rowNewsSection = $('jform_params_v_section_orderding').getParent();	
			for(i=0;i<=5;i++){
				rowNewsSection.addClass('jv_news_section');
				rowNewsSection = rowNewsSection.getNext();
			}
			var jvNewsCat = $$('.jv_news_category');
			var jvNewsSect = $$('.jv_news_section');	
			var selectStyle = function(style){
				switch(style) {
				case "horizon":
					jvNewsCat.each(function(item){
						item.setStyle('display','');
   					}.bind(this));
					jvNewsSect.each(function(item){
						item.setStyle('display','none');
   					}.bind(this));	 
					break;
				case "vertical":
					jvNewsCat.each(function(item){
						item.setStyle('display','none');
   					}.bind(this));
					jvNewsSect.each(function(item){
						item.setStyle('display','');
   					}.bind(this));
					break;
				}
			}
			selectStyle(layout);
			$('jform_params_template_type').addEvent('change',function(){
				selectStyle(this.value);                
		  	});
			
			//Source change
			sourceChange('<?php echo $source;?>');
			$('jform_params_source').addEvent('change',function(){
				sourceChange(this.value);
		  	});
		});
		</script>
	<?php
	}
}
