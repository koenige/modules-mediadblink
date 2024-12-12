<?php

/**
 * mediadblink module
 * common functions, always available
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/mediadblink
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2021-2024 Gustaf Mossakowski
 */


/**
 * get media from media database, public website folder
 *
 * @param mixed $identifier (string or array)
 * @param array $filter (optional) direct filter on database export, e. g. meta=something
 * @param string $class (optional) restrict results to class_identifier of meta data
 * @param mixed $ids (optional) restrict results to foreign key IDs
 */
function mf_mediadblink_media($identifier, $filter = [], $class = '', $ids = []) {
	if (!is_array($identifier)) $identifier = [$identifier];
	array_unshift($identifier, wrap_setting('mediadblink_public_website_path'));
	return mf_mediadblink_media_get($identifier, $filter, $class, $ids);
}

/**
 * get media from media database
 *
 * @param mixed $identifier (string or array)
 * @param array $filter (optional) direct filter on database export, e. g. meta=something
 * @param string $class (optional) restrict results to class_identifier of meta data
 * @param mixed $ids (optional) restrict results to foreign key IDs
 */
function mf_mediadblink_media_get($identifier, $filter = [], $class = '', $ids = []) {
	// @todo read corresponding value from languages table
	switch (wrap_setting('lang')) {
		case 'de': $lang3 = 'deu'; break;
		case 'en': default: $lang3 = 'eng'; break;
	}

	if (is_array($identifier)) $identifier = implode('/', $identifier);
	if ($identifier_prefix = wrap_setting('mediadblink_export_url_identifier_prefix')) {
		$identifier = explode('/', $identifier);
		array_splice($identifier, 1, 0, [$identifier_prefix]);
		$identifier = implode('/', $identifier);
	}
	$filter = $filter ? '&'.http_build_query($filter) : '';
	$url = sprintf(wrap_setting('mediadblink_export_url'), $identifier, $lang3, $filter);

	$settings = [];
	if (!str_starts_with($identifier, wrap_setting('mediadblink_public_website_path').'/')) {
		$settings['headers_to_send'][] = sprintf('Authorization: Bearer %s'
			, wrap_setting('mediadblink_access_token')
		);
	}
	wrap_include('syndication', 'zzwrap');
	$media = wrap_syndication_get($url, 'json', $settings);
	unset($media['_']); // metadata
	if (!$media) return [];

	$foreign_keys = !is_array($ids) ? [$ids] : $ids;
	$matches = [];
	foreach ($media as $medium) {
		foreach ($medium['meta'] as $meta) {
			if ($foreign_keys AND !in_array($meta['foreign_key'], $foreign_keys)) continue;
			if ($class AND $meta['class_identifier'] !== $class) continue;
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
	if (in_array($medium['class_identifier'], wrap_setting('mediadblink_no_preview_classes'))) return;
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
