<?php
$this->Html->script('Metas.ajax', array('inline' => false, 'once' => true));

$metaKeys = $this->Meta->keys();
foreach ($metaKeys as $i => $key):
	echo $this->Form->input('MetaValue.' . $i . '.id', array(
		'type' => 'hidden',
		'value' => isset($this->request->data['MetaValue'][$i]['id']) ? $this->request->data['MetaValue'][$i]['id'] : null
	));
	echo $this->Form->input('MetaValue.' . $i . '.meta_key_id', array(
		'type' => 'hidden',
		'value' => $i
	));
	echo $this->Form->input('MetaValue.' . $i . '.val', array(
		'label' => $key['MetaKey']['name'],
		'value' => isset($this->request->data['MetaValue'][$i]['val']) ? $this->request->data['MetaValue'][$i]['val'] : null,
		'type' => $key['MetaKey']['type']
	));
endforeach;
?>