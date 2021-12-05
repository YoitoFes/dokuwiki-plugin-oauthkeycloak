<?php
/**
 * Options for the oauthgeneric plugin
 */

$meta['key'] = array('string');
$meta['secret'] = array('password');

$meta['authurl'] = array('string');
$meta['tokenurl'] = array('string');
$meta['userurl'] = array('string');
$meta['authmethod'] = array('multichoice', '_choices' => [0, 1, 6, 2, 3, 4, 5]);
$meta['scopes'] = array('array');

$meta['json-user'] = array('string');
$meta['json-name'] = array('string');
$meta['json-mail'] = array('string');
$meta['json-grps'] = array('string');

$meta['label'] = array('string');
$meta['color'] = array('string');
