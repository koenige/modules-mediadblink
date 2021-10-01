<?php

/**
 * mediadblink module
 * export organisations (contacts) into media database
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2015-2017, 2021 Gustaf Mossakowski
 */


/**
 * @todo diese Funktion wird noch nicht produktiv genutzt
 */
function mod_mediadblink_get_organisations($vars) {
	global $zz_setting;

	$sql = 'SELECT contacts.contact_id AS `objects[foreign_key]`
			, identifier AS `objects[identifier]`
			, contact AS `objects[title][deu]`
			, "organization" AS `objects[category]`
			, "Org" AS `objects[path]`

			, (SELECT identification FROM contactdetails
				WHERE contactdetails.contact_id = contacts.contact_id
				AND provider_category_id = %d
				LIMIT 1
			)  AS `uris[0][uri]`
			, "/uri/web" AS `uris[0][uri_property]`
			, CONCAT(contacts.contact_id, "-", 0)  AS `uris[0][foreign_key]`

		FROM contacts
	';
	$sql = sprintf($sql, wrap_category_id('provider/website'));
	// @todo add coordinates
	$data = wrap_db_fetch($sql, 'objects[foreign_key]');
	foreach ($data as $id => $object) {
		if (!$object['uris[0][uri]']) {
			unset($data[$id]['uris[0][uri]']);
			unset($data[$id]['uris[0][uri_property]']);
			unset($data[$id]['uris[0][foreign_key]']);
		}
	}

	return $data;
}
