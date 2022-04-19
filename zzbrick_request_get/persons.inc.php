<?php

/**
 * mediadblink module
 * export persons (only if they participated somewhere) into media database
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2014-2015, 2019, 2021-2022 Gustaf Mossakowski
 */


function mod_mediadblink_get_persons($vars) {
	$sql = 'SELECT DISTINCT person_id AS `objects[foreign_key]`
			, identifier AS `objects[identifier]`
			, "Personen" AS `objects[path]`
			, "person" AS `objects[category]`
			, IF(sex = "female", "woman", IF(sex = "male", "man", NULL)) AS `objects[subcategory]`
			, contact AS `objects[title][---]`
			, identifier AS `objects[title][-id]`
		FROM persons
		JOIN participations USING (person_id)
		JOIN contacts USING (contact_id)
		WHERE first_name != "unbekannt" AND last_name != "unbekannt"';
	$data = wrap_db_fetch($sql, 'objects[foreign_key]');
	
	// @todo E-Mail-Adresse auslesen
	return $data;
}
