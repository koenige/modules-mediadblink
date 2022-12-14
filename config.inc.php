<?php

/**
 * mediadblink module
 * module configuration
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2022 Gustaf Mossakowski
 */


if (wrap_get_setting('local_access') AND wrap_get_setting('mediadblink_use_local'))
	$zz_setting['mediadblink_server'] = wrap_get_setting('mediadblink_server_local');
