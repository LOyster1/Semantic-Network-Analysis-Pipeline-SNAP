<?php
/**
 * @package jDownloads
 * @version 2.0  
 * @copyright (C) 2007 - 2012 - Arno Betz - www.jdownloads.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * 
 * jDownloads is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

class jdownloadsModelupload extends JModelLegacy
{

	function __construct()
	{
		parent::__construct();
		
	}
	
	function upload($fieldName)
	{

       global $jlistConfig;

       jimport('joomla.filesystem.file');
       jimport('joomla.filesystem.folder');
     
       
      /* $POST_MAX_SIZE = ini_get('post_max_size');
        $unit = strtoupper(substr($POST_MAX_SIZE, -1));
        $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

        if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
            header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
            echo "POST exceeded maximum allowed size.";
            exit(0);
        }*/
        
  /*     $fileError = $_FILES[$fieldName]['error'];
       if ($fileError > 0) {
            switch ($fileError){
                case 1:
                echo JText::_( 'FILE TO LARGE THAN PHP INI ALLOWS' );
                return;
                case 2:
                echo  JText::_( 'FILE TO LARGE THAN HTML FORM ALLOWS' );
                return;
                case 3:
                echo  JText::_( 'ERROR PARTIAL UPLOAD' );
                return;
                case 4:
                echo  JText::_( 'ERROR NO FILE' );
                return;
            }
       }
     
       //check for filesize
   /*    $fileSize = $_FILES[$fieldName]['size'];
       $limit = JDownloadsHelper::return_bytes(ini_get('upload_max_filesize'));
       if ($fileSize >  $limit){
            echo JText::_( 'FILE BIGGER THAN ALLOWED' );
       } 
     
        //check the file extension is ok */
        $fileName = $_FILES[$fieldName]['name'];
/*        $uploadedFileNameParts = explode('.',$fileName);
        $uploadedFileExtension = array_pop($uploadedFileNameParts);
        $invalidFileExts = explode(',', 'php,php4,php5,php6,html,htm');
        $valid_ext = true;
        foreach($invalidFileExts as $key => $value){
            if( preg_match("/$value/i", $uploadedFileExtension )){
                $valid_ext = false;
            }
        }
        if ($valid_ext == false){
            HandleUploadError(JText::_( 'INVALID extension type!' ));
            exit(0);
        }
     
        // replace special chars in filename?
        $filename_new = JDownloadsHelper::checkFileName($fileName);
        // rename new file when it exists in this folder
        $only_name = substr($filename_new, 0, strrpos($filename_new, '.'));
        if ($only_name != ''){
            // filename is valid
            $file_extension = strrchr($filename_new,".");
            $num = 0;
            while (is_file($jlistConfig['files.uploaddir'].DS.$filename_new)){
                  $filename_new = $only_name.$num++.$file_extension;
                  if ($num > 5000) exit(0); 
            }
            $fileName = $filename_new; 
        } else {
            echo JText::_('COM_JDOWNLOADS_BACKEND_FILESEDIT_INVALID_FILENAME');
            exit(0);
        }    
     */
   /*     $fileTemp = $_FILES[$fieldName]['tmp_name'];
        //$uploadPath = $jlistConfig['files.uploaddir'].DS.$fileName;
        $uploadPath = JPATH_SITE.'/images/stories/'.$fileName;
        
        if(!JFile::upload($fileTemp, $uploadPath)){
            echo JText::_( 'ERROR MOVING FILE' );
            return;
        } else {
            exit(0);
        }

    } */ 

		//this is the name of the field in the html form, filedata is the default name for swfupload
		//so we will leave it as that
		$fieldName = 'Filedata';
 
		//any errors the server registered on uploading
	/*	$fileError = $_FILES[$fieldName]['error'];
		if ($fileError > 0) 
		{
			switch ($fileError) 
			{
			case 1:
			echo JText::_( 'COM_SWFUPLOAD_UPLOAD_FILE_LARGER_THAN_PHP_ALLOWS' );
			return;
 
			case 2:
			echo JText::_( 'COM_SWFUPLOAD_UPLOAD_FILE_LARGER_THAN_HTML_FORM_ALLOWS' );
			return;
 
			case 3:
			echo JText::_( 'COM_SWFUPLOAD_UPLOAD_ERROR_PARTIAL_UPLOAD' );
			return;
 
			case 4:
			echo JText::_( 'COM_SWFUPLOAD_UPLOAD_ERROR_NO_FILE' );
			return;
			}
		}
/*		
		$params = JComponentHelper::getParams('com_swfupload');
 
		$fileSize = $_FILES[$fieldName]['size'];
		if($fileSize > ($params->get('maxfilesize', 2)*1000000))
		{
			echo JText::_( 'COM_SWFUPLOAD_UPLOAD_FILE_BIGGER_THAN_MB' );
			return;
		}

		$fileName = $_FILES[$fieldName]['name'];
		$filename = JFile::makeSafe($filename);
		$uploadedFileNameParts = explode('.',$fileName);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);

		if($uploadedFileExtension=='php') {
			echo JText::_( 'COM_SWFUPLOAD_UPLOAD_PHP_EXTENSION' );
			return;
		}
		$validFileExts = explode(',', $params->get('allowedfiles', 'gz,zip,rar,bmp,csv,doc,docx,gif,ico,jpg,jpeg,odg,odp,ods,odt,pdf,png,ppt,pptx,swf,txt,xcf,xls,xlsx,BMP,CSV,DOC,GIF,ICO,JPG,JPEG,ODG,ODP,ODS,ODT,PDF,PNG,PPT,SWF,TXT,XCF,XLS,DOCX,XLSX,PPTX'));
 
		$extOk = false;
 
		foreach($validFileExts as $key => $value)
		{
			if( preg_match("/$value/i", $uploadedFileExtension ) )
			{
				$extOk = true;
			}
		}
 
		if ($extOk == false) 
		{
			echo JText::_( 'COM_SWFUPLOAD_UPLOAD_INVALID_EXTENSION' );
			return;
		}
 
		$fileTemp = $_FILES[$fieldName]['tmp_name'];
		
		// remove invalid chars
		$file_extension = strtolower(substr(strrchr($filename,"."),1));
		$name_cleared = preg_replace("#[^A-Za-z0-9 _.-]#", "", $fileName);
		if ($name_cleared != $file_extension){
			$fileName = $name_cleared;
		}
  */							
		$fileTemp = $_FILES[$fieldName]['tmp_name'];
        $uploadPath  = JPATH_SITE.'/jdownloads/'.$fileName ;
 
		if(!JFile::upload($fileTemp, $uploadPath, false, true)) 
		{
			echo JText::_( 'COM_JDOWNLOADS_UPLOAD_ERROR_MOVING_FILE' );
			return;
		}
		else
		{
			exit(0);
		}
	}
}
?>