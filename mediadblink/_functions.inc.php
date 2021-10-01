<?php

/**
 * mediadblink module
 * common functions, always available
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2021 Gustaf Mossakowski
 */


/**
 * get media from media database
 *
 * @param string $event
 * @param string $folder
 * @param string $category
 * @param string $foreign_key
 */
function mf_mediadblink_media($event, $folder, $category, $foreign_key) {
	global $zz_setting;
	$zz_setting['brick_cms_input'] = 'json';
	$url = sprintf($zz_setting['mediadblink_website'], $event, $folder);
	$media = brick_request_external($url, $zz_setting);
	unset($media['_']); // metadata

	$matches = [];
	foreach ($media as $medium) {
		foreach ($medium['meta'] as $meta) {
			if ($meta['foreign_key'] !== $foreign_key) continue;
			if ($meta['category_identifier'] !== $category) continue;
			if (empty($medium['base_filename'])) {
				wrap_error(sprintf('Preview images missing in Media Database for object ID %d, identifier %s', $medium['object_id'], $medium['identifier']));
			}
			$matches[] = $medium;
		}
	}
	return $matches;
}
