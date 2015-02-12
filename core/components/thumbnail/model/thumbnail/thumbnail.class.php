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

	class Thumbnail {
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
		 * @var Array.
		 */
		public $customConfig = array();
		
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
				'elementsPath' 			=> $corePath.'elements/',
				'snippetsPath' 			=> $corePath.'elements/snippets/',
				'helpurl'				=> 'thumbnail',
				'assetsDir'				=> $this->modx->getOption('thumbnail_assets_dir'),
				'cacheDir'				=> $this->modx->getOption('thumbnail_cache_dir'),
				'cacheExpires'			=> $this->modx->getOption('thumbnail_cache_expires'),
				'md5TmpName'			=> $this->modx->getOption('thumbnail_tmp_name'),
				'fullScreen'			=> 1,
				'position'				=> 'center',
				'quality'				=> 100
			), $config);

			$this->modx->addPackage('thumbnail', $this->config['modelPath']);
			
			$this->modx->lexicon->load('thumbnail:default');
		}
		
		/**
		 * @acces protected.
		 * @param String $customConfig.
		 */
		protected function setCustomConfig($customConfig) {
			foreach (explode('&', trim($customConfig, '&')) as $key => $value) {
				if (false !== strstr($value, '=')) {
					$value = explode('=', $value, 2);
		
					$this->customConfig[array_shift($value)] = array_shift($value);
				}
			}
			
			$this->config = array_merge($this->config, $this->customConfig);
		}
		
		/**
		 * @acces protected.
		 * @param String $dir.
		 * @param Integer $chmod.
		 * @return String.
		 */
		protected function getDirectory($directory, $chmod = 0755) {
			$output = '';
			
			$directory = trim(preg_replace('/([\/\\\])+/si', '/', $directory), '/');
			
			foreach (explode('/', $directory) as $value) {
				$output .= $value.'/';
				
				$dir = $this->modx->getOption('base_path').$output;
				
				if (!is_dir($dir)) {
					if (!mkdir($dir, $chmod)) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, '[Thumbnail] Could not create directory "'.$dir.'".');
					
						return false;
					}
				} else if (substr(decoct(fileperms($dir)), 2) != decoct($chmod)) {
					if (!chmod($dir, $chmod)) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, '[Thumbnail] Could not chmod directory "'.$dir.'".');
					
						return false;
					}
				}
			}

			return $this->modx->getOption('base_path').$output;
		}
		
		/**
		 * @acces public.
		 * @param String $input.
		 * @param String $config.
		 * @return Array.
		 */
		public function create($input, $config) {
			$this->setCustomConfig($config);
			
			if (false !== ($file = $this->getFileInfo($input))) {
				if (in_array($file['mime'], array('image/png', 'image/gif', 'image/jpg', 'image/jpeg'))) {
					$size = $this->calculateSizes($file);
				
					if (false !== $this->getFileThumbnail($file)) {
						$image = imagecreatefromstring(file_get_contents($file['file']));
						imagealphablending($image, true);
						
						$image = $this->setFilters($image);
						
						$thumbnail = imagecreatetruecolor($size['maxWidth'], $size['maxHeight']);
						
						imagealphablending($thumbnail, false);
						imagesavealpha($thumbnail, true);  
	
						imagecopyresampled($thumbnail, $image, $size['left'], $size['top'], 0, 0, $size['newWidth'], $size['newHeight'], $size['orgWidth'], $size['orgHeight']);
						
						switch ($file['mime']) {
							case 'image/png':
								$result = imagepng($thumbnail, $file['tmpfile'], round((9 / 100) * $this->config['quality']));
								break;
							case 'image/gif':
								$result = imagegif($thumbnail, $file['tmpfile']);
								break;
							case 'image/jpg':
							case 'image/jpeg':
								$result = imagejpeg($thumbnail, $file['tmpfile'], $this->config['quality']);
								break;
						}
	
						imagedestroy($image);
						
						if ($result) {
							return array(
								'file'		=> substr($file['tmpfile'], strlen(rtrim($this->modx->getOption('base_path'), '/')) + 1, strlen($file['tmpfile'])),
								'width'		=> $size['maxWidth'],
								'height'	=> $size['maxHeight']
							);
						}
					} else {
						return array(
							'file'		=> substr($file['tmpfile'], strlen(rtrim($this->modx->getOption('base_path'), '/')) + 1, strlen($file['tmpfile'])),
							'width'		=> $size['maxWidth'],
							'height'	=> $size['maxHeight']
						);
					}
				} else if ('directory' != $file['mime']) {
					$this->modx->log(modX::LOG_LEVEL_ERROR, '[Thumbnail] Not supported mime-type "'.$file['mime'].'".');
				}
				
			}
			
			return false;
		}
		
		/**
		 * @acces public.
		 * @param Resource $image.
		 * @return Resource.
		 */
		protected function setFilters($image) {
			if (false !== ($filtersStr = $this->modx->getOption('filters', $this->config, false))) {
				$filters = array(
                    'negate'			=> IMG_FILTER_NEGATE,
                    'grayscale'			=> IMG_FILTER_GRAYSCALE,
                    'brightness'		=> IMG_FILTER_BRIGHTNESS,
                    'contrast'			=> IMG_FILTER_CONTRAST,
                    'colorize'			=> IMG_FILTER_COLORIZE,
                    'edgedetect'        => IMG_FILTER_EDGEDETECT,
                    'emboss'            => IMG_FILTER_EMBOSS,
                    'gaussianblur' 		=> IMG_FILTER_GAUSSIAN_BLUR,
                    'selectiveblur'   	=> IMG_FILTER_SELECTIVE_BLUR,
                    'removal'           => IMG_FILTER_MEAN_REMOVAL,
                    'smooth'            => IMG_FILTER_SMOOTH,
                    'pixelate'          => IMG_FILTER_PIXELATE
                );
                
                foreach (explode(',', $filtersStr) as $filter) {
	                $filter = explode('|', $filter);
	                $filterKey = array_shift($filter);

	                if (isset($filters[$filterKey])) {
                        imagefilter($image, $filters[$filterKey], array_shift($filter), array_shift($filter), array_shift($filter), array_shift($filter));
                    }
                }
			}
			
			return $image;
		}
		
		/**
		 * @acces protected.
		 * @param String $file.
		 * @return Array.
		 */
		protected function getFileInfo($file) {
			$file = ltrim(preg_replace('/([\/\\\])+/si', '/', $file), '/');
			
			$directory = trim(preg_replace('/([\/\\\])+/si', '/', $this->config['assetsDir']), '/');
			
			if (0 === ($pos = strpos($file, $directory))) {
				$file = trim(substr($file, strlen($directory), strlen($file)), '/');
			}
			
			$fullPathFile = rtrim($this->modx->getOption('base_path'), '/').'/'.$directory.'/'.$file;

			if (file_exists($fullPathFile)) {
				return array_merge(array(
					'file'		=> $fullPathFile,
					'tmpfile'	=> $this->getFileTmp($file),
					'mime'		=> finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fullPathFile)
				), pathinfo($fullPathFile));
			}
			
			return false;
		}
		
		/**
		 * @acces protected.
		 * @param String $file.
		 * @return String.
		 */
		protected function getFileTmp($file) {
			$directory = '';
			
			if (false !== ($pos = strrpos($file, '/'))) {
				$directory = substr($file, 0, $pos);
				$file = substr($file, $pos + 1, strlen($file));
			}
			
			list($file, $extension) = explode('.', $file);
			
			
			$tmpName = (bool) $this->config['md5TmpName'] ? '.'.md5(serialize($this->customConfig)) : '';
		
			return $this->getDirectory($this->config['cacheDir'].$directory).$file.$tmpName.'.'.$extension;
		}
		
		/**
		 * @acces protected.
		 * @param Array $file.
		 * @return Boolean.
		 */
		protected function getFileThumbnail($file) {
			if (0 == $this->config['cacheExpires']) {
				return true;
			} else if (!is_file($file['tmpfile']) || filemtime($file['file']) > filemtime($file['tmpfile'])) {
				if (!is_file($file['tmpfile'])) {
					return true;
				} else {
					if (-1 != $this->config['cacheExpires'] && filemtime($file['tmpfile']) < time() + $this->config['cacheExpires']) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		/**
		 * @acces protected.
		 * @param Array $file.
		 * @return Array.
		 */
		protected function calculateSizes($file) {
			list($width, $height) = getimagesize($file['file']);
			
			$type = array(
				'landscape' => $width, 
				'portrait' 	=> $height
			);
			
			$size = array(
				'orgWidth'	=> $width,
				'orgHeight'	=> $height,
				'maxWidth'	=> $this->modx->getOption('w', $this->config, $this->modx->getOption('mW', $this->config, $width)),
				'maxHeight'	=> $this->modx->getOption('h', $this->config, $this->modx->getOption('mH', $this->config, $width)),
				'newWidth'	=> $width,
				'newHeight'	=> $height,
				'type'		=> array_search(max($type), $type),
				'position'	=> $this->modx->getOption('position', $this->config, 'center'),
				'mime'		=> $file['mime']
			);
			
			if ($size['newWidth'] > $size['maxWidth']) {
				$size['newHeight']	= ceil($size['newHeight'] * ($size['maxWidth'] / $size['newWidth']));
				$size['newWidth'] 	= $size['maxWidth'];
			}
			
			if ($size['newHeight'] > $size['maxHeight']) {
				$size['newWidth']	= ceil($size['newWidth'] * ($size['maxHeight'] / $size['newHeight']));
				$size['newHeight'] 	= $size['maxHeight'];
			}
			
			if (false !== ($maxWidth = $this->modx->getOption('mW', $this->config, false))) {
				$size['maxWidth']	= $size['newWidth'];
			}
			
			if (false !== ($maxHeight = $this->modx->getOption('mH', $this->config, false))) {
				$size['maxHeight']	= $size['newHeight'];
			}
			
			if ((bool) $this->modx->getOption('fullScreen', $this->config, true)) {
				if ($size['newWidth'] < $size['maxWidth']) {
					$size['newHeight']	= ceil($size['newHeight'] * ($size['maxWidth'] / $size['newWidth']));
					$size['newWidth'] 	= $size['maxWidth'];
				}
				
				if ($size['newHeight'] < $size['maxHeight']) {
					$size['newWidth']	= ceil($size['newWidth'] * ($size['maxHeight'] / $size['newHeight']));
					$size['newHeight'] 	= $size['maxHeight'];
				}
			}
			
			switch (strtolower($size['position'])) {
				case 'topleft':
					$size['top']	= 0;
					$size['left']	= 0;
					break;
				case 'top':
					$size['top']	= 0;
					$size['left']	= round(($size['maxWidth'] - $size['newWidth']) / 2);
					break;
				case 'topright':
					$size['top']	= 0;
					$size['left']	= round($size['maxWidth'] - $size['newWidth']);
					break;
				case 'right':
					$size['top']	= round(($size['maxHeight'] - $size['newHeight']) / 2);
					$size['left']	= round($size['maxWidth'] - $size['newWidth']);
					break;
				case 'bottomright':
					$size['top']	= round($size['maxHeight'] - $size['newHeight']);
					$size['left']	= round($size['maxWidth'] - $size['newWidth']);
					break;
				case 'bottom':
					$size['top']	= round($size['maxHeight'] - $size['newHeight']);
					$size['left']	= round(($size['maxWidth'] - $size['newWidth']) / 2);
					break;
				case 'bottomleft':
					$size['top']	= round($size['maxHeight'] - $size['newHeight']);
					$size['left']	= 0;
					break;
				case 'left':
					$size['top']	= round(($size['maxHeight'] - $size['newHeight']) / 2);
					$size['left']	= 0;
					break;
				default:
					$size['top']	= round(($size['maxHeight'] - $size['newHeight']) / 2);
					$size['left']	= round(($size['maxWidth'] - $size['newWidth']) / 2);
					break;
			}
			
			return $size;
		}
	}
	
?>