<style>
@import url(https://fonts.googleapis.com/css?family=Open+Sans);

#container {
	background-color: rgba(255, 255, 255, 0.7);
	padding: 40px;
	font-family: 'Open Sans', sans-serif;	
	border-radius: 20px;
}
</style>


<h2>Scheduled Maintenance currently underway.</h2>
<p class="error">
	Something has gone wrong. We have logged the error and the site administrator has been made aware of the problem. If you would like further information, or wish to report a special circumstance, please use the contact information below. We apologize for any inconvenience this may cause.
</p>
<p class="error">
	If you need technical support, please contact <a href="http://www.radarhill.com">Radar Hill</a> at:
		<ul class="contact-info">
			<li class="label">Phone: <span itemprop="telephone">250.477.6395</span></li>
			<li class="label">Toll-Free: <span itemprop="telephone">1.866.477.6395</span></li>
			<li class="label">Email: 
				<a href="mailto:info@radarhill.com" title="info@radarhill.com" onClick="_gaq.push([&#039;_trackEvent&#039;, &#039;email&#039;, &#039;info@radarhill.com&#039;, &#039;500 error&#039;]);" itemprop="email">info@radarhill.com</a>
			</li>   
		</ul>
</p>

<?php CakeLog::write( '500errors', $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);  ?>

<?php
if (Configure::read('debug') > 0 ):
	echo $this->element('exception_stack_trace');
endif;
?>
