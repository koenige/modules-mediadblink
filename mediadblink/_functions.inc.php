<?php

/**
 * mediadblink module
 * common functions, always available
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2021-2022 Gustaf Mossakowski
 */


/**
 * get media from media database, public website folder
 *
 * @param mixed $identifier (string or array)
 * @param array $filter (optional) direct filter on database export, e. g. meta=something
 * @param string $category (optional) restrict results to category_identifier of meta data
 * @param mixed $ids (optional) restrict results to foreign key IDs
 */
function mf_mediadblink_media($identifier, $filter = [], $category = '', $ids = []) {
	if (!is_array($identifier)) $identifier = [$identifier];
	array_unshift($identifier, wrap_get_setting('mediadblink_public_website_path'));
	return mf_mediadblink_media_get($identifier, $filter, $category, $ids);
}

/**
 * get media from media database
 *
 * @param mixed $identifier (string or array)
 * @param array $filter (optional) direct filter on database export, e. g. meta=something
 * @param string $category (optional) restrict results to category_identifier of meta data
 * @param mixed $ids (optional) restrict results to foreign key IDs
 */
function mf_mediadblink_media_get($identifier, $filter = [], $category = '', $ids = []) {
	global $zz_setting;

	// @todo read corresponding value from languages table
	switch ($zz_setting['lang']) {
		case 'de': $lang3 = 'deu'; break;
		case 'en': default: $lang3 = 'eng'; break;
	}

	if (is_array($identifier)) $identifier = implode('/', $identifier);
	if ($identifier_prefix = wrap_get_setting('mediadblink_export_url_identifier_prefix')) {
		$identifier = explode('/', $identifier);
		array_splice($identifier, 1, 0, [$identifier_prefix]);
		$identifier = implode('/', $identifier);
	}
	$filter = $filter ? '&'.http_build_query($filter) : '';
	$url = sprintf(wrap_get_setting('mediadblink_export_url'), $identifier, $lang3, $filter);

	$settings = [];
	if (!str_starts_with($identifier, wrap_get_setting('mediadblink_public_website_path').'/')) {
		$settings['headers_to_send'][] = sprintf('Authorization: Bearer %s'
			, wrap_get_setting('mediadblink_access_token')
		);
	}
	require_once $zz_setting['core'].'/syndication.inc.php';
	$media = wrap_syndication_get($url, 'json', $settings);
	unset($media['_']); // metadata

	$foreign_keys = !is_array($ids) ? [$ids] : $ids;
	$matches = [];
	foreach ($media as $medium) {
		foreach ($medium['meta'] as $meta) {
			if ($foreign_keys AND !in_array($meta['foreign_key'], $foreign_keys)) continue;
			if ($category AND $meta['category_identifier'] !== $category) continue;
			if (empty($medium['base_filename']))
				mf_mediadblink_media_report_missing($medium);
			$key = $foreign_keys ? $meta['foreign_key'] : $medium['object_id'];
			$matches[$key] = $medium;
			continue 2; // might be more than one meta object
		}
	}
	return $matches;
}

/**
 * report if a medium is missing
 *
 * @param array $medium
 * @return void
 */
function mf_mediadblink_media_report_missing($medium) {
	if (in_array($medium['category'], wrap_get_setting('mediadblink_no_preview_categories'))) return;
	$filetype_def = wrap_filetypes($medium['filetype']);
	if (empty($filetype_def['thumbnail'])) return;

	wrap_error(sprintf('Preview images missing in Media Database for object ID %d, identifier %s', $medium['object_id'], $medium['identifier']));
}

/**
 * get sizes for thumbnail images depending on image, dimension and size
 *
 * @param array $image
 * @param string $dimension (width, height)
 * @return string
 */
function mf_mediadblink_image_size($image, $dimension) {
	$size = substr($image['large_filename'], strrpos($image['large_filename'], '.') + 1);
	if (!is_numeric($size)) return '';
	switch ($dimension) {
	case 'width':
		if ($image['image_ratio'] >= 1) return $size;
		return round($size / $image['image_ratio']);
	case 'height':
		if ($image['image_ratio'] < 1) return $size;
		return round($size / $image['image_ratio']);
	}
	return '';
}
