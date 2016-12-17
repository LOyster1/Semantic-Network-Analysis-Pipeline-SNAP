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

/*
# This script is original from the component com_mediamu and is only modified to use it with jDownloads 
# ------------------------------------------------------------------------
@author Ljubisa - ljufisha.blogspot.com
@copyright Copyright (C) 2012 ljufisha.blogspot.com. All Rights Reserved.
@license - http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
Technical Support: http://ljufisha.blogspot.com
*/

defined("_JEXEC") or die();

class PLuploadScript
{

	public  $mediaRoot;
	public  $runtime;
    public  $runtimeScript;
        
	private $_maxFileSize;
	private $_chunkSize;
    private $_chunkUnit;
	private $_rename; //bool
	private $_uniqueNames; //bool
	private $_imageFilter;
	private $_otherFilesFilter;
	private $_resize; //bool
	private $_resizeWidth;
	private $_resizeHeight;
	private $_resizeQuality;
        
	private $_SCRIPT;

    /**
     *
     * @param string $PLdataDir Root folder for the script
     *
     */
	public function __construct( $PLdataDir )
	{
		$this->mediaRoot = $PLdataDir;
		$this->_setParams();
		$this->_buildScript();
	}
        
    /**
     * 
     * Properly set parameters for JavaScript usage
     * 
     */
	private function _setParams()
	{
        global $jlistConfig;        
        //runtimes
        $allRuntimes                    = 'html5,flash,gears,silverlight,browserplus,html4';
        $this->runtimeScript            = $jlistConfig['plupload.runtime'];
        $this->runtime                  = $this->runtimeScript == 'full' ? $allRuntimes : $this->runtimeScript;
        //default 1MB
        $this->_maxFileSize             = $jlistConfig['plupload.max.file.size'];
        //chunk upload
		$this->_chunkSize 		        = $jlistConfig['plupload.chunk.size'];
        $this->_chunkUnit               = $jlistConfig['plupload.chunk.unit'];
        $this->_chunkUnit               = strtolower($this->_chunkUnit);
        //file rename
		$this->_rename 			        = ($jlistConfig['plupload.rename'] == 1) ? 'true' : 'false';
        //file filters
		$imageFilter                    = $jlistConfig['plupload.image.file.extensions'];
        $this->_imageFilter             = $this->_cleanOption($imageFilter);
		$otherFilesFilter               = $jlistConfig['plupload.other.file.extensions'];
        $this->_otherFilesFilter        = $this->_cleanOption($otherFilesFilter);
        //generate unique file names
		$this->_uniqueNames		        = $jlistConfig['plupload.unique.names'] == 1 ? 'true' : 'false';
        //image resizing
        $this->_resize 			        = $jlistConfig['plupload.enable.image.resizing'] == 0 ? false : true;
        $this->_resizeWidth 		    = $jlistConfig['plupload.resize.width'];  // '640' default);
		$this->_resizeHeight		    = $jlistConfig['plupload.resize.height']; // '480' default;
		$this->_resizeQuality		    = $jlistConfig['plupload.resize.quality']; // '90' default;
	}
        
    /**
     * Sets up the JavaScript code with component parameters 
     * 
     * @return void
     */
	private function _buildScript()
	{
		$l_resize           = ""; //script resize line
		$l_chunk		    = ""; //script chuk_size line
		$l_flash_swf_url 	= "flash_swf_url : '" . $this->mediaRoot . "js/Moxie.swf',"; //script flash swf url line
		$l_silverlight_xap	= "silverlight_xap_url: '" . $this->mediaRoot . "js/Moxie.xap',"; //script silverlight xap line
                
		if($this->_resize)
                {
                    $l_resize = "resize : {width : " . $this->_resizeWidth . ", height : " . $this->_resizeHeight . ", quality : " . $this->_resizeQuality . "},";
		}

		if($this->_chunkSize !== 0 || $this->_chunkSize !== "") 
                {
                    $l_chunk = "chunk_size : '" . $this->_chunkSize . $this->_chunkUnit . "',";
		}
                
                ob_start();
                
        ?>


        $(function() {

                $('a#dismiss').click(function () {
                    $('#system-message-container').html('');
                });
                
                function handleUpStatus(up, file, info, chunk) {
                    // Called when a file or chunk has finished uploading
                    var rspObj = $.parseJSON(info.response);
                    var statusMsg = '';
                    var fileString = '';
                    var spanClass = '';

                    if(rspObj.error == 1) {
                        $('#' + file.id).attr('class', 'plupload_failed');
                        file.hint = rspObj.msg;
                        file.status = plupload.FAILED;
                        file.percent = 0;

                        up.total.size-= file.size;
                        up.total.percent-=100;
                        up.total.uploaded-=1;
                        spanClass = 'failed_uploading';
                    } else {
                        $('#' + file.id).attr('class', 'plupload_done');
                        file.status = plupload.DONE;
                        spanClass = 'success_uploading';
                    }
                    
                    statusMsg+= '<span class="' + spanClass + '">';
                    statusMsg+= ' Status: ';
                    statusMsg+= (file.status == plupload.DONE) ? 'DONE' : 'FAILED ';
                    statusMsg+= ' Code: ' + rspObj.code + ' : '+ rspObj.msg;
                    statusMsg+= '</span>';

                    fileString+= ' Id: ' + file.id + ' Name: ' + file.name + ' Size: ' + file.size + ' Loaded: ' + file.percent + '% ';
                    fileString+= statusMsg;
                    if(!chunk){
                        log('<b>[FileUploaded]</b> ' + fileString);
                    } else {
                        log('<b>[ChunkUploaded]</b> File:' + fileString);
                    }
                    
                }

	        function ajaxReq(dataString, action) {

		        var msgCont = $('#system-message-container');
		        msgCont.html('<span class="loading"></span>');

		        $.ajax({
			        type: 'POST',  
			        url: action, 
			        data: dataString,
			        dataType : 'json',
			        success: function(response) {

                                        msgCont.html(' ');
                                        var msgHTML = '';
                                                
				        if(response.error == 1) {
					        msgHTML+= '<dl id="system-message">';
					        msgHTML+= '<dt class="error">Error</dt>';
					        msgHTML+= '<dd class="error message">';
					        msgHTML+= '<ul><li>' + response.msg + '</li></ul>';
					        msgHTML+= '</dd>';
					        msgHTML+= '</dl>';
				        } else {
					        msgHTML+= '<dl id="system-message">';
					        msgHTML+= '<dt class="message">Error</dt>';
					        msgHTML+= '<dd class="message message">';
					        msgHTML+= '<ul><li>' + response.msg + '</li></ul>';
					        msgHTML+= '</dd>';
					        msgHTML+= '</dl>';
				        }
                                        window.frames[0].location.reload();
				        msgCont.html(msgHTML);
			        }  
		        });
	        }
	        //init uploader
	        function initUploader() {
		        $("#uploader").pluploadQueue({
			        // General settings
			        runtimes : '<?php echo $this->runtime ?>',
			        url : 'index.php?option=com_jdownloads&no_html=1&task=uploads.upload&<?php echo JSession::getFormToken()  ?>=1',
			        <?php echo $l_chunk ?>
					
			        rename : <?php echo $this->_rename ?>,
			        unique_names : <?php echo $this->_uniqueNames ?>,
					dragdrop: true,
					filters : {
						// Maximum file size
						max_file_size : '<?php echo $this->_maxFileSize  ?>mb',
						// Specify what files to browse for
						mime_types: [
							{title : "Image files", extensions : "<?php echo $this->_imageFilter ?>"},
							{title : "Other files", extensions : "<?php echo $this->_otherFilesFilter ?>"}
						]
					},
					<?php echo $l_resize ?>
					// Flash settings
			        <?php echo $l_flash_swf_url ?>
			        // Silverlight settings
			        <?php echo $l_silverlight_xap  ?>

					preinit : {
							Init: function(up, info) {
								log('[Init]', 'Info:', info, 'Features:', up.features);
							},
				 
							UploadFile: function(up, file) {
								log('[UploadFile]', file);
				 
								// You can override settings before the file is uploaded
								// up.setOption('url', 'upload.php?id=' + file.id);
								// up.setOption('multipart_params', {param1 : 'value1', param2 : 'value2'});
							}
						},

					// Post init events, bound after the internal events
					init : {
						PostInit: function() {
							// Called after initialization is finished and internal event handlers bound
							log('[PostInit]');
							 },
			 
						Browse: function(up) {
							// Called when file picker is clicked
							log('[Browse]');
						},
			 
						Refresh: function(up) {
							// Called when the position or dimensions of the picker change
							log('[Refresh]');
						},
			  
                        StateChanged: function(up) {
                            // Called when the state of the queue is changed
                            if(up.state == plupload.STARTED) {
                                //disable navigation
                                $('#dirbroswer').hide();
                                                        
                                $('div#upload_in_progress').addClass('upload_in_progress');
                                $('div#upload_in_progress').html('<h5>Upload in progress...</h5>');
                                // Add stop button
                                var stopBtn = document.createElement('a');
                                stopBtn.className = 'plupload_button plupload_stop';
                                stopBtn.id = 'plupload_stop';
                                stopBtn.innerHTML = '<?php echo addslashes(JText::_('COM_JDOWNLOADS_UPLOADER_STOP_UPLOAD'))  ?>';
                                stopBtn.href = '#',
                                stopBtn.onclick = function (up) {
                                    up.stop();
                                }
                                
                                $('.plupload_filelist_footer').prepend(stopBtn);
                                
                             }

                            if(up.state == plupload.STOPPED) {
                                //enable navigation and reload iframe
                                $('div#upload_in_progress').removeClass('upload_in_progress');
                                $('div#upload_in_progress').html('');
                                $('#dirbroswer').show();
                                                        
                                //window.frames[0].location.reload();

                                //add refresh uploader button
                                var refreshBtn = document.createElement('a');
                                refreshBtn.className = 'plupload_button plupload_refresh';
                                refreshBtn.id = 'plupload_refresh';
                                refreshBtn.innerHTML = '<?php echo addslashes(JText::_('COM_JDOWNLOADS_UPLOADER_REFRESH_UPLOADER'))  ?>';
                                refreshBtn.href = '#',
                                refreshBtn.onclick = function (up) {
                                    initUploader();
                                }
                                
                                $('.plupload_filelist_footer').prepend(refreshBtn);
                                
                            }
                                                log('<b>[StateChanged]</b>', up.state == plupload.STARTED ? "STARTED" : "STOPPED");  
                        },						
                        
						QueueChanged: function(up) {
							// Called when queue is changed by adding or removing files
							log('[QueueChanged]');
						},
			 
						OptionChanged: function(up, name, value, oldValue) {
							// Called when one of the configuration options is changed
							log('[OptionChanged]', 'Option Name: ', name, 'Value: ', value, 'Old Value: ', oldValue);
						},
			 
						BeforeUpload: function(up, file) {
							// Called right before the upload for a given file starts, can be used to cancel it if required
							log('[BeforeUpload]', 'File: ', file);
						},
			  
						UploadProgress: function(up, file) {
							// Called while file is being uploaded
							log('[UploadProgress]', 'File:', file, "Total:", up.total);
						},
			 
						FileFiltered: function(up, file) {
							// Called when file successfully files all the filters
							log('[FileFiltered]', 'File:', file);
						},
			  
						FilesAdded: function(up, files) {
							// Called when files are added to queue
							log('[FilesAdded]');
			  
							plupload.each(files, function(file) {
								log('  File:', file);
							});
						},
			  
						FilesRemoved: function(up, files) {
							// Called when files are removed from queue
							log('[FilesRemoved]');
			  
							plupload.each(files, function(file) {
								log('  File:', file);
							});
						},
			  
						FileUploaded: function(up, file, info) {
							// Called when file has finished uploading
							log('[FileUploaded] File:', file, "Info:", info);
						},
			  
						ChunkUploaded: function(up, file, info) {
							// Called when file chunk has finished uploading
							log('[ChunkUploaded] File:', file, "Info:", info);
						},
			 
						UploadComplete: function(up, files) {
							// Called when all files are either uploaded or failed
							log('[UploadComplete]');
						},
			 
						Destroy: function(up) {
							// Called when uploader is destroyed
							log('[Destroy] ');
						},
			  
						Error: function(up, args) {
							// Called when error occurs
							log('[Error] ', args);
						}
					}
				});
	        }
	        //log events
			function log() {
					var str = "";
			 
					plupload.each(arguments, function(arg) {
						var row = "";
			 
						if (typeof(arg) != "string") {
							plupload.each(arg, function(value, key) {
								// Convert items in File objects to human readable form
								if (arg instanceof plupload.File) {
									// Convert status to human readable
									switch (value) {
										case plupload.QUEUED:
											value = 'QUEUED';
											break;
			 
										case plupload.UPLOADING:
											value = 'UPLOADING';
											break;
			 
										case plupload.FAILED:
											value = 'FAILED';
											break;
			 
										case plupload.DONE:
											value = 'DONE';
											break;
									}
								}
			 
								if (typeof(value) != "function") {
									row += (row ? ', ' : '') + key + '=' + value;
								}
							});
			 
							str += row + " ";
						} else {
							str += arg + " ";
						}
					});

                        $('#log').prepend(str + '<span class="log_sep"></span>');
	        }	



	        // show/hide uploader log
	        $("#log_btn").click(function () {
		        $("#log").slideToggle('slow');
	        });

	        //add language support and initialize uploader
	        initUploader();
        });
        <?php
            $script = ob_get_contents();
            ob_clean();
            $this->_SCRIPT = $script;
	}
        
    /**
     * 
     * Get the dependency Script
     * 
     * @return string JavaScript code
     */
	public function getScript()
	{
		return $this->_SCRIPT;
	}
        
    /**
     * Clean Comma Separated Option
     * @param string $string Option to be cleaned
     * @return string
     */
	private function _cleanOption( $string )
	{
            return trim($string);
	}

}

?>