<?php

/**
 * Group status for logged in user
 *
 * @package ElggGroups
 *
 * @uses $vars['entity'] Group entity
 */

$identifier = elgg_extract('identifier', $vars, 'groups');

$menu_params = $vars;
$menu_params['sort_by'] = 'priority';
$menu_params['class'] = 'elgg-menu-page';

$body = elgg_view_menu('groups:my_status', $menu_params);
echo elgg_view_module('aside', elgg_echo("$identifier:my_status"), $body);
