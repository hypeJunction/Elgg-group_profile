<?php

/**
 * Group members sidebar
 *
 * @package ElggGroups
 *
 * @uses $vars['entity'] Group entity
 * @uses $vars['limit']  The number of members to display
 */

$entity = elgg_extract('entity', $vars, elgg_get_page_owner_entity());

if (!elgg_group_gatekeeper(false, $entity->guid)) {
	return;
}

$identifier = is_callable('group_subtypes_get_identifier') ? group_subtypes_get_identifier($entity) : 'groups';

$limit = elgg_extract('limit', $vars, 14);

$body = elgg_list_entities_from_relationship(array(
	'relationship' => 'member',
	'relationship_guid' => (int) $entity->guid,
	'inverse_relationship' => true,
	'type' => 'user',
	'limit' => $limit,
	'order_by' => 'r.time_created DESC',
	'pagination' => false,
	'list_type' => 'gallery',
	'gallery_class' => 'elgg-gallery-users',
		));

$all_link = elgg_view('output/url', array(
	'href' => "$identifier/members/$entity->guid",
	'text' => elgg_echo("$identifier:members:more"),
		));

$footer = elgg_format_element('div', [
	'class' => 'center',
		], $all_link);

echo elgg_view_module('aside', elgg_echo("$identifier:members"), $body, array(
	'footer' => $footer,
));
