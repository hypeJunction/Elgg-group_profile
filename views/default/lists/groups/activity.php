<?php

$entity = elgg_extract('entity', $vars);
$guid = (int) $entity->guid;

$identifier = is_callable('group_subtypes_get_identifier') ? group_subtypes_get_identifier($entity) : 'groups';

$base_url = elgg_normalize_url("$identifier/activity/$entity->guid") . '?' . parse_url(current_page_url(), PHP_URL_QUERY);

$list_class = (array) elgg_extract('list_class', $vars, array());
$list_class[] = 'elgg-list-group-activity';

$item_class = (array) elgg_extract('item_class', $vars, array());

$options = (array) elgg_extract('options', $vars, array());

$list_options = array(
	'full_view' => true,
	'limit' => elgg_extract('limit', $vars, elgg_get_config('default_limit')) ? : 10,
	'list_class' => implode(' ', $list_class),
	'item_class' => implode(' ', $item_class),
	'no_results' => elgg_echo("$identifier:activity:none"),
	'pagination' => !elgg_in_context('widgets'),
	'pagination_type' => 'infinite',
	'base_url' => $base_url,
	'list_id' => "group-activity-$guid",
	'group' => $entity,
);

$dbprefix = elgg_get_config('dbprefix');
$getter_options = array(
	'joins' => array(
		"JOIN {$dbprefix}entities e1 ON e1.guid = rv.object_guid",
		"LEFT JOIN {$dbprefix}entities e2 ON e2.guid = rv.target_guid",
	),
	'wheres' => array(
		"(e1.container_guid = $entity->guid OR e2.container_guid = $entity->guid)",
	),
);

$options = array_merge_recursive($list_options, $options, $getter_options);

if (elgg_view_exists('lists/activity')) {
	$params = $vars;
	$params['options'] = $options;
	$params['group'] = $entity;
	echo elgg_view('lists/activity', $params);
} else {
	echo elgg_list_river($options);
}