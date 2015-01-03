<?php

	/**
	 * Thumbnail
	 *
	 * Copyright 2014 by Oene Tjeerd de Bruin <info@oetzie.nl>
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

	require_once $modx->getOption('thumbnail.core_path', null, $modx->getOption('core_path').'components/thumbnail/').'/model/thumbnail/thumbnail.class.php';

	$thumbnail = new Thumbnail($modx, $scriptProperties);

	if (false !== ($thumb = $thumbnail->create($input, $options))) {
		$modx->setPlaceholders(array(
			'thumbnail'			=> $thumb['file'],
			'thumbnail.width'	=> $thumb['width'],
			'thumbnail.height'	=> $thumb['height']
		));

		return $thumb['file'];
	}

	return $input;

?>