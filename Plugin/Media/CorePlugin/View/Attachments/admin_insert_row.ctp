<?php
echo $this->element('Media.attachment_row', array(
	'item' => $item,
	'assocAlias' => $assocAlias,
	'count' => $count,
	'model' => $model,
	'plug' => $plug,
	'troller' => $troller
));