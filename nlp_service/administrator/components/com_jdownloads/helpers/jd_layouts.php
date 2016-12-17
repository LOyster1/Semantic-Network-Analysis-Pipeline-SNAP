<?php
/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 *
 * @component jDownloads
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

$JLIST_BACKEND_SETTINGS_TEMPLATES_CATS_DEFAULT = '{cat_title_begin}<div style="background-color:#EFEFEF; padding:6px;">{subcats_title_text}</div>{cat_title_end}
{cat_info_begin}
<table width="100%" style="border-bottom: 1px solid #cccccc;">
  <tr valign="top" border="0px">
    <td width="65%" style="padding:5px;">{cat_pic}<b>{cat_title}</b></td>
    <td width="20%" style="padding:5px; text-align:right">{sum_subcats}</td>
    <td width="15%" style="padding:5px; text-align:right">{sum_files_cat}</td>
  </tr>
  <tr valign="top" border="0px">
    <td colspan="3" width="100%" style="padding:5px;">{cat_description}</td>
  </tr>
  <tr><td>{tags}</td></tr>
</table>
{cat_info_end}';

$JLIST_BACKEND_SETTINGS_TEMPLATES_CAT_DEFAULT = '{cat_title_begin}<div style="background-color:#EFEFEF; padding:6px;">{subcats_title_text}</div>{cat_title_end}
{cat_info_begin}
<table width="100%" style="border-bottom: 1px solid #cccccc;">
  <tr valign="top" border="0px">
    <td width="65%" style="padding:5px;">{cat_pic}<b>{cat_title}</b></td>
    <td width="20%" style="padding:5px; text-align:right">{sum_subcats}</td>
    <td width="15%" style="padding:5px; text-align:right">{sum_files_cat}</td>
  </tr>
  <tr valign="top" border="0px">
    <td colspan="3" width="100%" style="padding:5px;">{cat_description}</td>
  </tr>
{tags}
</table>
{cat_info_end}
{sub_categories}
<div style="clear:both">&#160;</div>
<div style="float:right;">{checkbox_top}</div>
{files}
{form_hidden}
<div style="text-align:right">{form_button}</div>';

// files layout with mini icons - no checkboxes (Standard Files Layout v2.5)
$JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT = '{files_title_begin}<div style="background-color:#EFEFEF; padding:6px;">
{files_title_text}</div>{files_title_end}

<table class = "{featured_class}" width="100%" border="0" cellpadding="5" cellspacing="5" style="background:#F8F8F8;border-bottom:1px solid #cccccc;">
     <tr valign="top">
        <td width="80%" valign="top">{file_pic} <b>{file_title}</b>
          {release} {pic_is_new} {pic_is_hot} {pic_is_updated}
        </td>
        <td>{rating}</td>
       <td>{featured_pic}</td>
     </tr>
     <tr valign="top">
        <td valign="top" class="jd_body">{description}</td>        
        <td valign="top" class="jd_body" width="90%">{screenshot_begin}<a href="{screenshot}" rel="lightbox"> <img src="{thumbnail}" align="right" alt="" /></a>{screenshot_end}</td>
     </tr>
     <tr>
        <td valign="top" width="10%" align="center">{preview_player}</td>
     </tr>
     <tr>
        <td class = "{featured_detail_class}" style="background:#F8F8F8; padding:5px;" valign="top" width="90%"><small>{license_text} {author_text} {author_url_text} {created_date_value} {language_text} {system_text} {filesize_value} {hits_value}</small></td>
<td> {sum_jcomments}</td>
        <td valign="top" width="10%" align="center">
            {url_download}
        </td>
     </tr>
     <tr><td>{tags}</td></tr>
</table>';  

$JLIST_BACKEND_SETTINGS_TEMPLATES_SUMMARY_DEFAULT = '<div class="jd_cat_title" style="padding:5px; font-size:10px; font-weight:normal;">{summary_pic} {title_text}</div>
<div valign="top" style="padding:5px;">{download_liste}</div>
{captcha}
{password}
<div style="padding:5px;">{aup_points_info}</div>
<div style="padding:5px; text-align:center;"><b>{license_title}</b></div>
<div>{license_text}</div>
<div style="text-align:center">{license_checkbox}</div>
<div style="text-align:center; padding:5px;">{download_link}</div>
<div style="text-align:center;">{info_zip_file_size}</div>
<div style="text-align:center;">{external_download_info}</div>
<div style="text-align:center;">{user_limitations}</div>
<div>{google_adsense}</div>';

// Details Layout (Standard Details Layout v2.5 - Full Info)
$JLIST_BACKEND_SETTINGS_TEMPLATES_DETAILS_DEFAULT = '<table class="jdtable" border="0" cellpadding="10" cellspacing="5" width="100%">
    <tbody>
    <tr>
        <td colspan="1" height="38" valign="top"><span style="font-size: 13pt;">{file_pic} {file_title} {pic_is_new} {pic_is_hot} {pic_is_updated}</span></td>
        <td>{featured_pic} </td>
        <td style="text-align: center;">{rating}</td>
    </tr>
    <tr>
        <td height="auto" valign="top" width="313">
            <p>{description_long}</p>
            <div id="thumbs">
                <ul id="jdmain">
                    <li>{screenshot_begin}<a href="{screenshot}" rel="lightbox"> <img src="{thumbnail}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end}</li>
                    <li>{screenshot_begin2}<a href="{screenshot2}" rel="lightbox"> <img src="{thumbnail2}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end2}</li>
                    <li>{screenshot_begin3}<a href="{screenshot3}" rel="lightbox"> <img src="{thumbnail3}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end3}</li>
                    <li>{screenshot_begin4}<a href="{screenshot4}" rel="lightbox"> <img src="{thumbnail4}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end4}</li>
                    <li>{screenshot_begin5}<a href="{screenshot5}" rel="lightbox"> <img src="{thumbnail5}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end5}</li>
                    <li>{screenshot_begin6}<a href="{screenshot6}" rel="lightbox"> <img src="{thumbnail6}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end6}</li>
                    <li>{screenshot_begin7}<a href="{screenshot7}" rel="lightbox"> <img src="{thumbnail7}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end7}</li>
                    <li>{screenshot_begin8}<a href="{screenshot8}" rel="lightbox"> <img src="{thumbnail8}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end8}</li>
                    <li>{screenshot_begin9}<a href="{screenshot9}" rel="lightbox"> <img src="{thumbnail9}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end9}</li>
                    <li>{screenshot_begin10}<a href="{screenshot10}" rel="lightbox"> <img src="{thumbnail10}" alt="" align="right" border="0" hspace="10" vspace="0" /></a>{screenshot_end10}</li>
                </ul>
            <div style="clear: both;">{preview_player} </div>
            </div>
        </td>

        <td valign="top" width="10"> </td>
        <td valign="top" width="150">

        <table class="jdtable" style="border-style: solid; border-width: thin; border-color: #CECECE; padding: 5px; background-color: #efefef;" border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody>
            <tr><td colspan="2" height="25" valign="top"><p style="background-color: #cecece; padding: 2px;" align="center"><strong>{details_block_title}</strong></p></td></tr>
            <tr><td height="auto" valign="top">{release_title}</td><td style="text-align: right;" valign="top">{release}</td></tr>
            <tr><td height="auto" valign="top">{filesize_title}</td><td style="text-align: right;" valign="top">{filesize_value}</td></tr>
            <tr><td height="auto" valign="top">{hits_title}</td><td style="text-align: right;" valign="top">{hits_value}</td></tr>
            <tr><td height="auto" valign="top">{language_title}</td><td style="text-align: right;" valign="top">{language_text}</td></tr>
            <tr><td height="auto" valign="top">{license_title}</td><td style="text-align: right;" valign="top">{license_text}</td></tr>
            <tr><td height="auto" valign="top">{author_title}</td><td style="text-align: right;" valign="top">{author_text}</td></tr>
            <tr><td height="auto" valign="top">{author_url_title}</td><td style="text-align: right;" valign="top">{author_url_text}</td></tr>
            <tr><td height="auto" valign="top">{price_title}</td><td style="text-align: right;" valign="top">{price_value}</td></tr>
            <tr><td height="auto" valign="top">{created_date_title}</td><td style="text-align: right;" valign="top">{created_date_value}</td></tr>
            <tr><td height="auto" valign="top">{created_by_title}</td><td style="text-align: right;" valign="top">{created_by_value}</td></tr>
            <tr><td height="auto" valign="top">{modified_date_title}</td><td style="text-align: right;" valign="top">{modified_date_value}</td></tr>
            <tr><td height="auto" valign="top">{modified_by_title}</td><td style="text-align: right;" valign="top">{modified_by_value}</td></tr>
            <tr><td colspan="2" height="60" align="center" valign="middle"><p align="center">{url_download}</p></td></tr>
            <tr><td colspan="2" height="auto" align="center" valign="middle">{mirror_1} {mirror_2}</td></tr>
            <tr><td colspan="2" height="auto" align="center" valign="middle"><small>{report_link}</small></td></tr>
            </tbody>
        </table>
        </td>
    </tr>
    </tbody>
</table>
{tags}';

$JLIST_BACKEND_SETTINGS_TEMPLATES_DETAILS_DEFAULT_WITH_TABS = '<table width="100%" border="0" cellpadding="0" cellspacing="5">
  <tr>
    <td height="38" colspan="3" valign="middle"><h2>{file_pic} {file_title} {release} {pic_is_new}{pic_is_hot}{pic_is_updated}</h2></td>
    <td valign="top">{rating}</td>
  </tr>
</table>
{tags}
{tabs begin}

{tab description}
<table width="100%" border="0" cellpadding="0" cellspacing="5">
    <tr>
    <td style="text-align: justify;vertical-align:top;">{description_long}</td>
</tr></table>
{tab description end}

{tab pics}
<table width="100%" border="0" cellpadding="0" cellspacing="5">
<tr>
<td style="text-align: justify;vertical-align:top;">

<div style="padding:5px 5px 10px 5px;overflow: hidden;margin-bottom: 10px;margin-top: 8px;">

  <div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin}<a href="{screenshot}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail}" align="right" /></a>{screenshot_end}
  </div>
  </div>
  
  <div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin2}<a href="{screenshot2}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail2}" align="right" /></a>{screenshot_end2}
  </div>
  </div>
  
  <div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin3}<a href="{screenshot3}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail3}" align="right" /></a>{screenshot_end3}
  </div>
  </div>
  
  <div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin4}<a href="{screenshot4}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail4}" align="right" /></a>{screenshot_end4}
  </div>
  </div>
  
  <div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin5}<a href="{screenshot5}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail5}" align="right" /></a>{screenshot_end5}
  </div>
</div>

</div>

</td>
</tr>
</table>
{tab pics end}

{tab mp3}
<table width="100%" border="0" cellpadding="0" cellspacing="5">
    <tr>
       <td style="text-align: justify;vertical-align:top;">{mp3_player}</td>
    </tr>
    <tr>
       <td style="text-align: justify;vertical-align:top;">{mp3_id3_tag}</td>
    </tr>
</table>
{tab mp3 end}

{tab data}
<div style="margin-left: 5px;margin-bottom: 15px;clear: both;padding: 8px;background-color: #f4f4f4;border: 1px solid #dfdfdf;margin-top: 3px;border-radius: 5px;">  
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding:5px 10px 5px 10px;">
   
    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{release_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{release}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{hits_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{hits_value}&#160;</td>
    </tr>

    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{filesize_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{filesize_value}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{created_date_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{created_date_value}&#160;</td>
    </tr>
    
    <tr>
    <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{language_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{language_text}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{modified_date_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{modified_date_value}&#160;</td>
    </tr>

    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{price_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{price_value}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{created_by_title}&#160;</span></td>
        <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{created_by_value}&#160;</td>
    </tr>

    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{license_title}&#160;</span></td>
        <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{license_text}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{modified_by_title}&#160;</span></td>
        <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{modified_by_value}&#160;</td>
    </tr>
    
    <tr>
       <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{author_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{author_text}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{md5_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{md5_value}&#160;</td>
    </tr>

    <tr>
    <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{author_url_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{author_url_text}&#160;</td>

        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{sha1_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{sha1_value}&#160;</td>

    </tr>
</table>
</div>
{tab data end}

{tab download}
<table width="100%" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <td height="20" align="center">{file_name_title}: {file_name}</td>
      </tr>
      <tr>
        <td height="20" align="center">{filesize_title}: {filesize_value}</td>
      </tr>

      <tr>
         <td align="center" valign="middle">{url_download} {mirror_1} {mirror_2}
         </td>
      </tr>
</table>
{tab download end}
{tabs end}';    
    
$JLIST_BACKEND_SETTINGS_TEMPLATES_DETAILS_DEFAULT_NEW_25 = '<div style="padding-top:15px;padding-bottom:10px;" height="38" colspan="3" valign="top"><span style="font-weight: bold;font-size:13pt;">{file_pic} {file_title} {pic_is_new} {pic_is_hot} {pic_is_updated}</span>
     <div style="float: right;">{rating}</div>
</div>
{tags}
<div>{description_long}</div>
<div>{mp3_player}</div>
<div>{mp3_id3_tag}</div>

<div style="padding:5px 5px 10px 5px;overflow: hidden;margin-bottom: 10px;margin-top: 8px;">
<div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin}<a href="{screenshot}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail}" align="right" /></a>{screenshot_end}</div></div>
<div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin2}<a href="{screenshot2}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail2}" align="right" /></a>{screenshot_end2}</div></div>
<div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin3}<a href="{screenshot3}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail3}" align="right" /></a>{screenshot_end3}</div></div>
<div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin4}<a href="{screenshot4}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail4}" align="right" /></a>{screenshot_end4}</div></div>
<div style="float: left;"><div style="display: block;text-align: center !important;vertical-align: middle !important;">{screenshot_begin5}<a href="{screenshot5}" rel="lightbox"><img style="border : 1px solid lightgray;  padding:4px; margin-right: 5px;" src="{thumbnail5}" align="right" /></a>{screenshot_end5}</div></div>
</div>

<div style="margin-left: 5px;margin-bottom: 15px;clear: both;padding: 8px;background-color: #f4f4f4;border: 1px solid #dfdfdf;margin-top: 3px;border-radius: 5px;">  
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="padding:5px 10px 5px 10px;">
   
    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{release_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{release}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{hits_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{hits_value}&#160;</td>
    </tr>

    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{filesize_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{filesize_value}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{created_date_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{created_date_value}&#160;</td>
    </tr>
    
    <tr>
    <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{language_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{language_text}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{modified_date_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{modified_date_value}&#160;</td>
    </tr>

    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{price_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{price_value}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{created_by_title}&#160;</span></td>
        <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{created_by_value}&#160;</td>
    </tr>

    <tr>
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{license_title}&#160;</span></td>
        <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{license_text}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{modified_by_title}&#160;</span></td>
        <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{modified_by_value}&#160;</td>
    </tr>
    
    <tr>
       <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{author_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{author_text}&#160;</td>
    
        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">&#160;</td>
    </tr>

    <tr>
    <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">{author_url_title}&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">{author_url_text}&#160;</td>

        <td style="margin: 0px;padding: 0px 0px 5px;width: 100px;vertical-align: top;color: #464646;">
    <span style="border-bottom: 1px dotted #b7b7b7;padding: 5px 0px;width: 100px;position: absolute;display: block;z-index: 0;"></span>
    <span style="font-weight: bold;padding-right: 3px;background-color: #f4f4f4;position: relative;">&#160;</span></td>
    <td style="padding: 0px 0px 0px 6px;width: 200px;vertical-align: top;">&#160;</td>
    </tr>
    
    <tr>
    <td style="padding-top:15px;padding-bottom:10px;" colspan="4" align="center" valign="middle">
    <p align="center"><font size="2">{url_download}</font>{mirror_1} {mirror_2}</p></td>
    </tr>
    <tr><td style="padding-top:5px;padding-bottom:5px; font-size:11px;" colspan="4" align="center" valign="middle">{report_link}</td></tr>
</table>
</div>';

// files layout WITH checkboxes - no mini icons (Standard Files Layout with Checkboxes v2.5)  
$JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NEW_SIMPLE_1 = '{files_title_begin}<div style="background-color:#EFEFEF; padding:6px;">{files_title_text}</div>{files_title_end}

<table class="{featured_class}" width="100%" style="padding:3px; background-color:#F5F5F5;">
   <tr valign="middle">
      <td width="55%">{file_pic} {file_title} {release} {pic_is_new} {pic_is_hot} {pic_is_updated}</td>
      <td width="15%">
          <p align="center">{rating}</p>
      </td>
       <td width="15%">
          <p align="center">{featured_pic}</p>
      </td>
      <td width="15%">
          <p style="text-align: right;">Select {checkbox_list}</p>
      </td>
   </tr>
   <tr><td>{tags}</td></tr>
</table>
<table class="{featured_detail_class}" width="100%" style="padding:3px;">    
   <tr>
      <td width="75%" align="left" valign="top">{description}<br />{mp3_player}<br />{mp3_id3_tag}</td>
      <td valign="top">{screenshot_begin}<a href="{screenshot}" rel="lightbox"> <img src="{thumbnail}" align="right" /></a>{screenshot_end}
      </td>
      <td width="15%" valign="top"> {created_date_title}<br /> {filesize_title}<br /> {hits_title}</td>
      <td text-align="right" width="10%" valign="top">{created_date_value}<br />{filesize_value}<br />{hits_value}</td>
   </tr>
</table>';

// files layout WITHOUT checkboxes - no mini icons (Standard Files Layout without Checkboxes v2.5)
$JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_DEFAULT_NEW_SIMPLE_2 = '{files_title_begin}<div style="background-color:#EFEFEF; padding:6px;">{files_title_text}</div>{files_title_end}

<table class="{featured_class}" width="100%" style="padding:3px; background-color:#F5F5F5;">
   <tr valign="middle">
      <td width="55%">{file_pic} {file_title} {release} {pic_is_new} {pic_is_hot} {pic_is_updated}</td>
      <td width="20%">
          <p align="center">{rating}</p>
      </td>
      <td width="10%">
          <p align="center">{featured_pic}</p>
      </td>
      <td width="15%">
          <p align="center">{url_download}</p>
      </td>
   </tr>
   <tr><td>{tags}</td></tr>
</table>
<table class="{featured_detail_class}" width="100%" style="padding:3px;">    
   <tr>
      <td width="75%" align="left" valign="top">{description}<br />{mp3_player}<br />{mp3_id3_tag}</td>
      <td valign="top">{screenshot_begin}<a href="{screenshot}" rel="lightbox"> <img src="{thumbnail}" align="right" /></a>{screenshot_end}
      </td>
      <td width="15%" valign="top"> {created_date_title}<br /> {filesize_title}<br /> {hits_title}</td>
      <td text-align="right" width="10%" valign="top">{created_date_value}<br />{filesize_value}<br />{hits_value}</td>
   </tr>
</table>';

$JLIST_BACKEND_SETTINGS_TEMPLATES_CATS_COL_DEFAULT = '{cat_info_begin}
  <table width="100%">
    <tr valign="top" border="0px">
      <td width="25%" style="padding:5px; text-align:center">{cat_pic1}<b><br />{cat_title1}</b><br />{sum_subcats1}<br />{sum_files_cat1}</td>
      <td width="25%" style="padding:5px; text-align:center">{cat_pic2}<b><br />{cat_title2}</b><br />{sum_subcats2}<br />{sum_files_cat2}</td>
      <td width="25%" style="padding:5px; text-align:center">{cat_pic3}<b><br />{cat_title3}</b><br />{sum_subcats3}<br />{sum_files_cat3}</td>
      <td width="25%" style="padding:5px; text-align:center">{cat_pic4}<b><br />{cat_title4}</b><br />{sum_subcats4}<br />{sum_files_cat4}</td>
    </tr>
  </table>
{cat_info_end}';

$JLIST_BACKEND_SETTINGS_TEMPLATES_UPLOAD_DEFAULT='';
$JLIST_BACKEND_SETTINGS_TEMPLATES_SEARCH_DEFAULT='';

#Standard Layout for MP3 ID3 Tags
$JLIST_BACKEND_SETTINGS_TEMPLATES_ID3TAG = '<table max-width="300px" style="padding:5px; background-color:#FFFFDD;">
<tr>
  <td width="80px">{album_title}</td>
  <td width="220px">{album}</td>
</tr>
<tr>
  <td width="80px">{name_title}</td>
  <td width="220px">{name}</td>
</tr>
<tr>
  <td width="80px">{year_title}</td>
  <td width="220px">{year}</td>
</tr>
<tr>
  <td width="80px">{artist_title}</td>
  <td width="220px">{artist}</td>
</tr>
<tr>
  <td width="80px">{genre_title}</td>
  <td width="220px">{genre}</td>
</tr>
<tr>
  <td width="80px">{length_title}</td>
  <td width="220px">{length}</td>
</tr>
</table>';

$cats_header = '<table class="jd_top_navi" width="100%" style="border-bottom: 1px solid #cccccc;">
<tr valign="top" border="0px">
<td style="padding:5px;">{home_link}</td>
<td style="padding:5px;">{search_link}</td>
<td style="padding:5px;">{upload_link}</td>
<td style="padding:5px;" align="right" valign="bottom">{category_listbox}</td>
</tr>
</table>';

$cats_subheader = '<table class="jd_cat_subheader" width="100%">
<tr>
<td width="45%" valign="top">
<b>{subheader_title}</b>
</td>
<td width="55%" valign="top" colspan="2">
<div class="jd_page_nav" style="text-align:right">{page_navigation_pages_counter} {page_navigation}</div>
</td>
</tr>
<tr>
<td width="45%" valign="top" align="left">{count_of_sub_categories}</td>
<td width="55%" valign="top" colspan="2"></td>
</tr>
</table>';

$cats_footer = '<table class="jd_footer" style="width:100%;">   
    <tr>
        <td style="width:100%; vertical-align:top">
            <div class="jd_page_nav" style="text-align:right">{page_navigation}</div>
        </td>
    </tr>
</table>
<div style="text-align:left" class="back_button">{back_link}</div>';

$cat_header = '<table class="jd_top_navi" width="100%" style="border-bottom: 1px solid #cccccc;">
<tr valign="top" border="0px">
<td style="padding:5px;">{home_link}</td>
<td style="padding:5px;">{search_link}</td>
<td style="padding:5px;">{upload_link}</td>
<td style="padding:5px;">{upper_link}</td>
<td style="padding:5px;" align="right" valign="bottom">{category_listbox}</td>
</tr>
</table>';

$cat_subheader = '<table class="jd_cat_subheader" width="100%">
<tr>
<td width="45%" valign="top">
<b>{subheader_title}</b>
</td>
<td width="55%" valign="top" colspan="2">
<div class="jd_page_nav" style="text-align:right">{page_navigation_pages_counter} {page_navigation}</div>
</td>
</tr>
<tr>
<td width="45%" valign="top" align="left">{count_of_sub_categories}</td>
<td width="55%" valign="top" colspan="2"></td>
</tr>
</table>';

$cat_footer = '<table class="jd_footer" style="width:100%;">   
    <tr>
        <td style="width:100%; vertical-align:top">
            <div class="jd_page_nav" style="text-align:right">{page_navigation}</div>
        </td>
    </tr>
</table>
<div style="text-align:left" class="back_button">{back_link}</div>';

$files_header = '<table class="jd_top_navi" width="100%" style="border-bottom: 1px solid #cccccc;">
<tr valign="top" border="0px">
<td style="padding:5px;">{home_link}</td>
<td style="padding:5px;">{search_link}</td>
<td style="padding:5px;">{upload_link}</td>
<td style="padding:5px;">{upper_link}</td>
<td style="padding:5px;" align="right" valign="bottom">{category_listbox}</td>
</tr>
</table>';

$files_subheader = '<table class="jd_cat_subheader" width="100%">
<tr>
<td width="60%" valign="top">
<b>{subheader_title}</b>
</td>
<td width="40%" valign="top" colspan="2">
<div class="jd_page_nav" style="text-align:right">{page_navigation_pages_counter} {page_navigation}</div>
</td>
</tr>
<tr>
<td width="60%" valign="top" align="left">{count_of_sub_categories}</td>
<td width="40%" valign="top" colspan="2">
<div class="jd_sort_order" style="text-align:right">{sort_order}</div>
</td>
</tr></table>';

$files_footer = '<table class="jd_footer" style="width:100%;">   
    <tr>
        <td style="width:100%; vertical-align:top">
            <div class="jd_page_nav" style="text-align:right">{page_navigation}</div>
        </td>
    </tr>
</table>
<div style="text-align:left" class="back_button">{back_link}</div>';

$details_header = '<table class="jd_top_navi" width="100%" style="border-bottom: 1px solid #cccccc;">
<tr valign="top" border="0px">
<td style="padding:5px;">{home_link}</td>
<td style="padding:5px;">{search_link}</td>
<td style="padding:5px;">{upload_link}</td>
<td style="padding:5px;">{upper_link}</td>
<td style="padding:5px;" align="right" valign="bottom">{category_listbox}</td>
</tr>
</table>';

$details_subheader = '<table class="jd_cat_subheader" width="100%">
<tr><td><b>{detail_title}</b></td></tr>
</table>';

$details_footer = '<div style="text-align:left" class="back_button">{back_link}</div>';

$summary_header = '<table class="jd_top_navi" width="100%" style="border-bottom: 1px solid #cccccc;">
<tr valign="top" border="0px">
<td style="padding:5px;">{home_link}</td>
<td style="padding:5px;">{search_link}</td>
<td style="padding:5px;">{upload_link}</td>
<td style="padding:5px;">{upper_link}</td>
<td style="padding:5px;" align="right" valign="bottom">{category_listbox}</td>
</tr>
</table>';

$summary_subheader = '<table class="jd_cat_subheader" width="100%">
<tr><td><b>{summary_title}</b></td></tr>
</table>';

$summary_footer = '<div style="text-align:left" class="back_button">{back_link}</div>';

$search_header = '<table class="jd_top_navi" width="100%" style="border-bottom: 1px solid #cccccc;">
<tr valign="top" border="0px">
<td style="padding:5px;">{home_link}</td>
<td style="padding:5px;">{search_link}</td>
<td style="padding:5px;">{upload_link}</td>
<td style="padding:5px;">{upper_link}</td>
<td style="padding:5px;" align="right" valign="bottom">{category_listbox}</td>
</tr>
</table>';
$search_subheader = '';
$search_footer = '<div style="text-align:left" class="back_button">{back_link}</div>';

// This layout is used to view the subcategories from a category with pagination. 
// it must not be 'activated' for this
// no header, subheader or footer data is required here
$JLIST_BACKEND_SETTINGS_TEMPLATES_SUBCATS_PAGINATION_BEFORE = '{cat_title_begin}<div style="background-color:#EFEFEF; padding:6px;">{subcats_title_text}</div>
<div id="pageNavPosition" class="pageNavPosition"> </div>
{cat_title_end}
<table id="results" class="jdsubcats-table" style="border-bottom: 1px solid #cccccc;" width="100%" /><tr> </tr>';
$JLIST_BACKEND_SETTINGS_TEMPLATES_SUBCATS_PAGINATION_DEFAULT = '{cat_info_begin}
  <tr valign="top" border="0px">
    <td width="65%" style="padding:5px;">{cat_pic}<b>{cat_title}</b></td>
    <td width="20%" style="padding:5px; text-align:right">{sum_subcats}</td>
    <td width="15%" style="padding:5px; text-align:right">{sum_files_cat}</td>
  </tr>
{cat_info_end}';
$JLIST_BACKEND_SETTINGS_TEMPLATES_SUBCATS_PAGINATION_AFTER = '</table>';

$JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_NEW_ALTERNATE_1_BEFORE = '<div id="jd"> 
    <div class="items">';
$JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_NEW_ALTERNATE_1 = '{files_title_begin}<div style="background-color:#EFEFEF; padding:6px; border-width:1px;border-bottom-style:solid; border-color: #cccccc;">
{files_title_text}</div>{files_title_end}

<div class="jd_row">
    <div class="width100 first-cell">
        <div class="teaser-item">
        <div class="pos-media media-left">
            {file_pic}
        </div>
        <div class="pos-media media-right">
                {rating}
        </div>
        <h2 class="pos-title"> {file_title} {pic_is_new} {pic_is_hot} {pic_is_updated}</h2>
        <ul class="pos-specification">
            <li class="element element-itemmodified first">
                <strong>{created_date_title}: </strong> {created_date_value}
            </li>
            <li class="element element-text">
                <strong>{release_title}: </strong>{release}
            </li>
            <li class="element element-text">
                <strong>{license_title}: </strong>{license_text}
            </li>
            <li class="element element-text">
                <strong>{filesize_title}: </strong>{filesize_value}
            </li>
            <li class="element element-text">
                {url_download}
            </li>
        </ul>
        <ul class="pos-specification">
            <li class="element-text last">
                  {screenshot_begin}<a href="{screenshot}" rel="lightbox"> <img class="list-img" src="{thumbnail}" /></a>{screenshot_end} {description}
            </li>
        </ul>
        <div>
            {tags}
        </div>
        <div class="pos-button">
             {link_to_details}
        </div>
    </div>
    </div>
</div>';
$JLIST_BACKEND_SETTINGS_TEMPLATES_FILES_NEW_ALTERNATE_1_AFTER = '    </div>
</div>';
?>