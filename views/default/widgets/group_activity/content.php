<?php

/**
 * Group activity widget
 */
$entity = elgg_extract('entity', $vars);

$limit = (int) $entity->num_display;
$guid = (int) $entity->group_guid;

if (!$guid) {
	echo elgg_format_element('p', [
		'class' => 'elgg-text-help',
			], elgg_echo('groups:widget:group_activity:content:noselect'));
	return;
}

// backward compatibility when we couldn't set widget title (pre 1.9)
if (!$entity->title) {
	$title = get_entity($guid)->name;
	$content = "<h3>$title</h3>";
}

echo elgg_view('lists/groups/activity', array(
	'entity' => get_entity($guid),
	'limit' => $limit,
));
