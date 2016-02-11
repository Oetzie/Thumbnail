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
		public $properties = array();

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
		 * @param Array $scriptProperties.
		 * @return Boolean.
		 */
		public function setScriptProperties($scriptProperties = array()) {
			$properties = array();
			
			if (isset($scriptProperties['input'])) {
				$properties['image'] = $scriptProperties['input'];
			}
			
			if (isset($scriptProperties['options'])) {
				foreach (explode('&', ltrim($scriptProperties['options'], '&')) as $option) {
					if (false !== strstr($option, '=')) {
						list($key, $option) = explode('=', $option);
						
						$properties[$key] = $option;
					}
				}
			}
			
			$this->properties = array_merge(array(
				'method'	=> 'cover',
				'cache'		=> true,
				'quality'	=> 75,
				'filter'	=> ''
			), $properties);
			
			return true;
		}
		
		/**
		 * @acces public.
		 * @param String $input.
		 * @param Array $properties.
		 * @return String.
		 */
		public function run($input, $properties = array()) {
			$this->setScriptProperties(array_merge(array(
				'input' => $input
			), $properties));
			
			if (file_exists($this->properties['image']) || strstr($this->properties['image'], 'http')) {
				$image 		= $this->properties['image'];

				$basename 	= substr($image, strrpos($image, '/') + 1, strlen($image));
				$extension 	= strtolower(substr($basename, strrpos($basename, '.') + 1, strlen($basename)));
				$name		= substr($basename, 0, strrpos($basename, '.'));
				$path		= substr($image, 0, strrpos($image, '/') + 1);

				if (in_array($extension, array('jpg', 'jpeg', 'png', 'gif'))) {
					$path 		= $this->getImagePath($path);
					$sizes 		= $this->getImageSizes($image);
					$tmpname 	= $this->getImageTmpName($name, $extension);

					if (!$this->getImageCache($image, $path.$tmpname)) {
						$source = imagecreatefromstring(file_get_contents($image));
						imagealphablending($source, true);

						foreach ($this->properties['filter'] as $filter) {
							if (is_array($filter)) {
								imagefilter($source, $filter['filter'], array_shift($filter), array_shift($filter), array_shift($filter), array_shift($filter));
							}
						}
						
						$thumbnail = imagecreatetruecolor($sizes['width'], $sizes['height']);
						
						imagealphablending($thumbnail, false);
						imagesavealpha($thumbnail, true); 
						imagefill($thumbnail, 0, 0, imagecolorallocatealpha($thumbnail, 0, 0, 0, 127));
	
						imagecopyresampled($thumbnail, $source, $sizes['position_left'], $sizes['position_top'], 0, 0, $sizes['resize_width'], $sizes['resize_height'], $sizes['image_width'], $sizes['image_height']);
					
						switch ($extension) {
							case 'jpg':
							case 'jpeg':
								$result = imagejpeg($thumbnail, $path.$tmpname, (int) $this->properties['quality']);
								
								break;
							case 'png':
								$result = imagepng($thumbnail, $path.$tmpname, round((9 / 100) * (int) $this->properties['quality']));
								
								break;
							case 'gif':
								$result = imagegif($thumbnail, $path.$tmpname);
								
								break;
						}
						
						imagedestroy($source);
						imagedestroy($thumbnail);
						
						if (!$result) {
							return $image;
						}
					}
					
					if ((bool) $this->config['use_tinyfi']) {
						$cTmpname = $this->getImageTmpName($name, $extension, array('compressed'));
						
						if (!$this->getImageCache($image, $path.$cTmpname)) {
							if ($compressed = $this->getImageCompression($image, file_get_contents($path.$tmpname))) {
								if (copy($compressed, $path.$cTmpname)) {
									$tmpname = $cTmpname;
								} else {
									return $image;
								}
							}
						} else {
							$tmpname = $cTmpname;
						}
					}
					
					return rtrim(substr($path, strlen(rtrim($this->modx->getOption('base_path'), '/')) + 1, strlen($path)), '/').'/'.$tmpname;
				}				
			}
			
			return $this->properties['image'];
		}
		
		/**
		 * @acces protected.
		 * @param String $path.
		 * @param Integer $chmod.
		 * @return String.
		 */
		protected function getImagePath($path, $chmod = 0755) {
			$output	= rtrim($this->modx->getOption('base_path'), '/').'/';
			
			if (false === strstr($path, 'http')) {
				if ((bool) $this->properties['cache']) {
					$path = rtrim($this->config['cache_path'], '/').'/'.rtrim($path, '/').'/';
				} else {
					$path = rtrim($path, '/').'/';
				}
			} else {
				$path = rtrim($this->config['cache_path'], '/').'/';
			}
			
			foreach (array_filter(explode('/', $path)) as $part) {
				$output .= $part.'/';
				
				if (!is_dir($output)) {
					if (!mkdir($output, $chmod)) {
						$this->modx->log(modX::LOG_LEVEL_ERROR, 'Thumbnail, could not create cache directory "'.$output.'".');
					
						return false;
					}
				} else if (substr(decoct(fileperms($output)), 2) != decoct($chmod)) {
					if (version_compare(PHP_VERSION, '5.5.0', '<')) {
						if (!chmod($output, $chmod)) {
							$this->modx->log(modX::LOG_LEVEL_ERROR, 'Thumbnail, could not chmod cache directory "'.$output.'".');
							
							return false;
						}
					}
				}
			}
			
			return $output;
		}
		
		/**
		 * @acces protected.
		 * @param String $image.
		 * @return Array.
		 */
		protected function getImageSizes($image) {
			list($imageWidth, $imageHeight) = getimagesize($image);
			
			$sizes = array(
				'image_width'	=> $imageWidth,
				'image_height'	=> $imageHeight,
				'resize_width'	=> $imageWidth,
				'resize_height'	=> $imageHeight,
				'width'			=> isset($this->properties['width']) ? $this->properties['width'] : $imageWidth,
				'height'		=> isset($this->properties['height']) ? $this->properties['height'] : $imageHeight,
				'position_top'	=> 0,
				'position_left'	=> 0
			);
			
			switch (strtolower($this->properties['method'])) {
				case 'scale':
					if (isset($this->properties['width'])) {
						if ($sizes['resize_width'] > $sizes['width']) {
							$sizes['resize_height']	= ceil($sizes['resize_height'] * ($sizes['width'] / $sizes['resize_width']));
							$sizes['resize_width'] 	= $sizes['width'];
						}
						
						$sizes['height'] = $sizes['resize_height'];
					} else if (isset($this->properties['height'])) {
						if ($sizes['resize_height'] > $sizes['height']) {
							$sizes['resize_width']	= ceil($sizes['resize_width'] * ($sizes['height'] / $sizes['resize_height']));
							$sizes['resize_height'] 	= $sizes['height'];
						}
						
						$sizes['width'] = $sizes['resize_width'];
					}
					
					break;
				case 'fit':
					if ($sizes['resize_width'] > $sizes['width']) {
						$sizes['resize_height']	= ceil($sizes['resize_height'] * ($sizes['width'] / $sizes['resize_width']));
						$sizes['resize_width'] 	= $sizes['width'];
					}
					
					if ($sizes['resize_height'] > $sizes['height']) {
						$sizes['resize_width']	= ceil($sizes['resize_width'] * ($sizes['height'] / $sizes['resize_height']));
						$sizes['resize_height'] 	= $sizes['height'];
					}
					
					break;
				case 'cover':
					if ($sizes['resize_width'] > $sizes['width']) {
						$sizes['resize_height']	= ceil($sizes['resize_height'] * ($sizes['width'] / $sizes['resize_width']));
						$sizes['resize_width'] 	= $sizes['width'];
					}
					
					if ($sizes['resize_height'] > $sizes['height']) {
						$sizes['resize_width']	= ceil($sizes['resize_width'] * ($sizes['height'] / $sizes['resize_height']));
						$sizes['resize_height'] 	= $sizes['height'];
					}
					
					if ($sizes['resize_width'] < $sizes['width']) {
						$sizes['resize_height']	= ceil($sizes['resize_height'] * ($sizes['width'] / $sizes['resize_width']));
						$sizes['resize_width'] 	= $sizes['width'];
					}
					
					if ($sizes['resize_height'] < $sizes['height']) {
						$sizes['resize_width']	= ceil($sizes['resize_width'] * ($sizes['height'] / $sizes['resize_height']));
						$sizes['resize_height'] 	= $sizes['height'];
					}
				
					break;
			}
			
			$this->properties['width'] 	= $sizes['width'];
			$this->properties['height'] = $sizes['height'];

			$sizes['position_left']	= round(($sizes['width'] - $sizes['resize_width']) / 2);
			$sizes['position_top']	= round(($sizes['height'] - $sizes['resize_height']) / 2);
			
			return $sizes;
		}
		
		/**
		 * @acces protected.
		 * @param String $image.
		 * @param String $tmpimage.
		 * @return Boolean.
		 */
		protected function getImageCache($image, $tmpimage) {
			if (0 != $this->config['cache_lifetime'] && file_exists($tmpimage)) {
				if (-1 == $this->config['cache_lifetime'] && filemtime($tmpimage) > filemtime($image)) {
					return true;
				} else if (filemtime($tmpimage) > (time() - (86400 * $this->config['cache_lifetime']))) {
					return true;
				}
			}
			
			return false;
		}
		
		/**
		 * @acces protected.
		 * @param String $name.
		 * @param String $extension.
		 * @param Array $properties.
		 * @return String.
		 */
		protected function getImageTmpName($name, $extension, $properties = array()) {
			if ((bool) $this->properties['cache']) {
				return sprintf('%s.%s.%s', $name, md5(serialize(array_merge(array(
					'size'		=> $this->properties['width'].'x'.$this->properties['height'],
					'method'	=> $this->properties['method'],
					'quality'	=> $this->properties['quality'],
					'filters'	=> $this->getImageFilters()
				), $properties))), $extension);
			} else {
				return sprintf('%s.%s', $name, $extension);
			}
		}
		
		/**
		 * @acces protected.
		 * @return Array.
		 */
		protected function getImageFilters() {
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
        
			if (isset($this->properties['filter'])) {
				if (is_string($this->properties['filter'])) {
					$this->properties['filter'] = explode(',', $this->properties['filter']);
				}
				
				foreach ($this->properties['filter'] as $key => $filter) {
					$filter = explode('|', $filter);
					$type 	= array_shift($filter);
					
					if (isset($filters[$type])) {
						$this->properties['filter'][$key] = array(
							'filter'	=> $filters[$type]
						) + $filter;
					}
				}
			}
				
			return $this->properties['filter'];
		}
		
		/**
		 * @acces protected.
		 * @param String $image.
		 * @param Mixed $body.
		 * @param Array $header.
		 * @return Mixed.
		 */
		protected function getImageCompression($image, $body = null, $header = array()) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL				=> rtrim($this->config['tinyfi_api_endpoint'], '/'),
	            CURLOPT_BINARYTRANSFER 	=> true,
	            CURLOPT_RETURNTRANSFER 	=> true,
	            CURLOPT_HEADER 			=> true,
	            CURLOPT_HTTPHEADER		=> $header,
	            CURLOPT_USERPWD 		=> 'api:'.$this->config['tinyfi_api_key'],
	            CURLOPT_CAINFO 			=> dirname(__FILE__).'/cert.pem',
	            CURLOPT_SSL_VERIFYPEER 	=> true,
	            CURLOPT_USERAGENT 		=> sprintf('Tinify/%s PHP/%s curl/%s', '1.2.0', PHP_VERSION, curl_version()['version']),
	        ));
	        
	        if ($body) {
		        if (is_array($body)) {
			        $header[] = 'Content-Type: application/json';
			        
			        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			    	curl_setopt($curl, CURLOPT_POSTFIELDS, $this->modx->toJSON($body));   
		        } else {
			    	curl_setopt($curl, CURLOPT_POSTFIELDS, $body);  
		        }
        	} else {
	        	curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        	}
        	
        	$response = curl_exec($curl);
        	
        	if (is_string($response)) {
	        	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				$header = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
				$body 	= substr($response, $header);
				
				curl_close($curl);
				
				$headers = $this->getImageCompressionHeaders(substr($response, 0, $header));
				
				if ($status >= 200 && $status <= 299) {
					$response = $this->modx->fromJSON($body);
					
					if (isset($response['output']['url'])) {
						return $response['output']['url'];
					}
				}
	        }
        	
        	return false;
		}
		
		/**
		 * @acces protected.
		 * @param String $input.
		 * @return Array.
		 */
		protected function getImageCompressionHeaders($input) {
			if (!is_array($header)) {
            	$input = explode("\r\n", $input);
        	}
        	
        	$headers = array();
        	
        	foreach ($input as $header) {
	        	if (!empty($header) && false !== strstr($header, ':')) {
		        	list($key, $value) = explode(':', $header);
		        	
		        	$headers[$key] = $value;
		        }	
        	}
        	
        	return $headers;
		}
	}

?>