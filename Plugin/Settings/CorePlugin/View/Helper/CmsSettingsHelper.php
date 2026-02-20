<?php
/**
 * CmsSettingsHelper Class
 *
 * Various utility helper methods for the settings plugin, notably generating the admin form.
 *
 * @copyright    Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link         http://api.pyramidcms.com/docs/classCmsSettingsHelper.html
 * @package      Cms.Plugin.Settings.View.Helper
 * @since        Pyramid CMS v 1.0
 */
class CmsSettingsHelper extends AppHelper {

/**
 * Attached helpers
 */
	public $helpers = array(
		'Form' => array('className' => 'AppForm'),
		'Html' => array('className' => 'AppHtml'),
		'Media'
	);

/**
 * Form field counter for proper output of form inputs.
 */
	protected $_formCount = 0;

/**
 * Form depth used by adminForm to determine nesting level when looping through settings.
 */
	protected $_formDepth = 0;
	
/**
 * IDs for headers - set in adminTabs() and accessed in adminForm(). Used for the JS tab system.
 */
	protected $_headerIds = array();

/**
 * Spits out a nested admin form for settings, organized by package
 *
 * @param array
 * @return string
 */
	public function adminForm($settings) {
		$output = '';
		foreach ($settings as $header => $settings) {
			$output .= $this->Html->tag(
				'div',
				$this->_loopSettings($settings),
				array('id' => $this->_headerIds[$header])
			);
		}
		return $output;
	}
	
/**
 * Loops through settings to add form inputs as necessary. Supports nesting.
 *
 * @param array
 * @return string
 */
	protected function _loopSettings($settings) {
		$output = '';
		foreach ($settings as $key => $val) {
			if (!empty($val) && !isset($val['Setting'])) {
				$this->_formDepth++;
				$output .= $this->Html->tag(
					'h' . ($this->_formDepth + 1),
					Inflector::humanize(Inflector::underscore($key))
				);
				$output .= $this->_loopSettings($val);
				$this->_formDepth--;
			} else {
				if($val['Setting']['type'] == 'image' || $val['Setting']['type'] == 'document') {

					$thisTitle	= (!empty($val['Setting']['title'])) ? $val['Setting']['title']: $val['Setting']['key'];
					$output .= '<label>' . $thisTitle . '</label><div id="Setting' . $val['Setting']['id'] . Inflector::Camelize($val['Setting']['type']) . '" class="' . $thisTitle . 'Setting">';
	
					if($val['Setting']['type'] == 'image') {
						$arrayMediaResource	= (isset($val['Image'][0]) && !empty($val['Image'][0])) ? $val['Image'][0]: array();
						$mediaModel = 'Image';
						$fileType = 'image/*';
						$validateType = ''; // just to prevent null. validateType switches between $validate and $validateDocument in the model.
					} else {
						$arrayMediaResource	= (isset($val['Document'][0]) && !empty($val['Document'][0])) ? $val['Document'][0]: array();
						$mediaModel = 'Document';
						$fileType = 'application/pdf';
						$validateType = $val['Setting']['type'];
					}
					$existingImageId = '';
					if(isset($arrayMediaResource) && !empty($arrayMediaResource)) {
						
						$existingImageId = $arrayMediaResource['id'];
						
						$output .= $this->Html->link(
							$this->Html->image(
								'icons/cross.png',
								array(
									'alt' => 'Delete',
									'title' => 'Delete',
									'width' => 16,
									'height' => 16
								)
							)
							, array(
								'plugin' => 'media', 
								'controller' => 'attachments', 
								'action' => 'delete', 
								$arrayMediaResource['id'], 'admin' => true
							)
							, array('escape' => false,
									'class' => 'admin-btn')
							, "Are you sure you want to delete this item?"
						);
						
						
						if($mediaModel == 'Image') {
						// Image thumbnail 				
							$output .=  $this->Media->image($arrayMediaResource, 'thumb') . '<br>';
							
						// Caption input
							// removed
										
						} else {
						// Document link
							$output .= $this->Html->link( $arrayMediaResource['basename'], $this->Media->transferUrl($arrayMediaResource), array( 'target' => '_blank' ) );
						}
					}
					$output .= '</div>';
					// Uploader
					$output .= $this->_View->element('Media.upload_setting', array(
															'modelId' => $val['Setting']['id'], 
															'model' => 'Setting', 
															'group' => $mediaModel, 
															'settingId' => $val['Setting']['id'], 
															'fileType' => $fileType,
															'validateType' => $validateType
														)
													);
													

				} else {
					$value = isset($this->request->data['Setting'][$this->_formCount]['value']) ? $this->request->data['Setting'][$this->_formCount]['value'] : $val['Setting']['value'];
				
					$output .= $this->Form->input('Setting.' . $this->_formCount . '.id', array(
						'type' => 'hidden',
						'value' => $val['Setting']['id']
					));

					$options = array(
						'label' => $val['Setting']['title'] ? $val['Setting']['title'] : Inflector::humanize($key),
						'type' => $val['Setting']['type'],
						'options' => $val['Setting']['options'] ? $this->Html->tokenize($val['Setting']['options']) : null,
						'description' => $val['Setting']['description'] ? $val['Setting']['description'] : null,
						'disabled' => $val['Setting']['editable'] ? false : true,
						'value' => $value,
						'checked' => $val['Setting']['value'] == true ? true : false
					);

					$type = $val['Setting']['type'];
			
					if ($type == 'wysiwyg') {
						$options['wysiwyg'] = true;
						$type = 'textarea';
					}
					
					$options['type'] = $type;
					$output .= $this->Form->input('Setting.' . $this->_formCount . '.value', $options);
				}
        
        //print the key label which we'll format with some javscript
        $output .= '<span class="settingKey" data-for="#Setting'.$this->_formCount.'Value" data-key="'.$val['Setting']['key'].'"></span>';
        
				$this->_formCount++;
			}
		}
		return $output;
	}
	 
	
/**
 * Outputs tab list headers for the edit page.
 *
 * @param array Settings from the controller
 * @return string
 */
	public function adminTabs($settings) {
		$output = '';
		foreach ($settings as $header => $settings) {
			$headerId = 'tab-' . Inflector::underscore($header);
			$this->_headerIds[$header] = $headerId;
			
			$output .= $this->Html->tag(
				'li',
				$this->Html->link(Inflector::humanize(Inflector::underscore($header)), '#' . $headerId)
			);
		}
		return $output;
	}

/**
 * Simple convenience function to return a setting.
 * Reads from Configure before fetching from the database.
 * 
 * @param string setting key
 * @return mixed
 */
	public function show($key) {
		if (Configure::check('Settings.'.$key)){
			return Configure::read('Settings.'.$key);
		}
		
		$setting = ClassRegistry::init('Settings.Setting')->find(
			'first'
			, array(
				'conditions'	=> array(
						'Setting.key' => $key
						,
				)
				, 'contain'	=> array(
						'Image'
						, 'Document'
						,
				)
			)
		);
	
		if(isset($setting['Setting']['type'])) {
			if($setting['Setting']['type'] == 'image') {
				return (isset($setting['Image'][0]) && !empty($setting['Image'][0])) ? $setting['Image'][0]: array();
			} elseif($setting['Setting']['type'] == 'document') {
				return (isset($setting['Document'][0]) && !empty($setting['Document'][0])) ? $setting['Document'][0]: array();
			} 
		}

		return Configure::read('Settings.' . $key);
	}
/**
 * Little wrapper to return the SERVER_NAME variable.
 *
 * @return string
 */
	public function siteDomain() {
		return env('SERVER_NAME');
	}
}