<?php
if (!isset($documentData)) {
    $documentData = array();
}
if (!isset($channelData)) {
    $channelData = array();
	// $channelData = array(   'title' => Configure::read('Settings.Site.name'),
		// 'link' => Router::url('/', true),
		// 'description' => __("Most recent posts."),
		// 'language' => 'en-us',
	// ));
}

if (!isset($channelData['title'])) {
    $channelData['title'] = Configure::read('Settings.Site.name');
}

//mail('shannon@radarhill.com', '!!', print_r($content_for_layout, true));
$channel = $this->Rss->channel(array(), $channelData, $content_for_layout);
echo $this->Rss->document($documentData, $channel);