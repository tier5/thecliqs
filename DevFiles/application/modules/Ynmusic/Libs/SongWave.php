<?php
class SongWave {
	public function findValues($byte1, $byte2)  {
    	$byte1 = hexdec(bin2hex($byte1));
   	 	$byte2 = hexdec(bin2hex($byte2));
    	return ($byte1 + ($byte2 * 256));
	}

	/**
	 * Great function slightly modified as posted by Minux at
	 * http://forums.clantemplates.com/showthread.php?t=133805
	 */
	public function html2rgb($input) {
    	$input = ($input[0] == "#") ? substr($input, 1, 6) : substr($input, 0, 6);
    	return array(hexdec(substr($input, 0, 2)), hexdec(substr($input, 2, 2)), hexdec(substr($input, 4, 2)));
	}
	
	
	/**
	* PROCESS THE FILE
 	*/
	// temporary file name
	public function createWavePhoto($pathFile, $top_foreground = "#FF0000", $bott_foreground = "#000000") {
		$tmpname = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.substr(md5(time()), 0, 10);
		
		// copy from temp upload directory to current
		copy($pathFile, "{$tmpname}_o.mp3");
		exec("lame {$tmpname}_o.mp3 -m m -S -f -b 16 --resample 8 {$tmpname}.mp3 && lame -S --decode {$tmpname}.mp3 {$tmpname}.wav");
		$filename = "{$tmpname}.wav";
		
		
		// delete temporary files
		
		// set values
		$width = 736;
		$height = 70;
		
		$img = false;
		// generate foreground color
		list($r, $g, $b) = $this->html2rgb($top_foreground);
		list($b_r, $b_g, $b_b) = $this->html2rgb($bott_foreground);
		
		// process wav individually
		$handle = fopen($filename, "r");
		// wav file header retrieval
		$heading[] = fread($handle, 4);
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = fread($handle, 4);
		$heading[] = fread($handle, 4);
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = bin2hex(fread($handle, 4));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = bin2hex(fread($handle, 2));
		$heading[] = fread($handle, 4);
		$heading[] = bin2hex(fread($handle, 4));
		
		// wav bitrate
		$peek = hexdec(substr($heading[10], 0, 2));
		$byte = $peek / 8;
		
		// checking whether a mono or stereo wav
		$channel = hexdec(substr($heading[6], 0, 2));
		
		$ratio = ($channel == 2 ? 40 : 80);
		
		// start putting together the initial canvas
		$data_size = floor((filesize($filename) - 44) / ($ratio + $byte) + 1);
		
		$detail = (int)($data_size / $width);
		$data_point = 0;
		
		// create original image width based on amount of detail
		// each waveform to be processed with be $height high, but will be condensed
		// and resized later (if specified)
		$img = imagecreatetruecolor($data_size / $detail, $height);
		// transparent background specified
		imagesavealpha($img, true);
		$transparentColor = imagecolorallocatealpha($img, 0, 0, 0, 127);
		imagefill($img, 0, 0, $transparentColor);
		$count = 0;
		while (!feof($handle) && $data_point < $data_size) {
		    if ($data_point++ % $detail == 0) {
		        $count++;
		        $bytes = array();
		        // get number of bytes depending on bitrate
		        for ($i = 0; $i < $byte; $i++)
		            $bytes[$i] = fgetc($handle);
		
		        switch($byte) {
		            // get value for 8-bit wav
		            case 1 :
		                $data = $this->findValues($bytes[0], $bytes[1]);
		                break;
		            // get value for 16-bit wav
		            case 2 :
		                if (ord($bytes[1]) & 128)
		                    $temp = 0;
		                else
		                    $temp = 128;
		                $temp = chr((ord($bytes[1]) & 127) + $temp);
		                $data = floor($this->findValues($bytes[0], $temp) / 256);
		                break;
		        }
		
		        // skip bytes for memory optimization
		        fseek($handle, $ratio, SEEK_CUR);
		
		        // draw this data point
		        // relative value based on height of image being generated
		        // data values can range between 0 and 255
		        $v = (int)($data / 255 * ($height * 2 / 3));
		        $v_b = (int)($data / 255 * $height / 3);
		        // draw the line on the image using the $v value and centering it vertically on the canvas
		        imageline($img,
		            // x1
		            (int)($data_point / $detail) + $count,
		            // y1: height of the image minus $v as a percentage of the height for the wave amplitude
		            ($height * 2 / 3),
		            // x2
		            (int)($data_point / $detail) + $count,
		            // y2: same as y1, but from the bottom of the image
		            $v_b + ($height * 2 / 3), 
		            imagecolorallocate($img, $b_r, $b_g, $b_b)
		        );
		        imageline($img,
		            // x1
		            (int)($data_point / $detail) + $count,
		            // y1: height of the image minus $v as a percentage of the height for the wave amplitude
		            ($height * 2 / 3),
		            // x2
		            (int)($data_point / $detail) + $count,
		            // y2: same as y1, but from the bottom of the image
		            ($height * 2 / 3) - $v, 
		            imagecolorallocate($img, $r, $g, $b)
		         );
		
		    } 
		    else 
		    {
		        // skip this one due to lack of detail
		        fseek($handle, $ratio + $byte, SEEK_CUR);
		    }
		}
		
		// close and cleanup
		fclose($handle);
		// delete the processed wav file
		unlink($filename);
		
		// want it resized?
		if ($width) 
		{
		    // resample the image to the proportions defined in the form
		    $rimg = imagecreatetruecolor($width, $height);
		    // save alpha from original image
		    imagesavealpha($rimg, true);
		    imagealphablending($rimg, false);
		    // copy to resized
		    imagecopyresampled($rimg, $img, 0, 0, 0, 0, $width, $height, imagesx($img), imagesy($img));
		    imagepng($rimg, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.'waves.png');
		    imagedestroy($rimg);
		} else {
		    imagepng($img, APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary/'.'waves.png');
		}
		imagedestroy($img);
		unlink("{$tmpname}_o.mp3");
		unlink("{$tmpname}.mp3"); 
	}	 
}