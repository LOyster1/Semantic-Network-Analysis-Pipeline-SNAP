<?php

defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );


class jdownloadsModelfiles extends JModelList
{
	/**
	 * jDownloads data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * jDownloads total amount files
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;


	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';
         
    }   
     
    /**
    * Method to auto-populate the model state.
    *
    * Note. Calling getState in this method will result in recursion.
    *
    */
    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');
        
        // Load the filter state.
        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
        $this->setState('filter.search', $search);

        // Load the parameters.
        $params = JComponentHelper::getParams('com_jdownloads');
        $this->setState('params', $params);

        // List state information.
        $limit = 0;
        
        // Receive & set list options
        $default_limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'uint');
        if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array')){
            if (isset($list['limit'])){
                $limit = (int)$list['limit'];
            } else {
                $limit = $default_limit;
            }
        } else {
             $limit = $default_limit;
        }
        $this->setState('list.limit', $limit);
         
        $value = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
        $limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
        $this->setState('list.start', $limitstart);        
	}

    /**
     * Method to load files data in array
     *
     * @access    public
     * @return    array  An array of results.
     */
    public function getItems()
    {
        
        global $jlistConfig;
        
        jimport( 'joomla.filesystem.folder' );
        jimport( 'joomla.filesystem.file' );        
        
        require_once JPATH_COMPONENT.'/helpers/jdownloadshelper.php';
        jimport('joomla.html.pagination'); 
         
        $app = JFactory::getApplication('administrator');
        $mainframe = JFactory::getApplication();
        $option = 'com_jdownloads';
        
        // Lets load the file data if it doesn't already exist
       if (empty($this->_data))
       {
         // get all file names from upload root dir       
         $files_dir = $jlistConfig['files.uploaddir'].DS;
         $filenames = JFolder::files( $jlistConfig['files.uploaddir'], $filter= '.', $recurse=false, $fullpath=false, $exclude=array('index.htm', 'index.html', '.htaccess') ); 
         $files_info = array();
        
         // build data array for files list
         for ($i=0; $i < count($filenames); $i++)
         {
             $files_info[$i]['id']   = $i+1;
             $files_info[$i]['name'] = $filenames[$i];
             $date_format = JDownloadsHelper::getDateFormat();
             $files_info[$i]['date'] = date($date_format['long'], filemtime($files_dir.$filenames[$i]));               
             $files_info[$i]['size'] = JDownloadsHelper::fsize($files_dir.$filenames[$i]);    
         }
         
         // search in file names
         $search = $this->getState('filter.search');
         if ($search)
         {
             $search_result = JDownloadsHelper::arrayRegexSearch( '/'.$search.'/i', $files_info, TRUE, TRUE ); 
             foreach ($search_result as $result){
                $files_info_result[] = $files_info[$result]; 
             }
             $files_info = $files_info_result;   
         }  

         // build pagination data
         $limitstart = $this->getState('list.start');
         $limit      = $this->getState('list.limit');
         $pageNav = new JPagination( count($files_info), $limitstart, $limit );
         $this->_pagination = $pageNav;
         
         $items = array_splice ( $files_info, $limitstart, $limit );
         $this->_data = $items; 
        }
        return $this->_data;
    }
    
     /**
     * Method to set the pagination value
     *
     * @access    public
     * @return    boolean    True on success
     */
    
    public function getPagination()
    {
        return $this->_pagination;
    }	
  
	/**
	 * Method to delete a not assigned file from the uload root folder
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function delete($cid = array())
	{
		$result = false;

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

        
        
        
        }

		return true;
	}  	

}
