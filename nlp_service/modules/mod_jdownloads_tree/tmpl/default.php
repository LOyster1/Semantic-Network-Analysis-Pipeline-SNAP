<?php

    // For Tooltip
    JHTML::_('bootstrap.tooltip');
    
    // Creating new tree object
    $tree = new jdownloadstree();    
       foreach ($rows as $row) {  // let's append categories & sub categories to the tree
           if(strlen($row->title) > $lengthc){
              $row->title  = substr($row->title,0,($lengthc - 3));
              $row->title .= "...";
           }                                      
        
           if ($row->menu_itemid){  
               $Itemid = $row->menu_itemid;
           } else {
               $Itemid = $root_itemid;
           }           
           
           if ($row->link == '-'){
               // user has the permissions to view the category
               $catlink = JRoute::_('index.php?option=com_jdownloads&amp;view=category&amp;catid='. $row->id .'&amp;Itemid='. $Itemid);
           } else {
               // link to the login page
               $catlink = $row->link;
           }
           $tree->addToArray($row->id, $row->title, $row->parent_id, $catlink, '', $row->numitems, $row->subcatitems);

           if($row->id > $nodeId){
               $nodeId = $row->id;
           } // get max id
       }
       
       $nodeId++;
         
       // draw the tree
       $livesite = JURI::root();
       $tree->writeJavascript($livesite);
       $tree->drawTree($home_link, $moduleclass_sfx, $params);
       $tree->applyStyle();
?>