<?php
/**
 * @package jDownloads
 * @version 2.5  
 * @copyright (C) 2007 - 2013 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

// no direct access
defined('_JEXEC') or die;

if (JFactory::getApplication()->isSite()) {
	JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));
}

require_once JPATH_ROOT . '/components/com_jdownloads/helpers/route.php';

JHTML::_('bootstrap.tooltip');
JHtml::_('behavior.framework', true);
JHtml::_('formbehavior.chosen', 'select');

$jinput = JFactory::getApplication()->input;

$function	= $jinput->get('function', 'jSelectDownload');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>
<form action="<?php echo JRoute::_('index.php?option=com_jdownloads&view=list&layout=modallist&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1');?>" method="post" name="adminForm" id="adminForm">
	<div class="left">
        <b><?php // echo JText::_('COM_JDOWNLOADS_JD_MENU_VIEWDOWNLOAD_LABEL2') ?></b>
    </div>
    
    <fieldset class="filter clearfix">
		<div class="left">
			<label for="filter_search">
				<?php echo JText::_('COM_JDOWNLOADS_FILTER_LABEL'); ?>
			</label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" size="30" title="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_FILESLIST_SEARCH_DESC'); ?>" />

			<button type="submit">
				<?php echo JText::_('COM_JDOWNLOADS_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('COM_JDOWNLOADS_FILTER_CLEAR'); ?></button>
		</div>

		<div class="right">
			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_STATUS');?></option>
                <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => 0, 'trash' => 0)), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
            
            <?php
                // display category list box 
                echo JHtml::_('select.genericlist', $this->categories, 'filter_category_id', 'class="inputbox" onchange="this.form.submit()"', 'value', 'text',  $this->state->get('filter.category_id')); 
            ?>
                        

			<!-- <select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
            -->
            
			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JDOWNLOADS_SELECT_LANGUAGE');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
            <th class="title">
                <?php echo JHtml::_('grid.sort', 'COM_JDOWNLOADS_TITLE', 'a.file_title', $listDirn, $listOrder ); ?>
            </th>
            <th width="5%">
                <?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_RELEASE', 'a.release', $listDirn, $listOrder ); ?>
            </th> 
            <th width="10%">
                <?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_CAT', 'c.cat_title', $listDirn, $listOrder ); ?>
            </th> 
            <th width="10%">
                <?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_FILENAME', 'a.url_download', $listDirn, $listOrder ); ?>
            </th> 
            <th width="5%">
                <?php echo JHtml::_('grid.sort',   'COM_JDOWNLOADS_BACKEND_FILESLIST_DADDED', 'a.date_added', $listDirn, $listOrder ); ?>
            </th>    
            <th width="5%">
                <?php echo JHtml::_('grid.sort',  'COM_JDOWNLOADS_STATUS', 'a.published', $listDirn, $listOrder); ?>
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
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php if ($item->language && JLanguageMultilang::isEnabled()) {
				$tag = strlen($item->language);
				if ($tag == 5) {
					$lang = substr($item->language, 0, 2);
				}
				elseif ($tag == 6) {
					$lang = substr($item->language, 0, 3);
				}
				else {
					$lang = "";
				}
			}
			elseif (!JLanguageMultilang::isEnabled()) {
				$lang = "";
			}
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a href="javascript:void(0)" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->file_id; ?>', '<?php echo $this->escape(addslashes($item->file_title)); ?>', '<?php echo $this->escape($item->cat_id); ?>', null, '<?php echo $this->escape(JdownloadsHelperRoute::getDownloadRoute($item->file_id, $item->cat_id, $item->language)); ?>', '<?php echo $this->escape($lang); ?>', null);">
						<?php echo $this->escape($item->file_title); ?></a>
				</td>
                <td class="center"><?php echo $this->escape($item->release); ?>
                </td>

				<td class="center">
                    <?php 
                        // show only, when a category is selected - not when 'Uncategorised' is selected ('ROOT' is not useful)
                        if ($item->category_title == 'ROOT'){
                            echo JText::_('COM_JDOWNLOADS_UNCATEGORISED');
                        } else {
                            echo $this->escape($item->category_title);                                     
                        }    
                    ?>
				</td>
                
                <td align="center">
                <?php
                 if ($item->url_download !=''){
                          echo JHtml::_('tooltip',strip_tags($item->url_download),  JText::_('COM_JDOWNLOADS_BACKEND_FILESLIST_FILENAME'), JURI::root().'administrator/components/com_jdownloads/assets/images/file_blue.gif'); 
                } elseif ($item->extern_file != ''){
                          echo JHtml::_('tooltip',strip_tags($item->extern_file), '', JURI::root().'administrator/components/com_jdownloads/assets/images/external_orange.gif'); 
                } elseif ($item->other_file_id > 0){
                          echo JHtml::_('tooltip',strip_tags(JText::sprintf('COM_JDOWNLOADS_BACKEND_FILESLIST_OTHER_DOWNLOADS_FILE_NAME', $item->other_file_name)), JText::sprintf('COM_JDOWNLOADS_BACKEND_FILESLIST_OTHER_DOWNLOADS_FILE_USED', $item->other_download_title), JURI::root().'administrator/components/com_jdownloads/assets/images/file_orange.gif'); 
                } else {
                          // only a document without any files     
                          echo JHtml::_('tooltip',strip_tags(JText::_('COM_JDOWNLOADS_DOCUMENT_DESC1')), JText::_('COM_JDOWNLOADS_BACKEND_TEMPPANEL_TABTEXT_INFO'), JURI::root().'administrator/components/com_jdownloads/assets/images/tooltip_blue.gif'); 
                }
                ?>         
                </td>               

				<td class="center nowrap">
					<?php echo JHtml::_('date', $item->date_added, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
                
                <td class="center">
                    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'downloads.', false, 'cb', $item->publish_from, $item->publish_to); ?>
                </td>            

                <td class="center">
                    <?php echo $this->escape($item->access_level); ?>
                </td>

                <td class="center nowrap">
                    <?php if ($item->language=='*'):?>
                        <?php echo JText::alt('JALL','language'); ?>
                    <?php else:?>
                        <?php echo $item->language ? $this->escape($item->language) : JText::_('COM_JDOWNLOADS_UNDEFINED'); ?>
                    <?php endif;?>
                </td>                
                
				<td class="center">
					<?php echo (int) $item->file_id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
