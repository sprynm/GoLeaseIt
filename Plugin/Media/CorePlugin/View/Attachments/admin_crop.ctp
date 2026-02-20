<?php
//
	$liVersions	= '';
//
	if (isset($versions) && !empty($versions))
	{
	//
		$widths	= array();
	//
		foreach ($versions AS $VERSION)
		{
		//
			if ($this->params->named['version'] != $VERSION['AttachmentVersion']['name'])
			{
			//
				$liVersions	.= '<li>' . $this->Html->link(
					$VERSION['AttachmentVersion']['name']
					, array(
						'version' => $VERSION['AttachmentVersion']['name']
						, 'plug'	=> $this->params->named['plug']
						, 'troller'	=> $this->params->named['troller']
						, 'foreign_key'	=> $this->params->named['foreign_key']
						, 'action'	=> 'crop'
						, 'banner'	=> (isset($this->params->named['banner']) && $this->params->named['banner'] ? true : false)
						, 'admin'	=> true
						, '#'		=> (isset($this->params->named['banner']) && $this->params->named['banner'] ? 'tab-banner-image' : 'tab-images')
						, $image['id']
						,
					)
				) . '</li>';
			}
		//
			$widths[]	= $VERSION['AttachmentVersion']['width'];
		//
			$dimensions[]	= array('name' => $VERSION['AttachmentVersion']['name'], 'width' => $VERSION['AttachmentVersion']['width'], 'height' => $VERSION['AttachmentVersion']['height']);
		}
	}
//
	$thisOne	= array_search(max($widths), $widths);
//
	$file_name	= $this->Media->file($image, $version['name']);
//
	$imageWidth	= $dimensions[$thisOne]['width'];
//
	$imageHeight	= $dimensions[$thisOne]['height'];
?>
    <div class="notification success png_bg">
      <a href="#" class="close">Close &nbsp;<img src="/img/icons/cross_grey_small.png" alt="close" title="Close this notification"></a>
      <div>Your image was cropped and saved.</div>
      <div id="cropResult"></div>
    </div>
    <div class="notification error png_bg">
      <a href="#" class="close">Close &nbsp;<img src="/img/icons/cross_grey_small.png" alt="close" title="Close this notification"></a>	
			<div>There were problems processing your image crop.<br><span class="status"></span></div>
    </div>
    
    <div class="container">
      <div class="row">
        <div class="crop-header">
	<p>
<?php
//link back to where the image was uploaded from
	if (strpos($this->params->named['troller'], 'prototype') !== false) {
		$instance = ClassRegistry::init('Prototype.PrototypeInstance')->findById($this->params->named['foreign_key']);
		//
		$url	= array(
			'plugin'	=> $this->params->named['plug']
			, 'controller'	=> $this->params->named['troller']
			, 'action'	=> ($this->params->named['troller'] == 'prototype_instances' ? 'summary_edit': 'edit')
			, 'instance'	=> $instance['PrototypeInstance']['slug']
			, 'banner'	=> (isset($this->params->named['banner']) && $this->params->named['banner'] ? true : false)
			, $image['foreign_key']
			, 'admin'	=> true
			,
		);
		
		if ($image['model'] != 'CustomFieldValue') {
			$url['#']	= (isset($this->params->named['banner']) && $this->params->named['banner'] ? 'tab-banner-image' : 'tab-images');
		}
	} elseif ($image['model'] == 'CustomFieldValue') {
		//special case where we need to link back to the model that the customfieldvalue is attached to
		$customFieldVal = ClassRegistry::init("CustomFields.CustomFieldValue")->findById($image['foreign_key']);
		if (!empty($customFieldVal['CustomFieldValue']['foreign_key'])) {
			$url = array(
				'plugin' => $this->params->named['plug']
				, 'controller' => $this->params->named['troller']
				, 'action' => 'edit'
				, $customFieldVal['CustomFieldValue']['foreign_key']
				//, 'banner'	=> (isset($this->params->named['banner']) && $this->params->named['banner'] ? true : false)
				, 'admin' => true
			);
		}
	} else {
		$url	= array(
			'plugin'	=> $this->params->named['plug']
			, 'controller'	=> $this->params->named['troller']
			, 'action'	=> 'edit'
			, 'banner'	=> (isset($this->params->named['banner']) && $this->params->named['banner'] ? true : false)
			, 'admin'	=> true
			, '#'		=> (strpos($this->params->named['troller'], 'prototype') !== false) ? 'tab-photos': 'tab-images'
			, $image['foreign_key']
			,
		);
	}
//
	print $this->Html->link(
			'Return to Images List'
			, $url
	); 
?>
	</p>
          <div class="crop-meta" style="float: none;">
            Cropping <strong><em>"<?php print $version['name']; ?>"</em></strong> version <span class="dimensions">
              (Dimensions: <em><?php print $version['width']; ?>px wide</em> by <em><?php print $version['height']; ?>px high</em>)</span>
            <?php 
						if (!empty($version['description'])):
							echo '<br><span class="dimensions">'.$version['description'].'</span>';
						endif;
						?> 
          </div>
					<br>
          <div class="btn-group docs-buttons">
						<div id="advancedCropOptions">
							<h3>Advanced Cropping Options</h3>
							<fieldset title="The size the image will be scaled to before cropping occurs.">
								<legend>Image Scale: </legend>
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="0.1" title="Zoom In">
									<span class="docs-tooltip" title="Zoom In">
										<span class="fa fa-search-plus"></span>
									</span>
								</button>
								<input type="number" class="input" data-method="zoom" step="0.01" value="1">
								<button type="button" class="btn btn-primary" data-method="zoom" data-option="-0.1" title="Zoom Out">
									<span class="docs-tooltip" title="Zoom Out">
										<span class="fa fa-search-minus"></span>
									</span>
								</button>
								<div class="input number">
									<label>Width: </label>
									<input type="number" id="ImageWidthDisplay" step="0.1">
								</div>
								<div class="input number">
									<label>Height: </label>
									<input type="number" id="ImageHeightDisplay" step="0.1">
								</div>
							</fieldset>
							<fieldset title="Pixels away from the center of the scaled image.">
								<legend>Crop Box Offset (from center):</legend>
								<div class="input number">
									<label>X: </label>
									<input type="number" id="CropBoxXDisplay" step="0.1" value="0">
								</div>
								<div class="input number">
									<label>Y: </label>
									<input type="number" id="CropBoxYDisplay" step="0.1" value="0">
								</div>
							</fieldset>
							<div class="input color" title="The colour outside of the edges of the original image.">
								<label>Background Colour: </label>
								<input id="BackgroundColorPicker" type="color" value="#ffffff">
							<?php if ($version['convert'] == 'image/png'): ?>
								<label for="BackgroundColorTransparent">Transparent: </label>
								<input id="BackgroundColorTransparent" type="checkbox" value="transparent">
							<?php endif; ?>
							</div>
						</div>
						<div>
							<label data-method="getCroppedCanvas" class="btn btn-primary">Crop &amp; Save</label>
							<button type="button" class="btn btn-primary" data-method="getCroppedCanvas" title="Crop And Save">
								<span class="docs-tooltip" title="Crop And Save">
									<span class="fa fa-check"></span>
								</span>
							</button>
						</div>
          </div>
          <div class="crop-select">
            <strong>Select alternate version to crop:</strong>
              <ul>
                <?php print $liVersions; ?>
              </ul>
           </div> 
        </div>
        <hr>
          <div class="col-md-9">
            <div class="img-container">
             <img id="image" src="<?php echo $this->Media->transferUrl( $this->Media->file( $image ) ); ?>">
            </div>
          </div>
			</div>
    </div>
<style>
        .container {
          max-width: <?php print $imageWidth; ?>px;
        }
        
        #image {
          max-width: 100%;
        }
        .notification {
          display: none;
        }
				
				.docs-buttons {
					display: flex;
					flex-wrap: wrap;
					justify-content: space-between;
					align-items: stretch;
					float: none;
					clear: both;
					padding: 1em 0;
				}
				
				.docs-buttons fieldset {
					border-radius: .5em;
					padding: .5em;
					margin: .5em;
					text-align: center;
				}
				
				.docs-buttons fieldset legend {
					padding: 0 .5em;
				}
				
				#advancedCropOptions {
					display: flex;
					flex-wrap: wrap;
					flex: 0 1 auto;
				}
				
				#advancedCropOptions h3 {
					flex: 1 1 100%;
					cursor: pointer;
				}
				
				#advancedCropOptions h3:after {
					content: " +";
				}
				
				#advancedCropOptions h3.open:after {
					content: " -";
				}
				
</style>
<script>
    $(function () {
      'use strict';
			$("#advancedCropOptions h3").click(function (){
				if ($(this).hasClass("open")){
					$(this).removeClass("open");
					$(this).siblings().slideUp(250);
				} else {
					$(this).addClass("open");
					$(this).siblings().slideDown(250);
				}
			}).siblings().hide();
      var console = window.console || { log: function () {} };
      var $image = $('#image');
      var $download = $('#download');
      var updateFields = function (){
        var canvasData = $image.cropper('getCanvasData');
        var cropBoxData = $image.cropper('getCropBoxData');
        $("#ImageWidthDisplay").val(Math.round(canvasData.width*10)/10);
        $("#ImageHeightDisplay").val(Math.round(canvasData.height*10)/10);
        var imageCenter = {
          x: canvasData.left + canvasData.width/2 
          , y: canvasData.top + canvasData.height/2
        }
        $("#CropBoxXDisplay").val(Math.round((cropBoxData.left - imageCenter.x + cropBoxData.width/2)*10)/10);
        $("#CropBoxYDisplay").val(Math.round((cropBoxData.top - imageCenter.y + cropBoxData.height/2)*10)/10);
        $("input[data-method=zoom]").val( Math.round( canvasData.width/canvasData.naturalWidth * 100 ) / 100 );
      };
      
      var centerImage = function (){
        var canvasData = $image.cropper("getCanvasData");
        var containerData = $image.cropper("getContainerData");        
        canvasData.left = containerData.width/2 - canvasData.width/2;
        canvasData.top = containerData.height/2 - canvasData.height/2;
        $image.cropper("setCanvasData",canvasData);
      };
      
      var centerCropArea = function (){
        var data = $image.cropper('getCropBoxData');
        var canvasData = $image.cropper('getCanvasData');
        data.top = canvasData.top + (canvasData.height/2) - (data.height/2);
        data.left = canvasData.left + (canvasData.width/2) - (data.width/2);
        $image.cropper('setCropBoxData', data);
      }
      
      // Cropper 
   		$image.cropper({
        strict:		true
        , viewMode:	0
        , background:	false
        , dragMode:	'none'
        , guides:	false
        , highlight:	false
        , cropBoxResizable: false
        , built: function () {
          $image.cropper('setCropBoxData', { 
            left: <?php print ($imageWidth / 2 - ($version['width'] / 2)); ?>
            , top: <?php print ($imageHeight / 2 - ($version['height'] / 2)); ?>
            , width: <?php print $version['width']; ?>
            , height: <?php print $version['height']; ?> 
          });
          
          $image.cropper('zoomTo', 1);
          centerCropArea();
          centerImage();
          updateFields();
        }
        
        , zoom: function (e) {
          if (e.ratio > 1) {
            e.preventDefault();
            return $image.cropper('zoomTo', 1);
          }
        }
        
        , crop: function (e){
          updateFields();
        }
        , minContainerWidth: <?php print $version['width']; ?>
        , minContainerHeight: <?php print $version['height']; ?> 
      });
      
      $("#ImageWidthDisplay").on("click change", function () {
        var data = $image.cropper('getCanvasData');
        data.width = parseFloat($(this).val());
        data.height = data.width / data.naturalWidth * data.naturalHeight;
        $image.cropper('setCanvasData', data);
        centerImage();
      });
      $("#ImageHeightDisplay").on("click change", function () {
        var data = $image.cropper('getCanvasData');
        data.height = parseFloat($(this).val());
        data.width = data.height / data.naturalHeight * data.naturalWidth;
        $image.cropper('setCanvasData', data);
        centerImage();
      });
      
      
      $("#CropBoxXDisplay").on("click change", function () {
        var data = $image.cropper('getCropBoxData');
        var canvasData = $image.cropper('getCanvasData');
        data.left = parseFloat( $(this).val() ) + canvasData.left + (canvasData.width/2) - (data.width/2);
        $image.cropper('setCropBoxData', data);
      });
      
      $("#CropBoxYDisplay").on("click change", function () {
        var data = $image.cropper('getCropBoxData');
        var canvasData = $image.cropper('getCanvasData');
        data.top = parseFloat( $(this).val() ) + canvasData.top + (canvasData.height/2) - (data.height/2);
        $image.cropper('setCropBoxData', data);
      });
      
			$("#BackgroundColorPicker").on("change", function(){
				$("#BackgroundColorTransparent").prop("checked", false);
			});
			
      // Methods for buttons/inputs
      $('.docs-buttons').on('click change', '[data-method]', function () {
        var $this = $(this);
        var data = $this.data();
        var $target = $(data.target);
        var result;
    
        //set the target to itself if it's an input field
        if ($(this).is("input")) {
          $target = $this;
        }
        
        if ($this.prop('disabled') || $this.hasClass('disabled')) {
          return;
        }
    
        if ($image.data('cropper') && data.method) {
          data = $.extend({}, data); // Clone a new one
          //grab the value to set
          if ( $target.length ) {
            if (typeof data.option === 'undefined') {
              try {
                data.option = JSON.parse($target.val());
              } catch (e) {
                console.log(e.message);
              }
            }
          }
    
          if (data.method === 'rotate') {
            $image.cropper('crop');
          }
              
          //apply the method to the cropper
          switch (data.method) {
            case 'zoom':
              
              if ($this.is("input")) {                
                data.option = parseFloat(data.option);
              } else {
                var canvasData = $image.cropper("getCanvasData");
                data.option = canvasData.width / canvasData.naturalWidth + data.option;
              }
              
              if (parseFloat(data.option) > 1) {
                data.option = 1;
              }
              $image.cropper('zoomTo', parseFloat(data.option));
              break;
            case 'getCroppedCanvas':
              var fillColor = $("#BackgroundColorTransparent:checked").val() || $("#BackgroundColorPicker").val() || 'transparent';
              result = $image.cropper(data.method, { 
								fillColor: fillColor
							});
							
							//since most browsers only do bi-linear antiliasing when scaling down canvas images we need to do it in steps
							var targetWidth = <?php print $version['width']; ?>;
							var targetHeight = <?php print $version['height']; ?>;
							var steps = Math.ceil(Math.log(result.width / targetWidth ) / Math.log(2));
							var resultContext = result.getContext('2d');
							var newResult;
							var newContext;
							var stepWidth = result.width;
							var stepHeight = result.height;
							//scale the image to the correct size
							for ( var i=0; i < steps - 1;i++ ){
								var halfStepWidth = Math.round(stepWidth / 2);
								var halfStepHeight = Math.round(stepHeight / 2);
								newResult = document.createElement('canvas');
								newResult.width = halfStepWidth;
								newResult.height = halfStepHeight;
								newContext = newResult.getContext('2d');
								newContext.drawImage(result, 0, 0, stepWidth, stepHeight, 0, 0, halfStepWidth, halfStepHeight);
								result = newResult;
								stepWidth = halfStepWidth;
								stepHeight = halfStepHeight;
							}
							var oldCanvas = result;
							result = document.createElement('canvas');
							result.width = targetWidth;
							result.height = targetHeight;
							resultContext = result.getContext('2d');
							resultContext.drawImage(oldCanvas, 0, 0, stepWidth, stepHeight, 0, 0, targetWidth, targetHeight );
              //save the cropped image to the server
              if (result) {
								var data = {
									photo: result.toDataURL(<?php print JSON_encode($version['convert']); ?>)
									, version: <?php print JSON_encode($version['name']); ?>
									, file_name: <?php print JSON_encode($file_name); ?>
									, width: <?php print $version['width']; ?>
									, height: <?php print $version['height']; ?>
									, mime_type: <?php print JSON_encode($version['convert']); ?>
									, convert: <?php print JSON_encode($version['convert']); ?>
								};
								
                $.ajax({
                  method: 'POST',
                  url: <?php echo json_encode(Router::url(array('action'=>'ajax_save_canvas', $image['id'], $version['id']))); ?>,
                  data: data,
                  success: function(data) {
                  //
                    var $d = new Date();
                  //	
                    var cropImg	= <?php print JSON_encode($this->Media->image($image, $version['name'])); ?>;
                  //
                    var cropResult	= $(cropImg).attr('src', $(cropImg).attr('src') + '?cb=' + $d.getTime()).removeAttr('height width');
                  //
                    $('#cropResult').html(cropResult);
                  //
                    $('.notification.success').fadeTo('slow', 1 );
                  },
                  error: function(err) {
										$('.notification.error .error-message').text("");
                    $('.notification.error').fadeTo('slow', 1 );
										if (err['status'] == 413) {
											var fileSize = function (bytes) {
												var exp = Math.log(bytes) / Math.log(1024) | 0;
												var result = (bytes / Math.pow(1024, exp)).toFixed(2);
												return result + ' ' + (exp == 0 ? 'bytes': 'KMGTPEZY'[exp - 1] + 'B');
											}

											$('.notification.error .status').text("The cropped image was too large to be sent to the server (" + fileSize(data['photo'].length) + ").");
										} else {
											$('.notification.error .status').text(err['status'] + ": " + err['statusText']);
										}
                  }
                });
              }
              break;
            default:
              result = $image.cropper(data.method, data.option, data.secondOption);              
              break;
          }
        }
      });
    });
</script>