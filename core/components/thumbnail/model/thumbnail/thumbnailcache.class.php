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

	class ThumbnailCache {
		/**
		 * @acces public.
		 * @var Object.
		 */
		public $modx;
		
		/**
		 * @acces public.
		 * @var Array.
		 */
		public $config = array();
		
		/**
		 * @acces public.
		 * @param Object $modx.
		 * @param Array $config.
		 */
		function __construct(modX &$modx, array $config = array()) {
			$this->modx =& $modx;
			
			$corePath 		= $this->modx->getOption('thumbnail.core_path', $config, $this->modx->getOption('core_path').'components/thumbnail/');
			$assetsUrl 		= $this->modx->getOption('thumbnail.assets_url', $config, $this->modx->getOption('assets_url').'components/thumbnail/');
			$assetsPath 	= $this->modx->getOption('thumbnail.assets_path', $config, $this->modx->getOption('assets_path').'components/thumbnail/');
			
			$this->config = array_merge(array(
				'basePath'				=> $corePath,
				'corePath' 				=> $corePath,
				'modelPath' 			=> $corePath.'model/',
				'assetsDir'				=> $this->modx->getOption('thumbnail_assets_dir'),
				'cacheDir'				=> $this->modx->getOption('thumbnail_cache_dir'),
				'cacheExpires'			=> $this->modx->getOption('thumbnail_cache_expires')
			), $config);
			
			$this->modx->lexicon->load('thumbnail:default');
		}
		
		/**
		 * @acces public.
		 * @param String $directory.
		 * @return Boolean.
		 */
		public function clean($directory) {
			if (false !== ($clean = $this->cleanDirectory($directory))) {
				$this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('thumbnail.clear_cache'));
			}
			
			return $clean;
		}
		
		/**
		 * @acces public.
		 * @param String $directory.
		 * @return Boolean.
		 */
		protected function cleanDirectory($directory) {
			$directory = rtrim(preg_replace('/([\/\\\])+/si', '/', $directory), '/');

			if (is_dir($directory)) {
				foreach (scandir($directory) as $object) {
					if (!in_array($object, array('.', '..'))) {
						if ('dir' == filetype($directory.'/'.$object)){
							$this->cleanDirectory($directory.'/'.$object);
						} else { 
							if (unlink($directory.'/'.$object)) {
								//$this->modx->log(modX::LOG_LEVEL_INFO, '[Thumbnail] Clear cache file "'.$object.'".');
							}
						}
					}
				}
				
				if (rmdir($directory)) {
					//$this->modx->log(modX::LOG_LEVEL_INFO, '[Thumbnail] Clear cache directory "'.$directory.'".');
				}
			}
			
			return true;
		}
	}
	
?>