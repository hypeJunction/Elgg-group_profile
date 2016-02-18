<?php

/**
 * Group Profile
 *
 * @author Ismayil Khayredinov <info@hypejunction.com>
 * @copyright Copyright (c) 2016, Ismayil Khayredinov
 */
require_once __DIR__ . '/autoloader.php';

elgg_register_event_handler('init', 'system', 'group_profile_init');

/**
 * Initialize the plugin
 * @return void
 */
function group_profile_init() {

	elgg_register_plugin_hook_handler('route', 'groups', 'group_profile_router', 999);
	
	elgg_register_plugin_hook_handler('register', 'menu:groups:my_status', 'group_profile_my_status_menu_setup');
}

/**
 * Route groups pages
 *
 * @param string $hook   "route"
 * @param string $type   "groups"
 * @param array  $return Identifier and segments
 * @param array  $params Hook params
 * @return array
 */
function group_profile_router($hook, $type, $return, $params) {

	if (!is_array($return)) {
		return;
	}

	// Initial page identifier might be different from /groups
	// i.e. subtype specific handler e.g. /schools
	$initial_identifier = elgg_extract('identifier', $params);
	$identifier = elgg_extract('identifier', $return);
	$segments = elgg_extract('segments', $return);

	if ($identifier !== 'groups') {
		return;
	}

	$page = array_shift($segments);
	if (!$page) {
		$page = 'all';
	}

	// we want to pass the original identifier to the resource view
	// doing this via route hook in order to keep the page handler intact
	$resource_params = array(
		'identifier' => $initial_identifier ? : 'groups',
	);

	switch ($page) {
		case 'profile':
			$guid = array_shift($segments);
			$resource_params['guid'] = $guid;
			elgg_push_context('group_profile');
			elgg_set_page_owner_guid($guid);
			break;

		case 'activity' :
			$guid = array_shift($segments);
			$resource_params['guid'] = $guid;
			break;

		default :
			return;
	}

	elgg_load_library('elgg:groups');

	$resource_params['page'] = $page;
	$resource_params['segments'] = $segments;
	echo elgg_view_resource("groups/$page", $resource_params);
	return false;
}

/**
 * Setup My status menu
 *
 * @param string         $hook   "register"
 * @param string         $type   "menu:groups:my_status"
 * @param ElggMenuItem[] $return Menu
 * @param array          $params Hook params
 * @return ElggMenuItem[]
 */
function group_profile_my_status_menu_setup($hook, $type, $return, $params) {

	$entity = elgg_extract('entity', $params, elgg_get_page_owner_entity());
	$user = elgg_get_logged_in_user_entity();

	if (!$entity instanceof ElggGroup || !$user instanceof ElggUser) {
		return;
	}

	$identifier = is_callable('group_subtypes_get_identifier') ? group_subtypes_get_identifier($entity) : 'groups';
	
	if ($entity->owner_guid == $user->guid) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'membership_status',
					'text' => '<a>' . elgg_echo("$identifier:my_status:group_owner") . '</a>',
					'href' => false,
		));
	} else if ($entity->isMember($user)) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'membership_status',
					'text' => '<a>' . elgg_echo("$identifier:my_status:group_member") . '</a>',
					'href' => false
		));
	} else if (check_entity_relationship($user->guid, 'membership_request', $entity->guid)) {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'membership_status',
					'text' => '<a>' . elgg_echo("$identifier:my_status:membership_request_pending") . '</a>',
					'href' => false
		));
	} else {
		$return[] = ElggMenuItem::factory(array(
					'name' => 'membership_status',
					'text' => $entity->isPublicMembership() ? elgg_echo("$identifier:join") : elgg_echo("$identifier:joinrequest"),
					'href' => "/action/groups/join?group_guid={$entity->guid}",
					'is_action' => true
		));
	}

	if (elgg_is_active_plugin('notifications') && $entity->isMember($user)) {
		$subscribed = false;
		$NOTIFICATION_HANDLERS = _elgg_services()->notifications->getMethods();
		foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
			$relationship = check_entity_relationship($user->guid, 'notify' . $method, $entity->guid);
			if ($relationship) {
				$subscribed = true;
				break;
			}
		}
		if ($subscribed) {
			$return[] = ElggMenuItem::factory(array(
				'name' => 'subscription_status',
				'text' => elgg_echo("$identifier:subscribed"),
				'href' => "notifications/group/$user->username",
				'is_action' => true
			));
		} else {
			$return[] = ElggMenuItem::factory(array(
				'name' => 'subscription_status',
				'text' => elgg_echo("$identifier:unsubscribed"),
				'href' => "notifications/group/$user->username"
			));
		}
	}

	return $return;
}
