/**
 * mediadblink module
 * SQL updates
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2024 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


/* 2024-08-28-1 */	UPDATE _settings SET setting_key = 'mediadblink_no_preview_classes' WHERE setting_key = 'mediadblink_no_preview_categories';
