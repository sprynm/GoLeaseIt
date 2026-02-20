<?php 
$this->extend('Administration.Common/edit-page');

$this->start('actionLinks');
echo $this->AdminLink->link(__('Back to Submissions'), array('action' => 'index', $this->request->data['EmailFormSubmission']['email_form_id']));
$this->end('actionLinks');

$this->set('header', 'Submission from ' . $this->request->data['EmailForm']['EmailForm']['name']);
?>
<div id="email-submission">
	
<p><strong>Submitted: </strong><?php echo $this->Time->format('D F jS, Y, h:ia', $this->request->data['EmailFormSubmission']['created']); ?>
			<?php 
					if (!empty($this->request->data['EmailFormSubmission']['data']['Email'])) {
						echo ' by ' . $this->request->data['EmailFormSubmission']['data']['Email'] ;	
					} else if (!empty($this->request->data['EmailFormSubmission']['data']['email_address'])) {
						echo ' by ' . $this->request->data['EmailFormSubmission']['data']['email_address'] ;	
					} else if (!empty($this->request->data['EmailFormSubmission']['data']['email'])) {
						echo ' by ' . $this->request->data['EmailFormSubmission']['data']['email'] ;	
					} else if (!empty($this->request->data['EmailFormSubmission']['data']['Email Address'])) {
						echo ' by ' . $this->request->data['EmailFormSubmission']['data']['email'] ;	
					} 
			?></p>
<hr />			
<h2>Detail</h2>
		<?php 	
		unset($this->request->data['EmailFormSubmission']['data']['g-recaptcha-response']);
		unset($this->request->data['EmailFormSubmission']['data']['uses_recaptcha']);
		
			
		foreach ($this->request->data['EmailFormSubmission']['data'] as $key => $val):
			if(substr($key, -3) == '_id') {
				unset($this->request->data['EmailFormSubmission']['data'][$key]);
				continue;
			}
		
			if(is_array($val)){
				$list = '<ul>';
				foreach($val as $k => $v) {
					if($k === "name") {
						$list .= '<li>' . $this->Html->link($v, DS .'uploads' . DS . 'email_forms' . DS . Inflector::slug($key) . DS . $v) . '</li>';
						break;
 					}		
					$list .= '<li>' . $v . '</li>';
				}
				$list .= '</ul>';
				$val = $list;
			}
			
			echo $this->Html->tag(
				'p',
				'<strong>' . Inflector::humanize($key) . ': </strong>' . $val
			);
		endforeach;
		?>
	
</div>