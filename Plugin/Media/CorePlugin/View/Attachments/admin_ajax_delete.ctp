<?php 
echo json_encode( array(
	'notification' => $this->Session->flash()
	, 'id' => $ids
));
?>