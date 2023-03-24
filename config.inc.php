<?php

/**
 * mediadblink module
 * module configuration
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2022-2023 Gustaf Mossakowski
 */


if (wrap_setting('local_access') AND wrap_setting('mediadblink_use_local'))
	wrap_setting('mediadblink_server', wrap_setting('mediadblink_server_local'));
