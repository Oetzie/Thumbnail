<?php

	/**
	 * Thumbnail
	 *
	 * Copyright 2016 by Oene Tjeerd de Bruin <info@oetzie.nl>
	 *
	 * This file is part of Thumbnail, a real estate property listings component
	 * for MODX Revolution.
	 *
	 * Thumbnail is free software; you can redistribute it and/or modify it under
	 * the terms of the GNU General Public License as published by the Free Software
	 * Foundation; either version 2 of the License, or (at your option) any later
	 * version.
	 *
	 * Thumbnail is distributed in the hope that it will be useful, but WITHOUT ANY
	 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
	 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License along with
	 * Thumbnail; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
	 * Suite 330, Boston, MA 02111-1307 USA
	 */

	$_lang['thumbnail'] 										= 'Thumbnail';
		
	$_lang['area_thumbnail']									= 'Thumbnail';
	
	$_lang['setting_thumbnail.use_tinyfy']						= 'Use Tinyfy';
	$_lang['setting_thumbnail.use_tinyfy_desc']					= 'Use Tinyfy to compress the images so the images will be saved smaller. <strong>Pay attention:</strong> as a result, it takes longer to generate images.';
	$_lang['setting_thumbnail.tinyfy_api_key']					= 'Tinyfy API key';
	$_lang['setting_thumbnail.tinyfy_api_key_desc']				= 'The Tinyfy API key for authenticating the Tinyfy API to compress the images.';
	$_lang['setting_thumbnail.tinyfy_api_endpoint']				= 'Tinyfy API URL';
	$_lang['setting_thumbnail.tinyfy_api_endpoint_desc']		= 'The URL of the Tinyfy API o compress the images.';
	$_lang['setting_thumbnail.cache_path']						= 'Cache location';
	$_lang['setting_thumbnail.cache_path_desc']					= 'The location where all images will be saved.';
	$_lang['setting_thumbnail.cache_lifetime']					= 'Cache lifetime';
	$_lang['setting_thumbnail.cache_lifetime_desc']				= 'The number of days that an image need to be saved, after this the image will be refreshed automatically. Use "0" for not using the cache, use "-1" to cache the image infinity unless the original image has been updated. Default is "-1".';
	$_lang['setting_thumbnail.clear_cache']						= 'Refresh cache';
	$_lang['setting_thumbnail.clear_cache_desc']				= 'Refresh the image cache if the website cache has been refreshed. Default is "No".';
	
	$_lang['thumbnail_snippet_method_desc']						= 'The method to scale the images, this can be "scale", "fit" or "cover". Default is "cover".';
	$_lang['thumbnail_snippet_cache_desc']						= 'If yes, the image will be saved in the cache with a temporary name.';
	$_lang['thumbnail_snippet_quality_desc']					= 'The quality of the image, this is a number between the "0" and "100" where "100" stands for high quality. Default is "75".';
	
	$_lang['thumbnail.clear_cache']								= 'Refresh image cache: Refresh succeed.';
	$_lang['thumbnail.clear_cache_error_file']					= 'Refresh image cache: Image "[[+value]]" could not be refreshed.';
	$_lang['thumbnail.clear_cache_error_path']					= 'Refresh image cache: Cache location "[[+value]]" could not be refreshed.';
	
?>