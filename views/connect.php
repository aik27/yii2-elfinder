<?php
/**
 * @var array $options
 * @var array $plugin
 */

define('ELFINDER_IMG_PARENT_URL', \aik27\elfinder\Assets::getPathUrl());

// run elFinder
$connector = new elFinderConnector(new \aik27\elfinder\elFinderApi($options, $plugin));
$connector->run();