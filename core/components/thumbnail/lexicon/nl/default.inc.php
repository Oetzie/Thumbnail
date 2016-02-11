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
	
	$_lang['setting_thumbnail.use_tinyfy']						= 'Gebruik Tinyfy';
	$_lang['setting_thumbnail.use_tinyfy_desc']					= 'Gebruik Tinyfy om de afbeeldingen te comprimeren zodat deze kleiner opgeslagen worden. <strong>Let op:</strong> hierdoor duurt het genereren van afbeeldingen langer.';
	$_lang['setting_thumbnail.tinyfy_api_key']					= 'Tinyfy API sleutel';
	$_lang['setting_thumbnail.tinyfy_api_key_desc']				= 'De Tinyfy API sleutel voor de authenticatie van de Tinyfy API om de afbeeldingen te comprimeren.';
	$_lang['setting_thumbnail.tinyfy_api_endpoint']				= 'Tinyfy API URL';
	$_lang['setting_thumbnail.tinyfy_api_endpoint_desc']		= 'De URL van de Tinyfy API om de afbeeldingen te comprimeren.';
	$_lang['setting_thumbnail.cache_path']						= 'Cache locatie';
	$_lang['setting_thumbnail.cache_path_desc']					= 'De locatie waar alle afbeeldingen opgeslagen worden.';
	$_lang['setting_thumbnail.cache_lifetime']					= 'Cache levensduur';
	$_lang['setting_thumbnail.cache_lifetime_desc']				= 'Het aantal dagen dat een afbeelding bewaard moet blijven, hierna word de afbeelding automatisch vernieuwd. Gebruik "0" om geen gebruik te maken van de cache, gebruik "-1" om de afbeelding voor oneindig te cachen tenzij de officiële afbeelding vernieuwd is. Standaard is "-1".';
	$_lang['setting_thumbnail.clear_cache']						= 'Cache vernieuwen';
	$_lang['setting_thumbnail.clear_cache_desc']				= 'De afbeeldingen cache vernieuwen als de website cache vernieuwd word. Standaard is "Nee".';
	
	$_lang['thumbnail_snippet_method_desc']						= 'De methode die gebruikt word om de afbeeldingen te schalen, dit kan "scale", "fit" of "cover" zijn. Standaard is "cover".';
	$_lang['thumbnail_snippet_cache_desc']						= 'Indien ja, word de afbeelding opgeslagen in de tijdelijke cache locatie met een tijdelijke naam.';
	$_lang['thumbnail_snippet_quality_desc']					= 'De kwaliteit van de afbeelding, dit kan is een getal tussen de "0" en "100" zijn waarvoor "100" voor een hoge kwaliteit staat. Standaard is "75".';
	
	$_lang['thumbnail.clear_cache']								= 'Afbeeldingen cache vernieuwen: Vernieuwen geslaagd.';
	$_lang['thumbnail.clear_cache_error_file']					= 'Afbeeldingen cache vernieuwen: Afbeelding "[[+value]]" kon niet vernieuwd worden.';
	$_lang['thumbnail.clear_cache_error_path']					= 'Afbeeldingen cache vernieuwen: Cache locatie "[[+value]]" kon niet vernieuwd worden.';
	
?>