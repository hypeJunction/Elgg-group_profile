<?php
/**
 * Groups latest activity
 *
 * @todo add people joining group to activity
 * 
 * @package Groups
 */

$entity = elgg_extract('entity', $vars);
if (!$entity instanceof ElggGroup || $entity->activity_enable == 'no') {
	return;
}

$identifier = is_callable('group_subtypes_get_identifier') ? group_subtypes_get_identifier($entity) : 'groups';

elgg_push_context('widgets');
$content = elgg_view('lists/groups/activity', array(
	'entity' => $entity,
));
elgg_pop_context();

$all_link = elgg_view('output/url', array(
	'href' => "$identifier/activity/$entity->guid",
	'text' => elgg_echo('link:view:all'),
	'is_trusted' => true,
));

echo elgg_view('groups/profile/module', array(
	'title' => elgg_echo("$identifier:activity"),
	'content' => $content,
	'all_link' => $all_link,
));
