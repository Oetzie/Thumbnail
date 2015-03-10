<?php

	/**
	 * Thumbnail
	 *
	 * Copyright 2013 by Oene Tjeerd de Bruin <info@oetzie.nl>
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
	
	$_lang['setting_thumbnail_assets_dir']						= 'Assets directory.';
	$_lang['setting_thumbnail_assets_dir_desc']					= 'The location (directory) where the images are saved. Default "assets/".';
	$_lang['setting_thumbnail_cache_dir']						= 'Cache directory.';
	$_lang['setting_thumbnail_cache_dir_desc']					= 'The location (directory) where the thumbnails will be saved. Default "assets/thumbnails/".';
	$_lang['setting_thumbnail_cache_expires']					= 'Expiretime cache';
	$_lang['setting_thumbnail_cache_expires_desc']				= 'The number of seconds that a thumbnail needs to be cached. Default "604800".';
	$_lang['setting_thumbnail_cache_expires_desc']				= 'HThe number of seconds that a thumbnail needs to be cached. Use "0" for no cache, cache for always unless the official image is changed use "-1". Default "-1".';
	$_lang['setting_thumbnail_tmp_name']						= 'Hash thumbnail name';
	$_lang['setting_thumbnail_tmp_name_desc']					= 'Hash the settings together in the thumbnail filename. Default "Yes".';
	$_lang['setting_thumbnail_clear_cache']						= 'Clear thumbnail cache';
	$_lang['setting_thumbnail_clear_cache_desc']				= 'Clear the thumbnail cache if the websites cache clears. Default "No".';
	
	$_lang['thumbnail_snippet_fullsize_desc']					= 'Make the image fullscreen in the thumbnail. Default "Yes".';
	$_lang['thumbnail_snippet_position_desc']					= 'The position of the image in the thumbnail. Default "center" and can be "topleft, top, topright, right, bottomright, bottom, bottomleft, left or center".';
	$_lang['thumbnail_snippet_quality_desc']					= 'The image quality of the thumbnail. Default "100".';
	
	$_lang['thumbnail.clear_cache']								= 'Empty the thumbnails cache: Refreshing succeed!';
	
?>