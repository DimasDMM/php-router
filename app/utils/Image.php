<?php
namespace Utils;

class Image
{
	/**
	 * @param string $image Path of file
	 * @return array
	 */
	public function dimensions($image)
    {
		$dim = getimagesize($image);
		$result = array(
			'width' => $dim[0],
			'height' => $dim[1]
		);
		return $result;
	}

	/**
	 * @param string $image Formats: jpg, jpeg, gif, png
	 * @param string $destination Formats: jpg, jpeg, gif, png
	 * @param int $src_x Coordinate x in origen
	 * @param int $src_y Coordinate y in origen
	 * @param int $src_w Width origin
	 * @param int $src_h Height origin
	 * @param int $dst_w Width destination
	 * @param int $dst_h Height destination
	 * @return boolean
	 */
	public static function chop($image, $destination, $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h)
    {
		switch ($this->filetype($image)) {
			case 'jpg':
			case 'jpeg':
				$imgSrc = imagecreatefromjpeg($image);
				break;
			case 'gif':
				$imgSrc = imagecreatefromgif($image);
				break;
			case 'png':
				$imgSrc = imagecreatefrompng($image);
				break;
			default:
				return false;
		}

		$imgDst = imagecreatetruecolor($dst_w, $dst_h);

		imagecopyresampled($imgDst, $imgSrc, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		switch($this->filetype($image)) {
			case 'jpg':
			case 'jpeg':
				imagejpeg($imgDst, $destination, 90);
				break;
			case 'gif':
				imagegif($imgDst, $destination);
				break;
			case 'png':
				imagepng($imgDst, $destination, 7);
				break;
			default:
				return false;
		}

		imagedestroy($imgSrc);
		imagedestroy($imgDst);

		return file_exists($destination);
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function filetype($path)
    {
		return pathinfo($path, PATHINFO_EXTENSION);
	}
}
