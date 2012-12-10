<?php
/**
 * @package ZT Headline module
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2010- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' ); 
jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield'); 
class JFormFieldtextselection extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $type = 'textselection';
	
	
	protected function getInput(){ 
		if (! @$class) {
			$class = "inputbox";
		}
		$db = &JFactory::getDBO();
		$document 	= JFactory::getDocument();
		$document->addScriptDeclaration('function jInsertFieldValue(value,id) { var old_id = document.getElementById(id).value; if (old_id != id) { document.getElementById(id).value = value; } }  ');
		$document->addScript(JURI::root() . 'modules/mod_zt_headline/admin/js/zt_dropdrap.js');
		JHtml::_('behavior.modal', 'a.modal');
		$url = str_replace('administrator/','',JURI::base()); 
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
		$params = new JParameter($data);
		$text_data = $params->get('text_data');
		$dataarray = explode('||', $text_data); 
		$selection = array();  
		$arySelection = array(); 
		$baseURL = JURI::base(); 
		$html_return = ' 
		<div id="txselect"> 
			<div id="zt_header">Your slides:<span class="addslide">Add new slide</span></div>
			<div class="content-form" id="content-form">
				<div class="height_scroll" style="height: 0px;">
					<div class="itemsadd">
						<div class="maintext">
							<span class="title">'.JText::_( 'Content Type: ' ).'</span>
							<select name="jform_content_type" id="jform_content_type">
								<option value="content">Content Article</option>
								<option value="custom">Custom</option>
								<option value="k2">K2 Article</option>
							</select>
						</div>
						<div class="maintext">
							<span class="title">'.JText::_( 'Image: ' ).'</span>
							<input id="jform_img_path" value="" name="jform_img_path" size="25" readonly="readonly" class="imgonly"/>
							<div class="button2-left">
								<div class="image">
									<a rel="{handler: \'iframe\'}" href="'.JURI::base().'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;fieldid=jform_img_path&amp;asset=&amp;author=" title="Image" class="modal">Image</a>
								</div>
							</div>
						</div>
						<div class="maintext">
							<span class="title">'.JText::_( 'Title: ' ).'</span>
							<input id="jform_text_title" value="" name="jform_text_title" size="25"/> 
						</div>
						<div class="maintext">
							<span class="title">'.JText::_( 'Content Article: ' ).'</span>
							<input id="jform_content_article" value="" name="jform_content_article" size="25" readonly="readonly" class="imgonly"/>
							<input type="hidden" id="jform_content_article_id" value="" name="jform_content_article_id" size="25" readonly="readonly" class="imgonly"/>
							<input type="hidden" id="jform_content_article_title" value="" name="jform_content_article_title" size="25" readonly="readonly" class="imgonly"/>
							<div class="button2-left">
								<div class="image">
									<a rel="{handler: \'iframe\'}" href="'.JURI::base().'index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jSelectArticleFist" title="Select Content Article" class="modal">Select / Change</a>
								</div>
							</div>
						</div>
						<div class="maintext">
							<span class="title">'.JText::_( 'K2 Article: ' ).'</span>
							<input id="jform_k2_article" value="" name="jform_k2_article" size="25" readonly="readonly" class="imgonly"/>
							<input type="hidden" id="jform_k2_article_id" value="" name="jform_k2_article_id" size="25" readonly="readonly" class="imgonly"/>
							<input type="hidden" id="jform_k2_article_title" value="" name="jform_k2_article_title" size="25" readonly="readonly" class="imgonly"/>
							<div class="button2-left">
								<div class="image">
									<a rel="{handler: \'iframe\', size: {x: 700, y: 450}}" href="index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object=jform_k2_article" title="Select K2 Article" class="modal">Select / Change</a>
								</div>
							</div>
						</div>
						<div class="maintext">
							<span class="title">'.JText::_( 'Content: ' ).'</span>
							<textarea id="jform_add_content" class="jform_add_content" aria-invalid="false" rows="7" cols="40"></textarea> 
						</div>
						<div class="maintext">
							<span class="title">'.JText::_( 'Url: ' ).'</span>
							<input id="jform_text_link" value="" name="jform_text_link" size="42"/> 
						</div>
						<div class="maintext">
							<div class="textbutton">
								<span class="custombtn jform_save">'.JText::_( 'Save' ).'</span>
								<span class="custombtn jform_cancel">'.JText::_( 'Cancel' ).'</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="custom_list">';
				$i=0;
				foreach($dataarray as $item){
					if($item){
					$itemarray = explode('[]', $item);
					$content_type = $itemarray[0];
					$title = $itemarray[1];
					$img = $itemarray[2];
					$content_art_id='';
					$content_art_title='';
					$k2_art_id='';
					$k2_art_title='';
					$content='';
					$url='';
					if($content_type=='content'){
						$content_art_id .= $itemarray[3];
						$content_art_title .= $itemarray[4];
					}else if($content_type=='k2'){
						$k2_art_id .= $itemarray[3];
						$k2_art_title .= $itemarray[4];
					}else{
						$content .= $itemarray[3];
						$url .= $itemarray[4];
					}  
					$html_return .= '
						<div class="custom_item">
							<div class="custom_item_preview">
								<span class="custom_item_title">'.$title.'</span>
								<span class="move" title="Move"></span>
								<a class="remove" title="Remove" href="#remove"></a>
								<a class="edit" title="Edit" href="#edit"></a>
								<a class="modal" title="Preview" rel="{handler: \'image\'}" href="../'.$img.'">preview</a>
								<span class="type" title="Type">'.$content_type.'</span>
							</div>
							<div class="content-form-edit" id="content-form-edit" style="height: 0px;">
								<div class="height_scroll">
									<div class="itemsadd-edit">
										<div class="maintext">
											<span class="title">'.JText::_( 'Content Type: ' ).'</span>
											<select name="jform_content_type" id="jform_content_type_'.$i.'" class="jform_content_type">
												<option value="content">Content Article</option>
												<option value="custom">Custom</option>
												<option value="k2">K2 Article</option>
											</select>
										</div>
										<div class="maintext">
											<span class="title">Image:</span>
											<input id="jform_img_path_'.$i.'" readonly="readonly" value="'.$img.'" name="jform_img_path" size="25" class="imgonly">
											<div class="button2-left">
												<div class="image">
													<a class="modal" title="Image" rel="{handler: &quot;iframe&quot;, size: {x: 800, y: 500}}" href="'.JURI::base().'index.php?option=com_media&view=images&tmpl=component&fieldid=jform_img_path_'.$i.'&asset=&author=">Image</a>
												</div>
											</div>
										</div>
										<div class="maintext">
											<span class="title">Title:</span>
											<input id="jform_text_title_'.$i.'" value="'.$title.'" name="jform_text_title" size="25">
										</div>
										<div class="maintext">
											<span class="title">'.JText::_( 'Content Article: ' ).'</span>
											<input id="jform_content_article_'.$i.'" value="'.$content_art_title.'" name="jform_content_article" size="25" readonly="readonly" class="imgonly"/>
											<input type="hidden" id="jform_content_article_id_'.$i.'" value="'.$content_art_id.'" name="jform_content_article_id" size="25" readonly="readonly" class="imgonly"/>
											<input type="hidden" id="jform_content_article_title_'.$i.'" value="'.$content_art_title.'" name="jform_content_article_title" size="25" readonly="readonly" class="imgonly"/>
											<div class="button2-left">
												<div class="image">
													<a rel="{handler: \'iframe\'}" href="'.JURI::base().'index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jSelectArticle" title="Select Content Article" class="modal">Select / Change</a>
												</div>
											</div>
										</div>
										<div class="maintext">
											<span class="title">'.JText::_( 'K2 Article: ' ).'</span>
											<input id="jform_k2_article_'.$i.'" value="'.$k2_art_title.'" name="jform_k2_article" size="25" readonly="readonly" class="imgonly"/>
											<input type="hidden" id="jform_k2_article_id_'.$i.'" value="'.$k2_art_id.'" name="jform_k2_article_id" size="25" readonly="readonly" class="imgonly"/>
											<input type="hidden" id="jform_k2_article_title_'.$i.'" value="'.$k2_art_title.'" name="jform_k2_article_title" size="25" readonly="readonly" class="imgonly"/>
											<div class="button2-left">
												<div class="image">
													<a rel="{handler: \'iframe\', size: {x: 700, y: 450}}" href="index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object=jform_k2_article_'.$i.'" title="Select K2 Article" class="modal">Select / Change</a>
												</div>
											</div>
										</div>
										<div class="maintext">
											<span class="title">Content:</span>
											<textarea id="jform_add_content_'.$i.'" class="jform_add_content" aria-invalid="false" rows="7" cols="40">'.$content.'</textarea>
										</div>
										<div class="maintext">
											<span class="title">Url:</span>
											<input id="jform_text_link_'.$i.'" value="'.$url.'" name="jform_text_link" size="42">
										</div>
										<div class="maintext">
											<div class="textbutton">
												<span class="custombtn jform_save" onclick="upgradeItem('.$i.')">Save</span>
												<span class="custombtn jform_cancel" onclick="cancelItem('.$i.')">Cancel</span>
											</div>
										</div>
									</div>
								</div>
								<input type="hidden" value="'.$item.'" class="hiddendata" id="hiddendata_'.$i.'">
								<input type="hidden" value="'.$i.'" class="numbsitem">
							</div>
						</div>
					';
					}
					$i++;
				} 
			$html_return .= '</div>
		</div>';
		?>
		<script type="text/javascript">
			var $numbs = 0;
			var $curr_open = 0;
			window.addEvent('domready', function(){ 
				$('jform_params_text_data').getParent().setStyle("display","none");
				var customform = $('content-form'); 
				var customform_btns = $$('div#content-form span.custombtn');
				var scroll_wrap = customform.getElement('.height_scroll');
				var fx = new Fx.Tween(scroll_wrap, { duration: 500, property: 'height', onComplete: function() { if(scroll_wrap.getSize().y > 0) scroll_wrap.setStyle('height', 'auto'); } });
				document.id('zt_header').getElement('span').addEvent('click', function(e) {
					e.stop();
					fx.start(customform.getElement('.itemsadd').getSize().y);
				});
				customform_btns[1].addEvent('click', function(e) {
					if(e) e.stop(); 
					scroll_wrap.setStyle('height', scroll_wrap.getSize().y + 'px');
					customform.getElement('#jform_img_path').value="";
					customform.getElement('#jform_text_title').value="";
					customform.getElement('#jform_add_content').value="";
					customform.getElement('#jform_text_link').value="";
					customform.getElement('#jform_content_type').selectedIndex=1;
					customform.getElement('#jform_content_article').value="";
					customform.getElement('#jform_content_article_id').value="";
					customform.getElement('#jform_content_article_title').value="";
					customform.getElement('#jform_k2_article').value="";
					customform.getElement('#jform_k2_article_id').value="";
					customform.getElement('#jform_k2_article_title').value="";
					$('jform_content_article').getParent().setStyle('display','none');
					$('jform_k2_article').getParent().setStyle('display','none');
					$('jform_add_content').getParent().setStyle('display','');
					$('jform_text_link').getParent().setStyle('display','');
					fx.start(0);
				});
				$('jform_content_type').selectedIndex=1;
				customform_btns[0].addEvent('click', function(e) {
					addItem('new');
				});
				var selectContentType = function(type){
					if(type == 'content'){
						$('jform_content_article').getParent().setStyle('display','');
						$('jform_k2_article').getParent().setStyle('display','none');
						$('jform_add_content').getParent().setStyle('display','none');
						$('jform_text_link').getParent().setStyle('display','none');
					}else if(type == 'custom'){
						$('jform_content_article').getParent().setStyle('display','none');
						$('jform_k2_article').getParent().setStyle('display','none');
						$('jform_add_content').getParent().setStyle('display','');
						$('jform_text_link').getParent().setStyle('display','');
					}else{
						$('jform_content_article').getParent().setStyle('display','none');
						$('jform_k2_article').getParent().setStyle('display','');
						$('jform_add_content').getParent().setStyle('display','none');
						$('jform_text_link').getParent().setStyle('display','none');
					}
				}; 	 
				$('jform_content_type').addEvent('change',function(){
					selectContentType(this.value);                
				});
				selectContentType('custom');
				function addItem(source) {
					var content_type = $('jform_content_type').value;
					var title = $('jform_text_title').value;
					var images = $('jform_img_path').value;
					var content_art_id = $('jform_content_article_id').value;
					var content_art_title = $('jform_content_article_title').value;
					var k2_art_id = $('jform_k2_article_id').value;
					var k2_art_title = $('jform_k2_article_title').value;
					var content = htmlspecialchars($('jform_add_content').value);  
					var url = $('jform_text_link').value;
					if(source=='new'){
						if(title!='' && images!=''){
							var container = new Element('div',{'class':'custom_item'}).injectInside($('custom_list'));
							var item_preview = new Element('div',{'class':'custom_item_preview'}).injectInside(container);
							var span_title = new Element('span',{'class':'custom_item_title'}).set("html", title).injectInside(item_preview);
							var span_down = new Element('span',{'class':'move', 'title':'Move'}).injectInside(item_preview); 
							var a_rm = new Element('a',{'class':'remove', 'title':'Remove', 'href':'#remove'}).injectInside(item_preview);
							var a_edit = new Element('a',{'class':'edit', 'title':'Edit', 'href':'#edit'}).injectInside(item_preview);
							var a_pre = new Element('a',{'class':'modal', 'href':'../'+images, 'rel':'{handler: \'image\'}', 'title':'Preview'}).set("html","preview").injectInside(item_preview);
							var type = new Element('span',{'class':'type', 'title':'Type'}).set("html",content_type).injectInside(item_preview);
							var container_edit = new Element('div',{'class':'content-form-edit', 'id':'content-form-edit', 'style':'height:0px;'}).injectInside(container);
							var container_height = new Element('div',{'class':'height_scroll'}).injectInside(container_edit);
							var itemadd_edit = new Element('div',{'class':'itemsadd-edit'}).injectInside(container_height);
							var maintext_type = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var type_title = new Element('span',{'class':'title'}).set("html", "Content Type:").injectInside(maintext_type);
							var select_contenttype = new Element('select',{'id':'jform_content_type_'+$numbs, 'class':'jform_content_type', 'name':'jform_content_type'}).injectInside(maintext_type);
							var maintext = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var spimg_title = new Element('span',{'class':'title'}).set("html", "Image:").injectInside(maintext);
							var input_imp = new Element('input',{'value':images, 'id':'jform_img_path_'+$numbs, 'name':'jform_img_path', 'size':'25', 'readonly':'readonly', 'class':'imgonly'}).injectInside(maintext);
							var button2 = new Element('div',{'class':'button2-left'}).injectInside(maintext);
							var button2_img = new Element('div',{'class':'image'}).injectInside(button2);
							var select_img = new Element('a',{'class':'modal', 'title':'Image', 'rel':'{handler: "iframe", size: {x: 800, y: 500}}', 'href':'<?php echo JURI::base();?>index.php?option=com_media&view=images&tmpl=component&fieldid=jform_img_path_'+$numbs+'&asset=&author='}).set("html","Image").injectInside(button2_img);
							var maintext2 = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var sptitle_title = new Element('span',{'class':'title'}).set("html", "Title:").injectInside(maintext2);
							var input_title = new Element('input',{'value':title, 'id':'jform_text_title_'+$numbs, 'name':'jform_text_title', 'size':'25'}).injectInside(maintext2);
							var maintext_content = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var content_title = new Element('span',{'class':'title'}).set("html", "Content Article:").injectInside(maintext_content);
							var select_input_content = new Element('input',{'value':content_art_title, 'id':'jform_content_article_'+$numbs, 'name':'jform_content_article', 'readonly':'readonly'}).injectInside(maintext_content);
							var hidden_content_id = new Element('input',{'value':content_art_id, 'id':'jform_content_article_id_'+$numbs, 'name':'jform_content_article_id', 'type':'hidden'}).injectInside(maintext_content);
							var hidden_content_title = new Element('input',{'value':content_art_title, 'id':'jform_content_article_title_'+$numbs, 'name':'jform_content_article_title', 'type':'hidden'}).injectInside(maintext_content);
							var button_select_content = new Element('div',{'class':'button2-left'}).injectInside(maintext_content);
							var button_content_img = new Element('div',{'class':'image'}).injectInside(button_select_content);
							var select_content_art = new Element('a',{'class':'modal', 'title':'Select Content Article', 'rel':'{handler: "iframe", size: {x: 800, y: 500}}', 'href':'<?php echo JURI::base();?>index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jSelectArticle'}).set("html","Select / Change").injectInside(button_content_img);
							var maintext_k2 = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var k2_title = new Element('span',{'class':'title'}).set("html", "K2 Article:").injectInside(maintext_k2);
							var select_input_k2 = new Element('input',{'value':k2_art_title, 'id':'jform_k2_article_'+$numbs, 'name':'jform_k2_article', 'readonly':'readonly'}).injectInside(maintext_k2);
							var hidden_k2_id = new Element('input',{'value':k2_art_id, 'id':'jform_k2_article_id_'+$numbs, 'name':'jform_k2_article_id', 'type':'hidden'}).injectInside(maintext_k2);
							var hidden_k2_title = new Element('input',{'value':k2_art_title, 'id':'jform_k2_article_title_'+$numbs, 'name':'jform_k2_article_title', 'type':'hidden'}).injectInside(maintext_k2);
							var button_select_k2 = new Element('div',{'class':'button2-left'}).injectInside(maintext_k2);
							var button_k2_img = new Element('div',{'class':'image'}).injectInside(button_select_k2);
							var select_k2_art = new Element('a',{'class':'modal', 'title':'Select K2 Article', 'rel':'{handler: "iframe", size: {x: 800, y: 500}}', 'href':'<?php echo JURI::base();?>index.php?option=com_k2&amp;view=items&amp;task=element&amp;tmpl=component&amp;object=jform_k2_article_'+$numbs+''}).set("html","Select / Change").injectInside(button_k2_img);
							var maintext3 = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var spcontent_title = new Element('span',{'class':'title'}).set("html", "Content:").injectInside(maintext3);
							var text_content = new Element('textarea',{'value':htmlspecialchars_decode(content), 'id':'jform_add_content_'+$numbs, 'class':'jform_add_content', 'aria-invalid':'false', 'rows':'7', 'cols':'40'}).injectInside(maintext3);
							var maintext4 = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var spurl_title = new Element('span',{'class':'title'}).set("html", "Url:").injectInside(maintext4);
							var input_url = new Element('input',{'value':url, 'id':'jform_text_link_'+$numbs, 'name':'jform_text_link', 'size':'42'}).injectInside(maintext4);
							var maintext5 = new Element('div',{'class':'maintext'}).injectInside(itemadd_edit);
							var textbutton = new Element('div',{'class':'textbutton'}).injectInside(maintext5);
							var spbuttonsv = new Element('span',{'class':'custombtn jform_save', 'onclick':'upgradeItem('+$numbs+')'}).set("html","Save").injectInside(textbutton);
							var spbuttoncan = new Element('span',{'class':'custombtn jform_cancel', 'onclick':'cancelItem('+$numbs+')'}).set("html","Cancel").injectInside(textbutton); 
							if(content_type=='content'){
								var input_db = new Element('input',{'value':content_type+'[]'+title+'[]'+images+'[]'+content_art_id+'[]'+content_art_title, 'type':'hidden', 'class':'hiddendata', 'id':'hiddendata_'+$numbs}).injectInside(container_edit);
							}else if(content_type=='k2'){
								var input_db = new Element('input',{'value':content_type+'[]'+title+'[]'+images+'[]'+k2_art_id+'[]'+k2_art_title, 'type':'hidden', 'class':'hiddendata', 'id':'hiddendata_'+$numbs}).injectInside(container_edit);
							}else{
								var input_db = new Element('input',{'value':content_type+'[]'+title+'[]'+images+'[]'+content+'[]'+url, 'type':'hidden', 'class':'hiddendata', 'id':'hiddendata_'+$numbs}).injectInside(container_edit);
							} 
							var numbsitem = new Element('input',{'value':$numbs, 'type':'hidden', 'class':'numbsitem'}).injectInside(container_edit); 
							SqueezeBox.assign($$('a.modal'), {
								parse: 'rel'
							});
							for(k = 0; k < $('jform_content_type').options.length; k++) {
								var opttype       = document.createElement('option');
								opttype.value     = $('jform_content_type').options[k].value; 
								opttype.innerHTML = $('jform_content_type').options[k].innerHTML; 
								$('jform_content_type_'+$numbs).appendChild(opttype); 
							} 
							for(m = 0; m < $('jform_content_type_'+$numbs).options.length; m++) { 
								if($('jform_content_type_'+$numbs).options[m].value == content_type) { 
									$('jform_content_type_'+$numbs).options[m].selected = true;
								}
							}
							var changeContentType = function(type,wait){
								if(wait){
									if(type == 'content'){
										$('jform_content_article_'+($numbs-1)).getParent().setStyle('display','');
										$('jform_k2_article_'+($numbs-1)).getParent().setStyle('display','none');
										$('jform_add_content_'+($numbs-1)).getParent().setStyle('display','none');
										$('jform_text_link_'+($numbs-1)).getParent().setStyle('display','none');
									}else if(type == 'k2'){
										$('jform_content_article_'+($numbs-1)).getParent().setStyle('display','none');
										$('jform_k2_article_'+($numbs-1)).getParent().setStyle('display','');
										$('jform_add_content_'+($numbs-1)).getParent().setStyle('display','none');
										$('jform_text_link_'+($numbs-1)).getParent().setStyle('display','none');
									}else{
										$('jform_content_article_'+($numbs-1)).getParent().setStyle('display','none');
										$('jform_k2_article_'+($numbs-1)).getParent().setStyle('display','none');
										$('jform_add_content_'+($numbs-1)).getParent().setStyle('display','');
										$('jform_text_link_'+($numbs-1)).getParent().setStyle('display','');
									}
								}else{
									if(type == 'content'){
										$('jform_content_article_'+$numbs).getParent().setStyle('display','');
										$('jform_k2_article_'+$numbs).getParent().setStyle('display','none');
										$('jform_add_content_'+$numbs).getParent().setStyle('display','none');
										$('jform_text_link_'+$numbs).getParent().setStyle('display','none');
									}else if(type == 'k2'){
										$('jform_content_article_'+$numbs).getParent().setStyle('display','none');
										$('jform_k2_article_'+$numbs).getParent().setStyle('display','');
										$('jform_add_content_'+$numbs).getParent().setStyle('display','none');
										$('jform_text_link_'+$numbs).getParent().setStyle('display','none');
									}else{
										$('jform_content_article_'+$numbs).getParent().setStyle('display','none');
										$('jform_k2_article_'+$numbs).getParent().setStyle('display','none');
										$('jform_add_content_'+$numbs).getParent().setStyle('display','');
										$('jform_text_link_'+$numbs).getParent().setStyle('display','');
									}
								} 
							}; 	 
							$('jform_content_type_'+$numbs).addEvent('change',function(){
								changeContentType(this.value,true);                
							});
							changeContentType(content_type);
							var art_id = $('jform_params_text_data'); 
							var st_id = art_id.value;
							if(content_type=='content'){
								st_id = st_id + content_type +'[]'+ title + '[]' + images +'[]'+ content_art_id +'[]'+ content_art_title +'||'; 
							}else if(content_type=='k2'){
								st_id = st_id + content_type +'[]'+ title + '[]' + images +'[]'+ k2_art_id +'[]'+ k2_art_title +'||'; 
							}else {
								st_id = st_id + content_type +'[]'+ title +'[]'+ images + '[]' + content + '[]' + url +'||';
							} 
							art_id.innerHTML = st_id; 
							fireEvent('sortingready'); 
							customform_btns[1].fireEvent('click'); 
						}else{
							alert('Please insert data');	
						} 
					}else{
						var checktype = $('hiddendata_'+$numbs).value.split('[]');
						var strrep = htmlspecialchars_decode($('jform_add_content_'+$numbs).value);
						var art_id = $('jform_add_content_'+$numbs);
						art_id.innerHTML=strrep;
						
						if(checktype[0]=='content'){ 
							$('jform_content_type_'+$numbs).selectedIndex=0; 
							$('jform_content_article_'+$numbs).getParent().setStyle('display','');
							$('jform_k2_article_'+$numbs).getParent().setStyle('display','none');
							$('jform_add_content_'+$numbs).getParent().setStyle('display','none');
							$('jform_text_link_'+$numbs).getParent().setStyle('display','none');
						}
						if(checktype[0]=='k2'){
							$('jform_content_type_'+$numbs).selectedIndex=2; 
							$('jform_content_article_'+$numbs).getParent().setStyle('display','none');
							$('jform_k2_article_'+$numbs).getParent().setStyle('display','');
							$('jform_add_content_'+$numbs).getParent().setStyle('display','none');
							$('jform_text_link_'+$numbs).getParent().setStyle('display','none');
						}
						if(checktype[0]=='custom'){
							$('jform_content_type_'+$numbs).selectedIndex=1; 
							$('jform_content_article_'+$numbs).getParent().setStyle('display','none');
							$('jform_k2_article_'+$numbs).getParent().setStyle('display','none');
							$('jform_add_content_'+$numbs).getParent().setStyle('display','');
							$('jform_text_link_'+$numbs).getParent().setStyle('display','');
						}
						var ContentType = function(type,numb){ 
							if(type == 'content'){
								$('jform_content_article_'+numb).getParent().setStyle('display','');
								$('jform_k2_article_'+numb).getParent().setStyle('display','none');
								$('jform_add_content_'+numb).getParent().setStyle('display','none');
								$('jform_text_link_'+numb).getParent().setStyle('display','none');
							}else if(type == 'k2'){
								$('jform_content_article_'+numb).getParent().setStyle('display','none');
								$('jform_k2_article_'+numb).getParent().setStyle('display','');
								$('jform_add_content_'+numb).getParent().setStyle('display','none');
								$('jform_text_link_'+numb).getParent().setStyle('display','none');
							}else{
								$('jform_content_article_'+numb).getParent().setStyle('display','none');
								$('jform_k2_article_'+numb).getParent().setStyle('display','none');
								$('jform_add_content_'+numb).getParent().setStyle('display','');
								$('jform_text_link_'+numb).getParent().setStyle('display','');
							}
						}; 
						$$('.jform_content_type').each(function(e,i){ 
							e.addEvent('change', function(){
								ContentType(this.value,i);	
							});
						});
					} 
					$numbs ++;
				}
				$$('div#custom_list div.custom_item').each(function(e,i){ 
					addItem(i);
				});
			});
			function jSelectItem(id, title, object) {
				strarray = object.split('_');
				numb = strarray[3];
				if(numb){
					document.getElementById('jform_k2_article_id_'+numb).value = id;
					document.getElementById('jform_k2_article_title_'+numb).value = title;
					document.getElementById('jform_k2_article_'+numb).value = title;
				}else{
					document.getElementById('jform_k2_article_id').value = id;
					document.getElementById('jform_k2_article_title').value = title;
					document.getElementById('jform_k2_article').value = title;
				} 
				if(typeof(window.parent.SqueezeBox.close=='function')){
					window.parent.SqueezeBox.close();
				}
				else {
					document.getElementById('sbox-window').close();
				}
			}
			function jSelectArticle(id, title, object){ 
				document.getElementById('jform_content_article_id_'+$curr_open).value = id;
				document.getElementById('jform_content_article_title_'+$curr_open).value = title;
				document.getElementById('jform_content_article_'+$curr_open).value = title; 
				if(typeof(window.parent.SqueezeBox.close=='function')){
					window.parent.SqueezeBox.close();
				}
				else {
					document.getElementById('sbox-window').close();
				}
			}
			function jSelectArticleFist(id, title, object){
				document.getElementById('jform_content_article_id').value = id;
				document.getElementById('jform_content_article_title').value = title;
				document.getElementById('jform_content_article').value = title;
				if(typeof(window.parent.SqueezeBox.close=='function')){
					window.parent.SqueezeBox.close();
				}
				else {
					document.getElementById('sbox-window').close();
				}
			}
			function upgradeItem(numbs){
				var content_type = $('jform_content_type_'+numbs).value;
				var title = $('jform_text_title_'+numbs).value;
				var images = $('jform_img_path_'+numbs).value;
				var content_art_id = $('jform_content_article_id_'+numbs).value;
				var content_art_title = $('jform_content_article_title_'+numbs).value;
				var k2_art_id = $('jform_k2_article_id_'+numbs).value;
				var k2_art_title = $('jform_k2_article_title_'+numbs).value;
				var content = htmlspecialchars($('jform_add_content_'+numbs).value);
				var url = $('jform_text_link_'+numbs).value;
				if(content_type=='content'){
					var hiddendata = $('hiddendata_'+numbs).value=content_type+'[]'+title+'[]'+images+'[]'+content_art_id+'[]'+content_art_title;
				}else if(content_type=='k2'){
					var hiddendata = $('hiddendata_'+numbs).value=content_type+'[]'+title+'[]'+images+'[]'+k2_art_id+'[]'+k2_art_title;
				}else{
					var hiddendata = $('hiddendata_'+numbs).value=content_type+'[]'+title+'[]'+images+'[]'+content+'[]'+url;
				} 
				var element = $('hiddendata_'+numbs).getParent().getParent();
				 
				element.getElement('.custom_item_title').set('html',title);
				element.getElement('.type').set('html',content_type);
				element.getElement('.modal').setProperty('href', '../'+images);
				var scroller = element.getElement('.content-form-edit');
				scroller.setStyle('height', scroller.getSize().y + "px");
				new Fx.Tween(scroller, { duration: 500, property: 'height' }).start(0); 
					 
				var art_id = $('jform_params_text_data');
				var datazoro = ''; 
				$$('#custom_list input.hiddendata').each(function(item,i){
					datazoro = datazoro +  item.value  + '||';
				});
				art_id.innerHTML = datazoro; 
			}
			function cancelItem(numbs){ 
				$$('div#custom_list div.custom_item').each(function(e,i){ 
					if(e.getElement('.numbsitem').value==numbs){
						var scroller = e.getElement('.content-form-edit');
						scroller.setStyle('height', scroller.getSize().y + "px");
						new Fx.Tween(scroller, { duration: 300, property: 'height' }).start(0);
					}
				});
			}
			function htmlspecialchars(str) {
				if (typeof(str) == "string") {
					str = str.replace(/&/g, "[ztamp]");
					str = str.replace(/"/g, "[ztdquote]");
					str = str.replace(/'/g, "[ztquote]");
					str = str.replace(/</g, "[ztlt]");
					str = str.replace(/>/g, "[ztgt]");
				}
				return str;
			}
			function htmlspecialchars_decode(str) {
				if (typeof(str) == "string") {
					str = str.replace(/\[ztgt\]/g, ">");
					str = str.replace(/\[ztlt\]/g, "<");
					str = str.replace(/\[ztquote\]/g, "'");
					str = str.replace(/\[ztdquote\]/g, '"');
					str = str.replace(/\[ztamp\]/g, '&');
				}
				return str;
			}
			window.addEvent('domready', function(){			
				fireEvent('sortingready');
			});
			
			window.addEvent('sortingready', function(){ 
				$$('#custom_list .remove').addEvent('click', function(element){ 
					if(element) element.stop();
					var container = $(this).getParent().getParent();
					container.dispose();
					var allid = $$('#custom_list input.hiddendata'); 
					var art_id = $('jform_params_text_data');  
					var st_id = '';  
					for (var i = allid.length-1; i >= 0; i--) { 
						st_id = st_id + allid[i].value + '||'; 
					} 
					art_id.innerHTML = st_id; 
				});
				$$('#custom_list a.edit').each(function(element,i){
					element.addEvent('click', function(item){
						$curr_open = i; 
						var container = $(this).getParent().getParent(); 
						if(item) item.stop();
						var scroller = container.getElement('.content-form-edit'); 
						scroller.setStyle('height', scroller.getSize().y + "px");
						var fx = new Fx.Tween(scroller, { duration: 500, property: 'height', onComplete: function() { if(scroller.getSize().y > 0) scroller.setStyle('height', 'auto'); } });
						
						if(scroller.getSize().y > 0) {
							fx.start(0);
						} else {
							var items = container.getParent().getElements('.custom_item'); 
							items.each(function(element) { 
								if(element != container) element.getElements('span.custombtn')[1].fireEvent('click');  
							}); 
							fx.start(scroller.getElement('div').getSize().y); 
						}  
					});
				});
				if (typeof ZTdrapdrop != 'undefined') {
					new ZTdrapdrop(
						$$("#custom_list"),{classmove: '.move'})
				};
			});
		</script> 
		<?php return $html_return; 
	} 
}  