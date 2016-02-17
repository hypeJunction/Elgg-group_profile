<?php
/**
 * Search for content in this group
 *
 * @uses vars['entity'] ElggGroup
 */

$entity = elgg_extract('entity', $vars, elgg_get_page_owner_entity());

if (!elgg_group_gatekeeper(false, $entity->guid)) {
	return;
}

$identifier = is_callable('group_subtypes_get_identifier') ? group_subtypes_get_identifier($entity) : 'groups';

$url = elgg_get_site_url() . 'search';
$body = elgg_view_form("$identifier/search", array(
	'action' => $url,
	'method' => 'get',
	'disable_security' => true,
), $vars);

echo elgg_view_module('aside', elgg_echo("$identifier:search_in_group"), $body);