<?php

echo elgg_view('groups/sidebar/my_status', $vars);

$entity = elgg_extract('entity', $vars, elgg_get_page_owner_entity());
if (!elgg_group_gatekeeper(false, $entity->guid)) {
	return;
}

echo elgg_view('groups/sidebar/members', $vars);

if (elgg_is_active_plugin('search')) {
	echo elgg_view('groups/sidebar/search', $vars);
}
