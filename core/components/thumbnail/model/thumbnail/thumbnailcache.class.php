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
		public function __construct(modX &$modx, array $config = array()) {
			$this->modx =& $modx;

			$corePath 		= $this->modx->getOption('thumbnail.core_path', $config, $this->modx->getOption('core_path').'components/thumbnail/');
			$assetsUrl 		= $this->modx->getOption('thumbnail.assets_url', $config, $this->modx->getOption('assets_url').'components/thumbnail/');
			$assetsPath 	= $this->modx->getOption('thumbnail.assets_path', $config, $this->modx->getOption('assets_path').'components/thumbnail/');
		
			$this->config = array_merge(array(
				'namespace'				=> $this->modx->getOption('namespace', $config, 'thumbnail'),
				'helpurl'				=> $this->modx->getOption('helpurl', $config, 'thumbnail'),
				'language'				=> 'thumbnail:default',
				'base_path'				=> $corePath,
				'core_path' 			=> $corePath,
				'model_path' 			=> $corePath.'model/',
				'processors_path' 		=> $corePath.'processors/',
				'elements_path' 		=> $corePath.'elements/',
				'chunks_path' 			=> $corePath.'elements/chunks/',
				'cronjobs_path' 		=> $corePath.'elements/cronjobs/',
				'plugins_path' 			=> $corePath.'elements/plugins/',
				'snippets_path' 		=> $corePath.'elements/snippets/',
				'assets_path' 			=> $assetsPath,
				'js_url' 				=> $assetsUrl.'js/',
				'css_url' 				=> $assetsUrl.'css/',
				'assets_url' 			=> $assetsUrl,
				'connector_url'			=> $assetsUrl.'connector.php',
				'use_tinyfi'			=> $this->modx->getOption('thumbnail.use_tinyfy'),
				'tinyfi_api_endpoint'	=> $this->modx->getOption('thumbnail.tinyfy_api_endpoint'),
				'tinyfi_api_key'		=> $this->modx->getOption('thumbnail.tinyfy_api_key'),
				'cache_path'			=> $this->modx->getOption('thumbnail.cache_path'),
				'cache_lifetime'		=> $this->modx->getOption('thumbnail.cache_lifetime'),
				'clear_cache'			=> $this->modx->getOption('thumbnail.clear_cache')
			), $config);
			
			$this->modx->addPackage('thumbnail', $this->config['model_path']);
		}
		
		/**
		 * @acces public.
		 * @return Boolean.
		 */
		public function run() {
			$this->modx->lexicon->load($this->config['language']);
			
			if ((bool) $this->config['clear_cache']) {
				$path = rtrim($this->modx->getOption('base_path'), '/').'/'.rtrim($this->config['cache_path'], '/').'/';
				
				if (false !== ($clear = $this->clearPath($path))) {
					$this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('thumbnail.clear_cache'));
				}
				
				return $clear;
			}
			
			return true;
		}
		
		/**
		 * @acces public.
		 * @param String $path.
		 * @return Boolean.
		 */
		protected function clearPath($path) {
			$path = rtrim($path, '/').'/';
			$base = rtrim($this->modx->getOption('base_path'), '/').'/'.rtrim($this->config['cache_path'], '/').'/';
			
			if (is_dir($path)) {
				foreach (scandir($path) as $value) {
					if (!in_array($value, array('.', '..'))) {
						if ('dir' == filetype($path.$value)){
							$this->clearPath($path.$value);
						} else { 
							if (!unlink($path.$value)) {
								$this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('thumbnail.clear_cache_error_file', array(
									'value'	=> $value
								)));
							}
						}
					}
				}
				
				if ($path != $base) {
					if (!rmdir($path)) {
						$this->modx->log(modX::LOG_LEVEL_INFO, $this->modx->lexicon('thumbnail.clear_cache_error_path', array(
							'value'	=> $value
						)));
					}
				}
			}
			
			return true;
		}
	}

?>