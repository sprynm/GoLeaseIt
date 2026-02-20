<?php
$paginatorUrl = $this->passedArgs;

if (!empty($this->request->params['prefix'])) {
	$paginatorUrl[$this->request->params['prefix']] = true;
}

if (isset($this->request->params['category'])) {
	$paginatorUrl['category'] = $this->request->params['category'];
}

if (isset($this->request->params['instance'])) {
	$paginatorUrl['instance'] = $this->request->params['instance'];
}

$prevNext = $paginatorUrl;
unset($prevNext['page']);

if ($this->Paginator->params()['pageCount'] > 1) :
?>
<div class="pagination">
	<?php //
	//
	$prevNext	= am($prevNext, array('?' => array('search' => $this->request->query('search'))));
	//
	$paginatorUrl	= am($paginatorUrl, array('?' => array('search' => $this->request->query('search'))));
		//
		echo $this->Paginator->first('First', array('url' => $prevNext), null, array('class'=>'disabled'));
		if ($this->Paginator->hasPrev()):
			echo $this->Paginator->prev('Previous', array('url' => $prevNext), null, array('class'=>'disabled'));
		endif;
		echo $this->Paginator->numbers(array('url' => $paginatorUrl, 'separator' => false, 'class' => 'number'));
		if ($this->Paginator->hasNext()):
			echo $this->Paginator->next('Next', array('url' => $prevNext), null, array('class'=>'disabled'));
		endif;
		echo $this->Paginator->last('Last', array('url' => $prevNext), null, array('class'=>'disabled'));
	?>
</div>
<?php
endif;
?>