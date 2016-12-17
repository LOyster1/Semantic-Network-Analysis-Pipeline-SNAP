<?php 
/**
 * @package jDownloads
 * @version 2.5  
 * @copyright (C) 2007 - 2014 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined('_JEXEC') or die('Restricted access'); 

global $jlistConfig;

JHtml::_('behavior.tooltip');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$app         = JFactory::getApplication();
$user        = JFactory::getUser();
$userId      = $user->get('id');

$listOrder   = $this->escape($this->state->get('list.ordering'));
$listDirn    = $this->escape($this->state->get('list.direction'));
$canOrder    = $user->authorise('core.edit.state', 'com_jdownloads');
$saveOrder   = $listOrder == 'a.ordering';
$images_folder = JURI::root().'administrator/components/com_jdownloads/assets/images'; 

?>
<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=downloads');?>" method="POST" name="adminForm" id="adminForm">

    <?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif;?>

    <fieldset id="jdfilter-bar">
        <div class="filter-search jdfltlft">
            <!--<label class="filter-search-lbl jdfltlft" for="filter_search"><?php echo JText::_(''); ?></label>-->
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            <button type="submit"><?php echo JText::_('COM_JDOWNLOADS_FILTER_SUBMIT'); ?></button>
            <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('COM_JDOWNLOADS_FILTER_CLEAR'); ?></button>
        </div>
        <div class="filter-select jdfltrt">
            <select name="filter_published" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_STATUS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0, 'trash' => 0)), 'value', 'text', $this->state->get('filter.published'), true);?>
            </select>
            
            <?php
                // display category list box 
                echo JHtml::_('select.genericlist', $this->featured_option, 'filter_featured', 'class="inputbox" onchange="this.form.submit()"', 'value', 'text',  $this->state->get('filter.featured')); 
            ?>
                        
            <?php
                // display category list box 
                echo JHtml::_('select.genericlist', $this->categories, 'filter_category_id', 'class="inputbox" onchange="this.form.submit()"', 'value', 'text',  $this->state->get('filter.category_id')); 
            ?>
            
            <select name="filter_access" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_ACCESS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
            </select>            
                    
            <select name="filter_language" class="inputbox" onchange="this.form.submit()">
                <option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_LANGUAGE');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
            </select>
            
            <?php 
            // we must use a little trick to get the right input name and id
            $dummy = $this->pagination->getLimitBox(); 
            $limit_box = str_replace('id="limit"', 'id="list_limit"', $dummy);
            $limit_box = str_replace('name="limit"', 'name="list[limit]"', $limit_box);
            echo $limit_box; 
            ?>         
            
        </div>
    </fieldset>
    <div class="clr"> </div>

    <div id="editcell">                                             
    <table class="adminlist">
    <thead>
        <tr>
            <th width="5" align="left">
                <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /> 
            </th>
            
            <th class="title" align="left">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_TITLE', 'a.file_title', $listDirn, $listOrder ); ?>
            </th>
			<th width="5%">
				<?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_RELEASE', 'a.release', $listDirn, $listOrder ); ?>
			</th> 
			<th width="5%">
				<?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_PIC', 'a.file_pic', $listDirn, $listOrder ); ?>
			</th> 
			<th width="10%">
				<?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_CAT', 'category_title', $listDirn, $listOrder ); ?>
			</th> 
			<th width="5%">
				<?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_DESCRIPTION', 'a.description', $listDirn, $listOrder ); ?>
			</th> 
			<th width="10%">
				<?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_FILENAME', 'a.url_download', $listDirn, $listOrder ); ?>
			</th> 
			<th width="5%">
				<?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_DADDED', 'a.date_added', $listDirn, $listOrder ); ?>
			</th>    
			<th width="5%">
				<?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_HITS', 'a.downloads', $listDirn, $listOrder ); ?>
			</th>                                                  			
            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_STATUS', 'a.published', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">                
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_FEATURED', 'a.featured', $listDirn, $listOrder); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                <?php if ($canOrder && $saveOrder) :?>
                     <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'downloads.saveorder'); ?>
                <?php endif; ?>
            </th>
            <th width="10%">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_ACCESS', 'a.access', $listDirn, $listOrder ); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_LANGUAGE', 'a.language', $listDirn, $listOrder ); ?>
            </th>
            <th width="1%" class="nowrap">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_ID', 'a.file_id', $listDirn, $listOrder ); ?>
            </th>            
        </tr>    
    </thead>
        <tfoot>
            <tr>
                <td colspan="15">
                    <?php echo '<br />'.$this->pagination->getListFooter(); ?>
                </td>
            </tr>
            <tr>
              <td align="center" colspan="15">
              <?php echo '<img src="'.$images_folder.'/tick.png" width="16" height="16" border="0" alt="" /> '.JText::_('COM_JDOWNLOADS_PUBLISHED').'&nbsp;&nbsp;|&nbsp;&nbsp;'.
                         '<img src="'.$images_folder.'/publish_y.png" width="16" height="16" border="0" alt="" /> '.JText::_('COM_JDOWNLOADS_BACKEND_FILESLIST_TOOLTIP_INFO').'&nbsp;&nbsp;|&nbsp;&nbsp;'.
                         '<img src="'.$images_folder.'/publish_x.png" width="16" height="16" border="0" alt="" /> '.JText::_('COM_JDOWNLOADS_UNPUBLISHED'); 
              ?>   
              </td>
            </tr>            
        </tfoot>
     <tbody>             
        <?php 
            foreach ($this->items as $i => $item) {
                
                $ordering     = ($listOrder == 'a.ordering');
                $link         = JRoute::_( 'index.php?option=com_jdownloads&task=download.edit&file_id='.(int) $item->file_id );
                $canCheckin   = $user->authorise('core.manage',     'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
                $canChange    = $user->authorise('core.edit.state', 'com_jdownloads') && $canCheckin;
                $canCreate    = $user->authorise('core.create',     'com_jdownloads');
                $canEdit      = $user->authorise('core.edit',       'com_jdownloads');
            ?>
            <tr class="row<?php echo $i % 2; ?>">
                
                <td class="center">
                    <?php echo JHtml::_('grid.id', $i, $item->file_id); ?>
                </td>
                
                <td>
                <?php if ($item->checked_out) : ?>
                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'downloads.', $canCheckin); ?>
                <?php endif; ?>
                <?php if ($canEdit) : ?>
                    <a href="<?php echo $link; ?>">
                        <?php echo $this->escape($item->file_title); ?></a>
                <?php else : ?>
                        <?php echo $this->escape($item->file_title); ?>
                <?php endif; ?>
                <p class="smallsub"> 
                    <?php if (empty($item->notes)) : 
                              echo JText::sprintf('COM_JDOWNLOADS_LIST_ALIAS', $this->escape($item->file_alias));
                          else : 
                              echo JText::sprintf('COM_JDOWNLOADS_LIST_ALIAS_NOTE', $this->escape($item->file_alias), $this->escape($item->notes));
                          endif; ?>
                </p>            
                </td>         

			    <td class="center"><?php echo $this->escape($item->release); ?>
                </td>

                <td class="center">
                    <?php if ($item->file_pic != '') { ?>
                        <img src="<?php echo JURI::root().JRoute::_( "images/jdownloads/fileimages/$item->file_pic" ); ?>" width="32px" height="32px" align="middle" border="0"/>
                    <?php } ?>
                </td>            

                <td align="center">
                    <?php 
                        // show only, when a category is selected - not when 'Uncategorised' is selected ('ROOT' is not useful)
                        if ($item->category_title == 'ROOT'){
                            echo JText::_('COM_JDOWNLOADS_UNCATEGORISED');
                        } else {
                            if ($item->category_title_parent != 'ROOT'){
                                echo JHtml::_('tooltip', strip_tags($item->category_title_parent), JText::_('COM_JDOWNLOADS_PARENT_CATEGORY_LABEL'), '', $this->escape($item->category_title)); 
                            } else {
                                echo $this->escape($item->category_title);
                            }   
                        }    
                    ?>
                </td>

                <td align="center">
                <?php
                    if (strlen($item->description) > 200 ) {
                        $description_short = $this->escape(strip_tags(substr($item->description, 0, 200).' ...'));
                    } else {
                        $description_short = $this->escape(strip_tags($item->description));
                    }
                    if ($description_short != '') {
                        echo JHtml::_('tooltip', $description_short, JText::_('COM_JDOWNLOADS_BACKEND_FILESLIST_DESCRIPTION_SHORT'), JURI::root().'administrator/components/com_jdownloads/assets/images/tooltip_blue.gif'); 
                    }
                ?>
                </td>
                
                <td align="center">
                <?php
                 if ($item->url_download !=''){
                          echo JHtml::_('tooltip',strip_tags($item->url_download),  JText::_('COM_JDOWNLOADS_BACKEND_FILESLIST_FILENAME'), JURI::root().'administrator/components/com_jdownloads/assets/images/file_blue.gif'); 
                } elseif ($item->extern_file != ''){
                          echo JHtml::_('tooltip',strip_tags($item->extern_file), JText::_('COM_JDOWNLOADS_BACKEND_FILE_EDIT_EXT_DOWNLOAD_TITLE'), JURI::root().'administrator/components/com_jdownloads/assets/images/external_orange.gif'); 
                } elseif ($item->other_file_id > 0){
                          echo JHtml::_('tooltip',strip_tags(JText::sprintf('COM_JDOWNLOADS_BACKEND_FILESLIST_OTHER_DOWNLOADS_FILE_NAME', $item->other_file_name)), JText::sprintf('COM_JDOWNLOADS_BACKEND_FILESLIST_OTHER_DOWNLOADS_FILE_USED', $item->other_download_title), JURI::root().'administrator/components/com_jdownloads/assets/images/file_orange.gif'); 
                } else {
                          // only a document without any files     
                          echo JHtml::_('tooltip',strip_tags(JText::_('COM_JDOWNLOADS_DOCUMENT_DESC1')), JText::_('COM_JDOWNLOADS_BACKEND_TEMPPANEL_TABTEXT_INFO'), JURI::root().'administrator/components/com_jdownloads/assets/images/tooltip_red.gif'); 
                }
                ?>         
                </td>
                
                <td align="center"><?php echo JHtml::_('date',$item->date_added, JText::_('DATE_FORMAT_LC4')); ?>
                </td>
                
                <td align="center"><?php echo JDownloadsHelper::strToNumber((int)$item->downloads);?>
                </td> 
                
                <td class="center">
                    <?php 
                    echo JHtml::_('jgrid.published', $item->published, $i, 'downloads.', $canChange, 'cb', $item->publish_from, $item->publish_to); ?>
                </td>
                <td class="center">
                    <?php 
                    echo JDownloadsHelper::getFeatureHTML($item->featured, $i, $canChange); ?>            
                </td>                
                
                <td class="order">
                    <?php if ($canChange) : ?>
                        <?php if ($saveOrder) :?>
                            <?php if ($listDirn == 'asc') : ?>
                                <span><?php echo $this->pagination->orderUpIcon($i, ($item->cat_id == @$this->items[$i-1]->cat_id), 'downloads.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->cat_id == @$this->items[$i+1]->cat_id), 'downloads.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php elseif ($listDirn == 'desc') : ?>
                                <span><?php echo $this->pagination->orderUpIcon($i, ($item->cat_id == @$this->items[$i-1]->cat_id), 'downloads.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                                <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->cat_id == @$this->items[$i+1]->cat_id), 'downloads.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                        <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                    <?php else : ?>
                        <?php echo $item->ordering; ?>
                    <?php endif; ?>
                </td>
                
                <td class="center">
                    <?php echo 
                    $this->escape($item->access_level); ?>
                </td>
                
                <td class="center nowrap">
                    <?php if ($item->language=='*'):?>
                        <?php echo JText::alt('JALL','language'); ?>
                    <?php else:?>
                        <?php echo $item->language ? $this->escape($item->language) : JText::_('COM_JDOWNLOADS_UNDEFINED'); ?>
                    <?php endif;?>
                </td>
                
                <td>
                    <?php echo (int) $item->file_id; ?>
                </td>                                                                                       

         </tr>
    <?php } ?>

    </tbody>
	</table>
    
    <?php //Load the batch processing form. ?>
    <?php echo 
    $this->loadTemplate('batch'); ?>
        
</div>

<input type="hidden" name="option" value="com_jdownloads" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="hidemainmenu" value="0">
<input type="hidden" name="view" value="downloads" />
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />        
<?php echo JHtml::_('form.token'); ?>    
</form>
