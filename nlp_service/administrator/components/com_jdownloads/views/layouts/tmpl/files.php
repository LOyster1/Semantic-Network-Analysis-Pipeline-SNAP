<form action="index.php" method="post" name="adminForm">
	<table cellpadding="4" cellspacing="0" border="0" width="100%" class="adminlist">
	  <tr align="right">
		  <td colspan="7"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_LOCKED_DESC'); ?></td>
	  </tr>
		<tr>
			<th width="5" align="left"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(<?php echo count( $rows ); ?>);" /></th>
			<th class="title"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_TITLE')." "; ?></th>
			<th class="title"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_TYP')." "; ?></th>
			<th class="title"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_ACTIVE')." "; ?></th>
			<th class="title"><?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPLIST_LOCKED')." "; ?></th>
        </tr>
		<?php
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
			$row = &$this->rows[$i];
			$link 		= 'index.php?option=com_jdownloads&task=templates.edit.files&hidemainmenu=1&cid='.$row->id;
			$checked 	= JHtml::_('grid.checkedout', $row, $i );
			?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $checked; ?></td>
			<td><a href="<?php echo $link; ?>" title="<?php echo JText::_('COM_JDOWNLOADS_BACKEND_TEMPEDIT_EDIT');?>"><?php echo $row->template_name; ?></a></td>

            <td>
            <?php
            echo $temp_typ[$row->template_typ -1]; ?>
            </td>

            <td>
            <?php
            if ($row->template_active > 0) { ?>
                <img src="components/com_jdownloads/images/active.png" width="12px" height="12px" align="middle" border="0"/>
            <?php } ?>

            </td>
            
            <td>
            <?php
            if ($row->locked > 0) { ?>
                <img src="components/com_jdownloads/images/active.png" width="12px" height="12px" align="middle" border="0"/>
            <?php } ?>
            </td>

            <?php $k = 1 - $k;  } ?>
		</tr>
	</table>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hidemainmenu" value="0" />
</form>