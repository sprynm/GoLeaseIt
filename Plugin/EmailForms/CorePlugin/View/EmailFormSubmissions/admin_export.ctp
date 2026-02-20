<?php
$this->Csv->addRow(array("Email form submissions for: " . $emailForm['EmailForm']['name']));
$this->Csv->addRow(array("Generated on: " . $generated));
$this->Csv->addRow(array("Total Count: " . count($submissions)));
$this->Csv->addRow(array());

$headers = array('submitted');
$headers = array_merge($headers, array_keys($submissions[0]['EmailFormSubmission']['data']));
$this->Csv->addRow($headers);

foreach ($submissions as $submission) {
	$line = array($submission['EmailFormSubmission']['created']);
	
	foreach ($submission['EmailFormSubmission']['data'] as $key => $val) {
		$line[] = $val;
	}

	$this->Csv->addRow($line);
}

$this->Csv->setFilename($filename);
echo $this->Csv->render();
?>