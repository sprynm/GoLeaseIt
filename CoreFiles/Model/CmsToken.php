<?php
/**
 * A basic token class that provides core functionality for features like login tokins
 * and email recovery tokens.
 *
 * @copyright	 Copyright 2010-2012, Radar Hill Technology Inc. (http://radarhill.com)
 * @link		 http://api.pyramidcms.com/docs/classCmsToken.html
 * @package		 Cms.Model 
 * @since		 Pyramid CMS v 1.0
 */
class CmsToken extends AppModel {

/**
 * Create a new token by providing the data to be stored in the token.
 *
 * @param array $data
 * @return mixed
 */
	public function generate($data = null) {
		$data = array(
			'token' => substr(md5(uniqid(rand(), 1)), 0, 10), 
			'data' => serialize($data)
		);
		if ($this->save($data)) {
			return $data['token'];
		}
		return false;
	}

/**
 * Delete token by token string. 
 *
 * @SG
 *
 * @string $token
 * @return boolean
 */
	public function delete($token) {
		//debug($token); die;
		
		return $this->deleteAll(array(
			'token' =>  $token
		));
	}
	
/**
 * Return the value stored or false if the token can not be found.
 *
 * @param string
 * @return mixed
 */
	public function get($token) {
		$this->garbage();
		$token = $this->findByToken($token);
		if ($token) {
			return unserialize($token[$this->alias]['data']);
		}
		
		return false;
	}

/**
 * Remove all tokens older than $time days.
 *
 * @var $time
 * @return boolean
 */
	public function garbage($time = 1) {
		return $this->deleteAll(array(
			'created < INTERVAL -' . (int)$time . ' DAY + NOW()'
		));
	}
	
}