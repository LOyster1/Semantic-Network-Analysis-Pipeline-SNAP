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

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * jDownloads backup Controller
 *
 */
class jdownloadsControllerbackup extends jdownloadsController
{
	/**
	 * Constructor
	 *                                 
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * logic for create the backup file
	 *
	 */
    public function runbackup()
    {
  	    global $jlistConfig;
        
        $jinput = JFactory::getApplication()->input;
        
        $jd_version = JDownloadsHelper::getjDownloadsVersion();
        $jd_version = str_replace(' ', '_', $jd_version);
  	    $add_also_logs = $jinput->get('logs', 0, 'int');
        
        // check user access right
        if (JFactory::getUser()->authorise('com_jdownloads.edit.config','com_jdownloads'))
        {  
                
            $db = JFactory::getDBO();
  		    $prefix = JDownloadsHelper::getCorrectDBPrefix();
            JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jdownloads'.DS.'tables');
      
            if ($add_also_logs){
                $dbtables = array($prefix.'jdownloads_config', $prefix.'jdownloads_categories', $prefix.'jdownloads_files', $prefix.'jdownloads_licenses', $prefix.'jdownloads_ratings', $prefix.'jdownloads_logs', $prefix.'jdownloads_templates', $prefix.'jdownloads_usergroups_limits', $prefix.'assets');
            } else {
                // logs are not stored                
                $dbtables = array($prefix.'jdownloads_config', $prefix.'jdownloads_categories', $prefix.'jdownloads_files', $prefix.'jdownloads_licenses', $prefix.'jdownloads_ratings', $prefix.'jdownloads_templates', $prefix.'jdownloads_usergroups_limits', $prefix.'assets');    
            }    
  		    $file = '<?php'."\r\n";
  		    for ($i=0; $i < count($dbtables); $i++) {
                
                // the target db can has an other prefix, so we can not use it here
                $table_name = str_replace($prefix, '#__', $dbtables[$i]);
                // make not the Joomla asset table empty!!!
                if ($dbtables[$i] != $prefix.'assets'){  
                    $file .= '$db->setQuery("TRUNCATE TABLE `'.$table_name.'`") ;$db->execute();'."\r\n";
                } else {
                    // only remove all olders jdownloads categories and downloads from asset table
                    // but not the component root item (level=1)
                    $file .= '$db->setQuery("DELETE FROM `'.$table_name.'` WHERE `name` LIKE '.$db->quote('com_jdownloads%').' AND `level` > '.$db->quote('1').'"); $db->execute();'."\r\n";
                }    
            }    
            
            // we will backup not the assets in this version
            array_pop($dbtables);
            
  		    foreach($dbtables as $dbtable){
  			    if ($dbtable == $prefix.'jdownloads_ratings' || $dbtable == $prefix.'jdownloads_files'){
                    $db->setQuery("SELECT file_id FROM $dbtable");
                } else {    
                    $db->setQuery("SELECT id FROM $dbtable");
                }
                // alternate when we will get also the assets data (not useful in this version):
                /*} elseif ($dbtable == $prefix.'assets' ){
                    $db->setQuery("SELECT id FROM $dbtable WHERE `name` LIKE 'com_jdownloads%' AND `level` > '1'");
                } else {    
                    $db->setQuery("SELECT id FROM $dbtable");
                } */    
  			    
                $xids = $db->loadObjectList();
  			    foreach($xids as $xid){
  				    switch($dbtable){
  					    case $prefix.'jdownloads_config':
                            $object = JTable::getInstance('config', 'jdownloadsTable');
  					    break;
  					    case $prefix.'jdownloads_categories':
                            $object = JTable::getInstance('category', 'jdownloadsTable');
  					    break;
  					    case $prefix.'jdownloads_files':
                            $object = JTable::getInstance('download', 'jdownloadsTable');
  					    break;
  					    case $prefix.'jdownloads_licenses':
                            $object = JTable::getInstance('license', 'jdownloadsTable');
  					    break;
  					    case $prefix.'jdownloads_templates':
                            $object = JTable::getInstance('template', 'jdownloadsTable');
  					    break;
                        case $prefix.'jdownloads_logs':
                            $object = JTable::getInstance('log', 'jdownloadsTable');
                        break;
                        case $prefix.'jdownloads_ratings':
                            $object = JTable::getInstance('rating', 'jdownloadsTable');
                        break;
                        case $prefix.'jdownloads_usergroups_limits':
                            $object = JTable::getInstance('group', 'jdownloadsTable');
                        break;
                         case $prefix.'assets':
                            $object = JTable::getInstance('assets', 'jdownloadsTable');
                        break;
                        
                    }
      			    
                    // get the data row
                    if ($dbtable == $prefix.'jdownloads_files'){
                        $object->load($xid->file_id);
                    } elseif ($dbtable == $prefix.'jdownloads_ratings'){
                        $db->setQuery("SELECT * FROM ".$prefix.'jdownloads_ratings'." WHERE `file_id` = '$xid->file_id'");
                        $row = $db->loadObject();                        
                    } else {    
                        $object->load($xid->id);
                    }                    
       
                    // the target db can has an other prefix, so we can not use it here
                    $table_name = str_replace($prefix, '#__', $dbtable);
  				    if ($table_name != '#__jdownloads_ratings'){
                        $sql = '$db->setQuery("INSERT INTO '.$table_name.' ( %s ) VALUES ( %s );"); $db->execute();$i++; '."\r\n";
  				        $fields = array();
  				        $values = array();
  				        foreach (get_object_vars( $object ) as $k => $v) {
  					        if (is_array($v) or is_object($v) or $v === NULL) {
  						        continue;
  					        }
  					        if ($k[0] == '_') {
  						        continue;
  					        }
  					        
                            // set field name
                            $fields[] = $db->quoteName( $k );
  					        
                            // set field value (but not for ID field from assets table!!!)
                            if ($table_name == '#__assets' && $k == 'id'){
                                $values[] = "''";
                            } else {
                                // write 0 to asset id
                                if ($k == 'asset_id'){
                                    $values[] = "'0'";
                                } else {
                                    $values[] = $db->Quote( $v );    
                                }    
                            }
  				        }
  				        $file .= sprintf( $sql, implode( ",", $fields ) ,  implode( ",", $values ) );
                    } else {
                        // special handling for ratings table required, then we have here not a primary key
                        $file .= '$db->setQuery("INSERT INTO '.$table_name.' ( `file_id`,`rating_sum`,`rating_count`,`lastip` ) VALUES ( '.$db->quote($row->file_id).','.$db->quote($row->rating_sum).','.$db->quote($row->rating_count).','.$db->quote($row->last_ip).' );"); $db->execute();$i++; '."\r\n";
                    }   
  			    }
  		    }
            $date_current = JHtml::_('date', '','Y-m-d_H:i:s');
  		    $file .= "\r\n?>";
  		    header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  		    header ("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
  		    header ("Cache-Control: no-store, no-cache, must-revalidate");
            header ('Cache-Control: post-check=0, pre-check=0', false );
  		    header ("Pragma: no-cache");
  		    header ("Content-type: text/plain");
  		    header ('Content-Disposition: attachment; filename="'.'backup_jdownloads_v'.$jd_version.'_date_'.$date_current.'_.txt'.'"' );
  		    print $file;
            
        }     
  		    exit;
    }	
}
?>