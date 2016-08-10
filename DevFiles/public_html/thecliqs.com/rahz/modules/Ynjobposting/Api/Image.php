<?php

class Ynjobposting_Api_Image extends Engine_Image_Adapter_Gd
{
	public function _fitInner($srcW, $srcH, $boxW, $boxH)
	{
		if (($delta = $boxW / $srcW) < 1)
		{
			$srcH = $srcH * $delta;
			$srcW = $srcW * $delta;
		}
		if (($delta = $boxH / $srcH) < 1)
		{
			$srcH = $srcH * $delta;
			$srcW = $srcW * $delta;
		}
		$srcW = round($srcW);
		$srcH = round($srcH);
		return array(
			$srcW,
			$srcH
		);
	}

	public function _fitOuter($srcW, $srcH, $boxW, $boxH)
	{
		if ($srcW > $boxW && $srcH > $boxH)
		{
			$delta = $boxW / $srcW;
			$newH = $srcH * $delta;
			$newW = $srcW * $delta;
			if ($newH < $boxH)
			{
				$delta = $boxH / $srcH;
				$newH = $srcH * $delta;
				$newW = $srcW * $delta;
			}
			$srcH = $newH;
			$srcW = $newW;
		}
		$srcW = round($srcW);
		$srcH = round($srcH);
		return array(
			$srcW,
			$srcH
		);
	}

	public function resize_old($width, $height, $aspect = TRUE)
	{
		$arr_size = $this -> _fitInner($this -> _width, $this -> _height, $width, $height);
		$new_width = $arr_size[0];
		$new_height = $arr_size[1];
		$dstX = (int)(($width - $new_width) / 2);
		$dstY = (int)(($height - $new_height) / 2);
		$destination_image = imagecreatetruecolor($width, $height);
		$background = imagecolorallocate($destination_image, 255, 255, 255);
		imagefill($destination_image, 0, 0, $background);

		if (!imagecopyresampled($destination_image, $this -> _resource, $dstX, $dstY, 0, 0, $new_width, $new_height, $this -> _width, $this -> _height))
		{
			imagedestroy($destination_image);
			throw new Engine_Image_Adapter_Exception('Unable to resize image');
		}

		// Now destroy old image and overwrite with new
		imagedestroy($this -> _resource);
		$this -> _resource = $destination_image;
		$this -> _width = $width;
		$this -> _height = $height;
		return $this;
	}

	public function resize($width, $height, $aspect = TRUE)
	{
		$destination_image = imagecreatetruecolor($width, $height);
		$background = imagecolorallocate($destination_image, 255, 255, 255);
		imagefill($destination_image, 0, 0, $background);
		$arr_size = $this -> _fitOuter($this -> _width, $this -> _height, $width, $height);
		$new_width = $arr_size[0];
		$new_height = $arr_size[1];

		$dstX = $dstY = 0;
		$srcX = $srcY = 0;
		if ($new_width <= $width && $new_height <= $height)
		{
			$srcWidth = $this -> _width;
			$srcHeight = $this -> _height;
			$dstX = (int)(($width - $new_width) / 2);
			$dstY = (int)(($height - $new_height) / 2);
		}
		else
		{
			if ($new_width > $width)
			{
				$new_width = $width;
				$multiplier = $new_height / $this -> _height;
				$srcWidth = (int)($new_width / $multiplier);
				$srcHeight = (int)($new_height / $multiplier);
				$srcX = (int)(($this -> _width - $srcWidth) / 2);
				$dstY = (int)(($height - $new_height) / 2);
			}
			else
			{
				// $new_height>$height
				$new_height = $height;
				$multiplier = $new_width / $this -> _width;
				$srcWidth = (int)($new_width / $multiplier);
				$srcHeight = (int)($new_height / $multiplier);
				$srcY = (int)(($this -> _height - $srcHeight) / 2);
				$dstX = (int)(($width - $new_width) / 2);
			}
		}
		//print_r($this->_width . ", " . $this->_height . ", $dstX, $dstY, $srcX, $srcY, $new_width, $new_height, $srcWidth,
		// $srcHeight");

		if (!imagecopyresampled($destination_image, $this -> _resource, $dstX, $dstY, $srcX, $srcY, $new_width, $new_height, $srcWidth, $srcHeight))
		{
			imagedestroy($destination_image);
			throw new Engine_Image_Adapter_Exception('Unable to resize image');
		}

		// Now destroy old image and overwrite with new
		imagedestroy($this -> _resource);
		$this -> _resource = $destination_image;
		$this -> _width = $width;
		$this -> _height = $height;

		return $this;
	}

}
