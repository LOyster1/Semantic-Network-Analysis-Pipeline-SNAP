<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table width="100%" border="0">
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{

			$row = &$this->rows[$i];
			?>
		<tr>
			<td width="100%" valign="top">
			<table cellpadding="4" cellspacing="1" border="0" class="adminlist">
		    	<tr>
		      		<th class="adminheading" colspan="2"><?php echo $row->id ? JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_EDIT') : JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_ADD');?></th>
		      	</tr>
		      	<tr>
		      		<td valign="top" align="left" width="100%">
		      			<table>
		  					<tr valign="top">
		    					<td><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_NAME')." "; ?></strong><br />
		    					    <?php 
                                          if ($row->locked){
                                             $dis = 'disabled="disabled"'; 	
                                          } else {
                                             $dis = '';
                                          } ?>
                                    <input name="template_name" value="<?php echo $row->template_name; ?>" <?php echo $dis; ?> size="70" maxlength="64"/>
		       					</td>
                                <td><?php if (!$dis) { echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_NAME_DESCRIPTION'); 
                                        } else { echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_NAME_DESCRIPTION').'<br />'.JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TITLE_NOT_ALLOWED_TO_CHANGE_DESK'); }?>
                                   </td>
   		  					</tr>
                            <tr valign="top">
                                <td><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_NOTE_TITLE')." "; ?></strong><br />
                                    <textarea name="note" rows="1" cols="80"><?php echo $row->note; ?></textarea>
                                   </td>
                                   <td><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_NOTE_DESC'); ?>
                                   </td>
                              </tr>
		  					<tr>
		  						<td><strong><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_TEXT')." "; ?></strong><br />
		  							<?php
                                    if ($this->editorbool['files.editor'] == "1") {
                                    	echo $this->editor->display( 'template_text',  @$row->template_text , '100%', '500', '80', '5' ) ;
                                        } else {?>
                                        <textarea name="template_text" rows="20" cols="80"><?php echo $row->template_text; ?></textarea>
                                        <?php
                                        } ?>
		  						</td>
		       					<td valign="top">
                                   <?php
                                        echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DESC').'<br /><br />'.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DESC2').'<br /><br />'.JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_DESC3');
                                   ?>
		       					</td>
		  					</tr>

                            <tr>
		      		           <th class="adminheading" colspan="2"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TITLE')." "; ?>
                               </th>
		      	            </tr>
                            <tr>
                               <td>
                               <br />
                                <div><? echo JHtml::_('image', 'administrator/components/com_jdownloads/assets/images/downloads_cats.gif', 'Templates Infos' );?></div>
                              </td>
                              <td valign="top"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_SETTINGS_TEMPLATES_CATS_INFO_TEXT');?>
                              </td>
                            </tr>

		  				</table>
		  			</td>
		  		</tr>
			</table>
			</td>
		</tr>
<?php $k = 1 - $k;  } 

echo $this->row->id; ?>		
	</table>
<br /><br />

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" value="0" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
        <input type="hidden" name="locked" value="<?php echo $row->locked; ?>" />
        <input type="hidden" name="template_typ" value="<?php echo $row->template_typ; ?>" />
        <input type="hidden" name="template_name" value="<?php echo $row->template_name; ?>" />
        <input type="hidden" name="template_active" value="<?php echo $row->template_active; ?>" />        
		<input type="hidden" name="task" value="cat" />
        <input type="hidden" name="controller" value="templates" />
        <input type="hidden" name="view" value="templates" />  
</form>