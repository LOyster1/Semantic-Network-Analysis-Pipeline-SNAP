<?php

/**
* @package jDMTree 1.5 - Docman jDMTree Module 1.5.5
* @copyright (C) 2008-2010 http://www.youthpole.com
* @author Josh Prakash
*
* ----------------------------------------------------------------------------------------------------
* All rights reserved.  Docman tree module for Joomla!
* Other credits & copyrights:
* dhtml tree structure base : http://www.dhtmlgoodies.com
*	cookie functions : http://www.mach5.com/support/analyzer/manual/html
*	                   /General/CookiesJavaScript.htm
*
* @license		GNU/GPL, see LICENSE.php
* This module is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* Revisions
* Nov-11-2008 - php5 compatibility fix for <? - jdmtree 1.5.1
* May-13-2009 - SEO url compatability/invisible icons & minor css fixes - jdmtree 1.5.2
* Jul-21-2009 - Category selection display - jdmtree 1.5.3 [no changes to this file]
* Nov-03-2009 - Docman groups (user defined) handling upgrade - jdmtree 1.5.4 [no changes to this file]
* Feb-26-2010 - Two additional parameter options - 1. Show/Hide View Category Link 2. Folder Link behavior selection - jdmtree 1.5.5
* -----------------------------------------------------------------------------------------------------
* Modified for jDownloads 3.2 by Arno Betz
* 
*/
// no direct access
defined('_JEXEC') or die('Restricted access');


class jdownloadstree{
  
	var $elementArray = array();
	var $nameOfCookie = "jdtree_expanded"; // Name of the cookie where the expanded nodes are stored.
	
	function applyStyle()
	{
	?>
	<style type="text/css">
    .j7dctree li {
	    list-style-type:none;	
        background:none;
	    font-family: arial;
	    font-size:12px;
	}

    .j7dctree img {
	    padding-top:-1px;
        vertical-align: middle;
	}

    .j7dctree div ul li a {
	    text-decoration:none;
    } 

    .j7dctree {
	    margin-left:0px;
	    padding-left:0px;
	}

    .j7dctree ul {
	    margin-left:12px;
	    padding-left:0px;
	    display:none;
	}
 
    .j7dctree_link {
	    line-height:13px;
	    padding-left:1px;
	}

    .j7dctree_node{
	    text-decoration:none;
	}

    .jdmroothov {
	    text-decoration:none;
    } 

    .jdmcredits {
	    font-size:8px;
	    padding-left:30px;
    }
    .jdmnumber {
        /*font-size:9px;*/
        padding-left:2px;
        padding-right:2px;
    }
     
	</style>		
	<?php		
	}


	function writeJavascript($livesite)
	{
		?>
		<script type="text/javascript">
		/*
		(c) jDMTree 1.5.5 for Joomla 1.5.x - Docman Tree Module, Josh Prakash, http://www.youthpole.com - 2010
		*/		
		var plusNode  = 'modules/mod_jdownloads_tree/jdtree/images/plus.gif';
		var minusNode = 'modules/mod_jdownloads_tree/jdtree/images/minus.gif';

		var nameOfCookie = '<?php echo $this->nameOfCookie; ?>';
		<?php
		$cookieValue = "";
		if(isset($_COOKIE[$this->nameOfCookie]))$cookieValue = $_COOKIE[$this->nameOfCookie];		
		echo "var initExpandedNodes =\"".$cookieValue."\";\n";
		?>		
		function Get_Cookie(name) { 
		   var start = document.cookie.indexOf(name+"="); 
		   var len = start+name.length+1; 
		   if ((!start) && (name != document.cookie.substring(0,name.length))) return null; 
		   if (start == -1) return null; 
		   var end = document.cookie.indexOf(";",len); 
		   if (end == -1) end = document.cookie.length; 
		   return unescape(document.cookie.substring(len,end)); 
		} 
		// This function has been slightly modified
		function Set_Cookie(name,value,expires,path,domain,secure) { 
			expires = expires * 60*60*24*1000;
			var today = new Date();
			var expires_date = new Date( today.getTime() + (expires) );
		    var cookieString = name + "=" +escape(value) + 
		       ( (expires) ? ";expires=" + expires_date.toGMTString() : "") + 
		       ( (path) ? ";path=" + path : "") + 
		       ( (domain) ? ";domain=" + domain : "") + 
		       ( (secure) ? ";secure" : ""); 
		    document.cookie = cookieString; 
		} 
		/*
		End cookie functions
		*/
		
		function expandNode(e,inputNode)
		{
			if(initExpandedNodes.length==0)initExpandedNodes=",";
			if(!inputNode)inputNode = this; 
			if(inputNode.tagName.toLowerCase()!='img')inputNode = inputNode.parentNode.getElementsByTagName('IMG')[0];	
			
			var inputId = inputNode.id.replace(/[^\d]/g,'');			
			
			var parentUl = inputNode.parentNode;
			var subUl = parentUl.getElementsByTagName('UL');

			if(subUl.length==0)return;
			if(subUl[0].style.display=='' || subUl[0].style.display=='none'){
				subUl[0].style.display = 'block';
				inputNode.src = '<?php echo $livesite; ?>'+minusNode;
				initExpandedNodes = initExpandedNodes.replace(',' + inputId+',',',');
				initExpandedNodes = initExpandedNodes + inputId + ',';
				
			}else{
				subUl[0].style.display = '';
				inputNode.src = '<?php echo $livesite; ?>'+plusNode;	
				initExpandedNodes = initExpandedNodes.replace(','+inputId+',',',');			
			}
			Set_Cookie(nameOfCookie,initExpandedNodes,60);
			
		}
		
		function initTree()
		{
			// Assigning mouse events
			var parentNode = document.getElementById('jdtree');
			var lis = parentNode.getElementsByTagName('LI'); // Get reference to all the images in the tree
			for(var no=0;no<lis.length;no++){
				var subNodes = lis[no].getElementsByTagName('UL');
				if(subNodes.length>0){
					lis[no].childNodes[0].style.visibility='visible';	
				}else{
					lis[no].childNodes[0].style.visibility='hidden';
				}
			}	
			
			var images = parentNode.getElementsByTagName('IMG');
			for(var no=0;no<images.length;no++){
				if(images[no].className=='tree_plusminus')images[no].onclick = expandNode;				
			}	

			var aTags = parentNode.getElementsByTagName('A');
			var cursor = 'pointer';
			if(document.all)cursor = 'hand';
			for(var no=0;no<aTags.length;no++){
				aTags[no].onclick = expandNode;		
				aTags[no].style.cursor = cursor;		
			}
			var initExpandedArray = initExpandedNodes.split(',');

			for(var no=0;no<initExpandedArray.length;no++){
				if(document.getElementById('plusMinus' + initExpandedArray[no])){
					var obj = document.getElementById('plusMinus' + initExpandedArray[no]);	
					expandNode(false,obj);
				}
			}				
		}
		
		window.onload = initTree;
		
		</script>	
		<?php
		
	}
	
	/**
    * This function adds elements to the array
    * 
    * @param mixed $id
    * @param mixed $name
    * @param mixed $parentID
    * @param mixed $url
    * @param mixed $target
    * @param mixed $numitems
    * @param mixed $subcatitems
    * @param mixed $imageIcon
    */
	
	function addToArray($id, $name, $parentID, $url = "", $target = "", $numitems = 0, $subcatitems = 0, $imageIcon="modules/mod_jdownloads_tree/jdtree/images/folder.gif"){
		if(empty($parentID) || $parentID == 'root'){
           $parentID = 0;      
        } 
		
        $this->elementArray[$parentID][] = array($id, $name, $url, $target, $imageIcon, $numitems, $subcatitems);
	}
	
	function drawSubNode($parentID, $params){
		$catid = intval(JArrayHelper::getValue($_REQUEST, 'catid', 0));
        if(isset($this->elementArray[$parentID])){			
			echo "<ul>";
			
            for ($no=0; $no < count($this->elementArray[$parentID]) ;$no++){
				$urlAdd = "";
                
                $amount_items    = '';
                $amount_sub_cats = '';
                $downloads       = $this->elementArray[$parentID][$no][5];
                $subcats         = $this->elementArray[$parentID][$no][6];
                
                // If the number of files to be displayed?
                if ($params->get('view_amount_items')){
                    if ($downloads || $params->get('view_zero_values')){
                        if ($params->get('view_tooltip')){
                             if ($downloads){
                                 $amount_items = '<span class="jdmnumber">['.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_DOWNLOADS_TOOLTIP"), $downloads), '', '', $downloads, '', '').']</span>';
                             } else {
                                 $amount_items = '<span class="jdmnumber">['.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_DOWNLOADS_TOOLTIP"), $downloads), '', '', $downloads, '', '0').']</span>';
                             } 
                        } else {
                            $amount_items = '<span class="jdmnumber">['.$downloads.']</span>';
                        }
                    }
                } 
                
                // If the number of categories to be displayed?
                if ($params->get('view_amount_cat_items')){
                    if ($subcats || $params->get('view_zero_values')){
                        if ($params->get('view_tooltip')){
                            if ($subcats){
                                $amount_sub_cats = '<span class="jdmnumber">('.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_CATEGORIES_TOOLTIP"), $subcats), '', '', $subcats, '', '').')</span>';
                            } else {
                                $amount_sub_cats = '<span class="jdmnumber">('.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_CATEGORIES_TOOLTIP"), $subcats), '', '', $subcats, '', '0').')</span>';
                            }
                        } else {
                            $amount_items = '<span class="jdmnumber">('.$subcats.')</span>';
                        }
                    }
                }                 
                
				if ($this->elementArray[$parentID][$no][0] != $catid){
                    if ($this->elementArray[$parentID][$no][2]){
					    $urlAdd = " href=\"".$this->elementArray[$parentID][$no][2]."\"";
					    if ($this->elementArray[$parentID][$no][3]){
                            $urlAdd.=" target=\"".$this->elementArray[$parentID][$no][3]."\"";
                        }
				    }
                    echo "<li class=\"j7dctree_node\"><img height=\"18\" width= \"18\" class=\"tree_plusminus\" id=\"plusMinus".$this->elementArray[$parentID][$no][0]."\" src=\"modules/mod_jdownloads_tree/jdtree/images/plus.gif\"><img height=\"16\" width= \"16\" src=\"".$this->elementArray[$parentID][$no][4]."\"><a class=\"j7dctree_link\"$urlAdd>".$this->elementArray[$parentID][$no][1].$amount_sub_cats.$amount_items."</a>";    
                } else { 
                    echo "<li class=\"j7dctree_node\"><img height=\"18\" width= \"18\" class=\"tree_plusminus\" id=\"plusMinus".$this->elementArray[$parentID][$no][0]."\" src=\"modules/mod_jdownloads_tree/jdtree/images/plus.gif\"><img height=\"16\" width= \"16\" src=\"".$this->elementArray[$parentID][$no][4]."\">".$this->elementArray[$parentID][$no][1].$amount_sub_cats.$amount_items;      
                }  
				
				$this->drawSubNode($this->elementArray[$parentID][$no][0], $params);
				echo "</li>";
			}	
			echo "</ul>";			
		}		
	}
	
	function drawTree($home_link, $moduleclass_sfx, $params){
        $catid = intval(JArrayHelper::getValue($_REQUEST, 'catid', 0));
        
        echo '<div id="jdtree" class="moduletable'.$moduleclass_sfx.'">';               
		echo "<ul id=\"jdtreetopNodes\" class=\"j7dctree\">";
        echo '<img height="18" width="18" src="modules/mod_jdownloads_tree/jdtree/images/base.gif" /> '.$home_link;
		
        for($no = 0; $no < count($this->elementArray[0]); $no++){
			$urlAdd = "";
            
            $amount_items    = '';
            $amount_sub_cats = '';
            $downloads       = $this->elementArray[0][$no][5];
            $subcats         = $this->elementArray[0][$no][6];
               
            // If the number of files to be displayed?
            if ($params->get('view_amount_items')){
                if ($downloads || $params->get('view_zero_values')){
                    if ($params->get('view_tooltip')){
                        if ($downloads){
                             $amount_items = '<span class="jdmnumber">['.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_DOWNLOADS_TOOLTIP"), $downloads), '', '', $downloads, '', '').']</span>';
                        } else {
                             $amount_items = '<span class="jdmnumber">['.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_DOWNLOADS_TOOLTIP"), $downloads), '', '', $downloads, '', '0').']</span>';
                        }
                    } else {
                        $amount_items = '<span class="jdmnumber">['.$downloads.']</span>';
                    }
                }
            } 
            
            // If the number of subcategories to be displayed?
            if ($params->get('view_amount_cat_items')){
                if ($subcats || $params->get('view_zero_values')){
                    if ($params->get('view_tooltip')){
                        if ($subcats){
                            $amount_sub_cats = '<span class="jdmnumber">('.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_CATEGORIES_TOOLTIP"), $subcats), '', '', $subcats, '', '').')</span>';
                        } else {
                            $amount_sub_cats = '<span class="jdmnumber">('.JHtml::tooltip(JText::sprintf(JText::_("MOD_JDOWNLOADS_TREE_AMOUNT_OF_CATEGORIES_TOOLTIP"), $subcats), '', '', $subcats, '', '0').')</span>';
                        }
                    } else {
                        $amount_sub_cats = '<span class="jdmnumber">('.$subcats.')</span>';
                    }
                }
            }            
            
            if ($this->elementArray[0][$no][0] != $catid){
			    if($this->elementArray[0][$no][2]){
				    $urlAdd = " href=\"".$this->elementArray[0][$no][2]."\"";
				    if($this->elementArray[0][$no][3]){
                       $urlAdd.=" target=\"".$this->elementArray[0][$no][3]."\"";      
                    } 
			    }
                echo "<li onmouseover=\"this.className='jdmroothov'\" onmouseout=\"this.className='j7dctree_node'\" class=\"j7dctree_node\" id=\"node_".$this->elementArray[0][$no][0]."\"><img height=\"18\" width= \"18\" id=\"plusMinus".$this->elementArray[0][$no][0]."\" class=\"tree_plusminus\" src=\"modules/mod_jdownloads_tree/jdtree/images/plus.gif\"><img height=\"16\" width= \"16\" src=\"".$this->elementArray[0][$no][4]."\"><a class=\"j7dctree_link\"$urlAdd>".$this->elementArray[0][$no][1].$amount_sub_cats.$amount_items."</a>";		
            } else {
                echo "<li onmouseover=\"this.className='jdmroothov'\" onmouseout=\"this.className='j7dctree_node'\" class=\"j7dctree_node\" id=\"node_".$this->elementArray[0][$no][0]."\"><img height=\"18\" width= \"18\" id=\"plusMinus".$this->elementArray[0][$no][0]."\" class=\"tree_plusminus\" src=\"modules/mod_jdownloads_tree/jdtree/images/plus.gif\"><img height=\"16\" width= \"16\" src=\"".$this->elementArray[0][$no][4]."\">".$this->elementArray[0][$no][1].$amount_sub_cats.$amount_items;        
            }     
            
            //numlinks to be appended above, if required in future
			$this->drawSubNode($this->elementArray[0][$no][0], $params);
			echo "</li>";	
        }   	
		echo "</ul></div>";	
	}
}
?>