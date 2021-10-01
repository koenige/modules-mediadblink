<?php

/**
 * mediadblink module
 * export participants for events into media database
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2014, 2017, 2019-2021 Gustaf Mossakowski
 */


/**
 * @todo diese Funktion wird noch nicht produktiv genutzt
 * Unterordner DSJ/2013/dem/Photos etc. erstellen
 */
function mod_mediadblink_get_participants($vars) {
	// Team
//	wrap_category_id('organisatoren');

	// Spieler
	wrap_id('usergroups', 'spieler');
	
	// Teilnehmer
	wrap_id('usergroups', 'teilnehmer');

	// Teilnehmer müssen als Personen unter /Personen stehen
	// werden hier nur verknüpft, analog zu Veranstaltungen/Teilnehmer auf media.REIFF

	return false;
	return $data;
}
