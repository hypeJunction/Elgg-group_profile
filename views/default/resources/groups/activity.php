<?php

$guid = elgg_extract('guid', $vars);
elgg_entity_gatekeeper($guid, 'group');

$entity = get_entity($guid);
elgg_set_page_owner_guid($guid);

$identifier = is_callable('group_subtypes_get_identifier') ? group_subtypes_get_identifier($entity) : 'groups';

// pushing context to make it easier to user 'menu:filter' hook
elgg_push_context("$identifier/activity");

elgg_group_gatekeeper();

if ($entity->activity_enable != "yes") {
	register_error(elgg_echo("$identifier:noaccess"));
	forward(REFERRER);
}

$title = elgg_echo("$identifier:activity");

elgg_push_breadcrumb(elgg_echo($identifier), "$identifier/all");
elgg_push_breadcrumb($entity->getDisplayName(), $entity->getURL());
elgg_push_breadcrumb($title);

$vars['entity'] = $entity;
$content = elgg_view('lists/groups/activity', $vars);
$filter = elgg_view('filters/groups/river', $vars);

$params = array(
	'content' => $content,
	'title' => $title,
	'filter' => $filter ? : '',
);

$body = elgg_view_layout('content', $params);
echo elgg_view_page($title, $body);