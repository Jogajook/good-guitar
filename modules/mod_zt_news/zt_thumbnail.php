<?php
/**
 * @package ZT News Module for Joomla! 2.5.0
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2011- ZooTemplate.com
 * @license PHP files are GNU/GPL
**/
if(!class_exists('ZTThumbnail')){
	class ZTThumbnail { 
		var $errmsg; 
		var $error; 
		var $format; 
		var $fileName;  
		var $currentDimensions; 
		var $newDimensions;  
		var $oldImage; 
		var $workingImage;  
		var $maxWidth; 
		var $maxHeight;
		var $options;
		function ZTThumbnail($fileName) { 
			if(!function_exists("gd_info")) {
				echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
				echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
				exit;
			} 
			$this->errmsg               = '';
			$this->error                = false;
			$this->currentDimensions    = array();
			$this->newDimensions        = array();
			$this->fileName             = $fileName;  
			$this->maxWidth             = 0;
			$this->maxHeight            = 0;
	
			if(!file_exists($this->fileName)) {
				$this->errmsg = 'File not found';
				$this->error = true;
			} 
			elseif(!is_readable($this->fileName)) {
				$this->errmsg = 'File is not readable';
				$this->error = true;
			} 
			if($this->error == false) { 
				if(stristr(strtolower($this->fileName),'.gif')) $this->format = 'GIF'; 
				elseif(stristr(strtolower($this->fileName),'.jpg') || stristr(strtolower($this->fileName),'.jpeg')) $this->format = 'JPG'; 
				elseif(stristr(strtolower($this->fileName),'.png')) $this->format = 'PNG'; 
				else {
					$this->errmsg = 'Unknown file format';
					$this->error = true;
				}
			}
			$defaultOptions = array 
			(
				'resizeUp'				=> false,
				'jpegQuality'			=> 100,
				'correctPermissions'	=> false,
				'preserveAlpha'			=> true,
				'alphaMaskColor'		=> array (255, 255, 255),
				'preserveTransparency'	=> true,
				'transparencyMaskColor'	=> array (0, 0, 0)
			);
			$this->options = $defaultOptions;
			if($this->error == false) {
				switch($this->format) {
					case 'GIF':
						$this->oldImage = ImageCreateFromGif($this->fileName);
						break;
					case 'JPG':
						$this->oldImage = ImageCreateFromJpeg($this->fileName);
						break;
					case 'PNG':
						$this->oldImage = ImageCreateFromPng($this->fileName);
						break;
				} 
				$size = GetImageSize($this->fileName);
				$this->currentDimensions = array('width'=>$size[0],'height'=>$size[1]); 
			} 
		} 
		function destruct() { 
			if(is_resource($this->oldImage)) @ImageDestroy($this->oldImage);
			if(is_resource($this->workingImage)) @ImageDestroy($this->workingImage);
		}  
		function showErrorImage() {
			header('Content-type: image/png');
			$errImg = ImageCreate(220,25);
			$bgColor = imagecolorallocate($errImg,0,0,0);
			$fgColor1 = imagecolorallocate($errImg,255,255,255);
			$fgColor2 = imagecolorallocate($errImg,255,0,0);
			imagestring($errImg,3,6,6,'Error:',$fgColor2);
			imagestring($errImg,3,55,6,$this->errmsg,$fgColor1);
			imagepng($errImg);
			imagedestroy($errImg);
		}
		function resize ($maxWidth = 0, $maxHeight = 0)
		{ 
			if (!is_numeric($maxWidth))
			{
				throw new InvalidArgumentException('$maxWidth must be numeric');
			}
			
			if (!is_numeric($maxHeight))
			{
				throw new InvalidArgumentException('$maxHeight must be numeric');
			}
			 
			if ($this->options['resizeUp'] === false)
			{
				$this->maxHeight	= (intval($maxHeight) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $maxHeight;
				$this->maxWidth		= (intval($maxWidth) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $maxWidth;
			}
			else
			{
				$this->maxHeight	= intval($maxHeight);
				$this->maxWidth		= intval($maxWidth);
			}
			 
			$this->calcImageSize($this->currentDimensions['width'], $this->currentDimensions['height']);
			 
			if (function_exists('imagecreatetruecolor'))
			{
				$this->workingImage = imagecreatetruecolor($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
			}
			else
			{
				$this->workingImage = imagecreate($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
			}
			
			$this->preserveAlpha();
			
			imagecopyresampled
			(
				$this->workingImage,
				$this->oldImage,
				0,
				0,
				0,
				0,
				$this->newDimensions['newWidth'],
				$this->newDimensions['newHeight'],
				$this->currentDimensions['width'],
				$this->currentDimensions['height']
			);
	
			// update all the variables and resources to be correct
			$this->oldImage 					= $this->workingImage;
			$this->currentDimensions['width'] 	= $this->newDimensions['newWidth'];
			$this->currentDimensions['height'] 	= $this->newDimensions['newHeight'];
			
			return $this;
		}
		function resize_image($width,$height,$crop=true,$aspect=true){ 
			if (!is_numeric($width) || $width  == 0)
			{
				throw new InvalidArgumentException('$width must be numeric and greater than zero');
			}
			
			if (!is_numeric($height) || $height == 0)
			{
				throw new InvalidArgumentException('$height must be numeric and greater than zero');
			} 
			if ($this->options['resizeUp'] === false)
			{
				$this->maxHeight	= (intval($height) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $height;
				$this->maxWidth		= (intval($width) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $width;
			}
			else
			{
				$this->maxHeight	= intval($height);
				$this->maxWidth		= intval($width);
			}
			
			$this->calcImageSizeStrict($this->currentDimensions['width'], $this->currentDimensions['height']);
			 
			$this->resize($this->newDimensions['newWidth'], $this->newDimensions['newHeight']);
			 
			if ($this->options['resizeUp'] === false)
			{
				$this->maxHeight	= (intval($height) > $this->currentDimensions['height']) ? $this->currentDimensions['height'] : $height;
				$this->maxWidth		= (intval($width) > $this->currentDimensions['width']) ? $this->currentDimensions['width'] : $width;
			}
			else
			{
				$this->maxHeight	= intval($height);
				$this->maxWidth		= intval($width);
			} 
			if (function_exists('imagecreatetruecolor'))
			{
				$this->workingImage = imagecreatetruecolor($this->maxWidth, $this->maxHeight);
			}
			else
			{
				$this->workingImage = imagecreate($this->maxWidth, $this->maxHeight);
			}
			
			$this->preserveAlpha();
			
			$cropWidth	= $this->maxWidth;
			$cropHeight	= $this->maxHeight;
			$cropX 		= 0;
			$cropY 		= 0; 
			if ($this->currentDimensions['width'] > $this->maxWidth)
			{
				$cropX = intval(($this->currentDimensions['width'] - $this->maxWidth) / 2);
			}
			elseif ($this->currentDimensions['height'] > $this->maxHeight)
			{
				$cropY = intval(($this->currentDimensions['height'] - $this->maxHeight) / 2);
			}
			
			imagecopyresampled
			(
				$this->workingImage,
				$this->oldImage,
				0,
				0,
				$cropX,
				$cropY,
				$cropWidth,
				$cropHeight,
				$cropWidth,
				$cropHeight
			); 
			$this->oldImage 					= $this->workingImage;
			$this->currentDimensions['width'] 	= $this->maxWidth;
			$this->currentDimensions['height'] 	= $this->maxHeight;
		} 
		function save ($fileName, $format = null)
		{ 
			$validFormats = array('GIF', 'JPG', 'PNG');
			$format = ($format !== null) ? strtoupper($format) : $this->format;
			if (!in_array($format, $validFormats))
			{
				throw new InvalidArgumentException ('Invalid format type specified in save function: ' . $format);
			} 
			if (!is_writeable(dirname($fileName)))
			{ 
				if ($this->options['correctPermissions'] === true)
				{
					@chmod(dirname($fileName), 0777); 
					if (!is_writeable(dirname($fileName)))
					{
						throw new RuntimeException ('File is not writeable, and could not correct permissions: ' . $fileName);
					}
				} 
				else
				{
					throw new RuntimeException ('File not writeable: ' . $fileName);
				}
			}
			
			switch ($format) 
			{
				case 'GIF':
					imagegif($this->oldImage, $fileName);
					break;
				case 'JPG':
					imagejpeg($this->oldImage, $fileName, $this->options['jpegQuality']);
					break;
				case 'PNG':
					imagepng($this->oldImage, $fileName);
					break;
			}
			
			return $this;
		}
		function calcWidth ($width, $height)
		{
			$newWidthPercentage	= (100 * $this->maxWidth) / $width;
			$newHeight			= ($height * $newWidthPercentage) / 100;
			
			return array
			(
				'newWidth'	=> intval($this->maxWidth),
				'newHeight'	=> intval($newHeight)
			);
		} 
		function calcHeight ($width, $height)
		{
			$newHeightPercentage	= (100 * $this->maxHeight) / $height;
			$newWidth 				= ($width * $newHeightPercentage) / 100;
			
			return array
			(
				'newWidth'	=> ceil($newWidth),
				'newHeight'	=> ceil($this->maxHeight)
			);
		} 
		function calcImageSizeStrict ($width, $height)
		{ 
			if ($this->maxWidth >= $this->maxHeight)
			{ 
				if ($width > $height)
				{
					$newDimensions = $this->calcHeight($width, $height);
					
					if ($newDimensions['newWidth'] < $this->maxWidth)
					{
						$newDimensions = $this->calcWidth($width, $height);
					}
				}
				elseif ($height >= $width)
				{
					$newDimensions = $this->calcWidth($width, $height);
					
					if ($newDimensions['newHeight'] < $this->maxHeight)
					{
						$newDimensions = $this->calcHeight($width, $height);
					}
				}
			}
			elseif ($this->maxHeight > $this->maxWidth)
			{
				if ($width >= $height)
				{
					$newDimensions = $this->calcWidth($width, $height);
					
					if ($newDimensions['newHeight'] < $this->maxHeight)
					{
						$newDimensions = $this->calcHeight($width, $height);
					}
				}
				elseif ($height > $width)
				{
					$newDimensions = $this->calcHeight($width, $height);
					
					if ($newDimensions['newWidth'] < $this->maxWidth)
					{
						$newDimensions = $this->calcWidth($width, $height);
					}
				}
			} 
			$this->newDimensions = $newDimensions;
		}
		protected function calcImageSize ($width, $height)
		{
			$newSize = array
			(
				'newWidth'	=> $width,
				'newHeight'	=> $height
			);
			
			if ($this->maxWidth > 0)
			{
				$newSize = $this->calcWidth($width, $height);
				
				if ($this->maxHeight > 0 && $newSize['newHeight'] > $this->maxHeight)
				{
					$newSize = $this->calcHeight($newSize['newWidth'], $newSize['newHeight']);
				}
			}
			
			if ($this->maxHeight > 0)
			{
				$newSize = $this->calcHeight($width, $height);
				
				if ($this->maxWidth > 0 && $newSize['newWidth'] > $this->maxWidth)
				{
					$newSize = $this->calcWidth($newSize['newWidth'], $newSize['newHeight']);
				}
			}
			
			$this->newDimensions = $newSize;
		}
		 
		function preserveAlpha ()
		{ 
			if ($this->format == 'PNG')
			{
				imagealphablending($this->workingImage, false);
				
				$colorTransparent = imagecolorallocatealpha
				(
					$this->workingImage, 
					$this->options['alphaMaskColor'][0], 
					$this->options['alphaMaskColor'][1], 
					$this->options['alphaMaskColor'][2], 
					0
				);
				
				imagefill($this->workingImage, 0, 0, $colorTransparent);
				
				imagesavealpha($this->workingImage, true);
			} 
			if ($this->format == 'GIF')
			{
				$colorTransparent = imagecolorallocate
				(
					$this->workingImage, 
					$this->options['transparencyMaskColor'][0], 
					$this->options['transparencyMaskColor'][1], 
					$this->options['transparencyMaskColor'][2] 
				);
				
				imagecolortransparent($this->workingImage, $colorTransparent);
				imagetruecolortopalette($this->workingImage, true, 256);
			}
		}
	}
}
?>