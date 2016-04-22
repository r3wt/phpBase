<?php

namespace App\Util;

trait Upload
{
	
	private function square_thumbs($img_name,$files,$sizes = [200],$min_width=200,$min_height=200,$unsafe=false)
	{
		if(!$unsafe){
			if(empty($files) || !isset($files[$img_name])){
				throw new \exception('You must select an image!');
			}
			if($files[$img_name]['error'])
			{
				throw new \exception($this->upload_errors($files[$img_name]['error']));
			}
		}
		$image_temp = $files[$img_name]['tmp_name'];
		@$image_info = getimagesize($image_temp);
		$exif = [IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG];
		$finfo  = ['image/gif','image/png','image/jpeg','image/pjpeg'];
		$etype = exif_imagetype($image_temp);
		if(!function_exists('finfo_file')){
			throw new \exception('Unable to determine image mime type.');
		}
		$type = finfo_file(finfo_open(FILEINFO_MIME_TYPE),$image_temp);
		
		if(!$unsafe){
			if( !is_uploaded_file($files[$img_name]['tmp_name']) ){
				throw new \exception('bad file input');
			}
			if( !in_array($etype,$exif) || !in_array($type,$finfo)){
				throw new \exception('file type not allowed');
			}	
		}
		
		switch($type){
			case 'image/gif':
				$type2 = 'gif';
			break;
			case 'image/png':
				$type2 = 'png';
			break;
			case 'image/jpeg':
			case 'image/jpg':
			case 'image/pjpeg':
				$type2 = 'jpg';
			break;
		}
		if( !$image_info ){
			throw new \exception('invalid image size.');
		}
		
		//get the image resource
		$image = imagecreatefromstring(file_get_contents($image_temp));
		$width  = $image_info[0];
		$height = $image_info[1];
		if($width < $min_width || $height < $min_height){
			$msg = 'Images must be atleast '.$min_width.' x '.$min_height.' in size.';
			throw new \exception($msg);
		}
		if($width != $height){
			list($size,$image) = $this->square_img($image,$width,$height);
			$width = $height = $size;
		}
		$size = $width;
		//now we have a square image and our image size is updated to reflect this change.
		$res = [];
		foreach($sizes as $z)
		{
			$t = imagecreatetruecolor($z,$z);
			ob_start();
			switch($type){
				case 'image/gif':
					imagecopyresampled($t,$image,0,0,0,0,$z,$z,$size,$size);
					imagegif($t);
				break;
				case 'image/png':
					imagesavealpha($t, true);
					$trans_colour = imagecolorallocatealpha($t, 0, 0, 0, 127);
					imagefill($t, 0, 0, $trans_colour);
					imagecopyresampled($t,$image,0,0,0,0,$z,$z,$size,$size);
					imagepng($t,NULL,2); //lossless compression minimal (default is 6)
				break;
				case 'image/jpeg':
				case 'image/jpg':
				case 'image/pjpeg':
					imagecopyresampled($t,$image,0,0,0,0,$z,$z,$size,$size);
					imagejpeg($t,NULL,100); //100% quality (default is 75%)
				break;
			}
			$fr = ob_get_clean();
			$fh = fopen($image_temp,'w+');
			fwrite($fh,$fr);
			fclose($fh);
			unset($fr,$fh,$fc,$t);
			$res[] = $this->s3PutFile('image',$image_temp,'.'.$type2,$type);
		}
		return $res;
	}
	
	private function square_img($image,$width,$height)
	{
		if ($width > $height) {
			$square = $height;              // $square: square side length
			$offsetX = ($width - $height) / 2;  // x offset based on the rectangle
			$offsetY = 0;              // y offset based on the rectangle
		}
		// vertical rectangle
		elseif ($height > $width) {
			$square = $width;
			$offsetX = 0;
			$offsetY = ($height - $width) / 2;
		}
		$t = imagecreatetruecolor($square,$square);
		imagecopyresampled($t, $image, 0, 0, $offsetX, $offsetY, $square, $square, $square, $square);
		return [$square,$t];
	}
	
	//private methods
	private function img_upload($img_name,$files,$min_width = 120, $min_height = 120)
	{
		if(empty($files) || !isset($files[$img_name])){
			throw new \exception('You must select an image!');
		}
		if($files[$img_name]['error'])
		{
			throw new \exception($this->upload_errors($files[$img_name]['error']));
		}
		$image_temp = $files[$img_name]['tmp_name'];
		@$image_info = getimagesize($image_temp);
		$exif = [IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG];
		$finfo  = ['image/gif','image/png','image/jpeg','image/pjpeg'];
		$etype = exif_imagetype($image_temp);
		if(!function_exists('finfo_file')){
			throw new \exception('Unable to determine image mime type.');
		}
		$type = finfo_file(finfo_open(FILEINFO_MIME_TYPE),$image_temp);
		if( !is_uploaded_file($files[$img_name]['tmp_name']) ){
			throw new \exception('bad file input');
		}
		if( !in_array($etype,$exif) || !in_array($type,$finfo)){
			throw new \exception('file type not allowed');
		}
		switch($type){
			case 'image/gif':
				$type2 = 'gif';
			break;
			case 'image/png':
				$type2 = 'png';
			break;
			case 'image/jpeg':
			case 'image/jpg':
			case 'image/pjpeg':
				$type2 = 'jpg';
			break;
		}
		if( !$image_info ){
			throw new \exception('invalid image size.');
		}
		$width  = $image_info[0];
		$height = $image_info[1];
		if($width < $min_width || $height < $min_height){
			throw new \exception('Images must be atleast 120 x 120 in size.');
		}
		$fr = imagecreatefromstring(file_get_contents($image_temp));
		ob_start();
		switch($type){
			case 'image/gif':
				imagegif($fr);
			break;
			case 'image/png':
				imagesavealpha($fr, true);
				$trans_colour = imagecolorallocatealpha($fr, 0, 0, 0, 127);
				imagefill($fr, 0, 0, $trans_colour);
				imagepng($fr);
			break;
			case 'image/jpeg':
			case 'image/jpg':
			case 'image/pjpeg':
				imagejpeg($fr);
			break;
		}
		$fc = ob_get_clean();
		$fh = fopen($image_temp,'w+');
		fwrite($fh,$fc);
		fclose($fh);
		unset($fr,$fh,$fc);
		return $this->s3PutFile('image',$image_temp,'.'.$type2,$type);
	}

	private function upload_errors($err_code) {
		switch ($err_code) {
			case UPLOAD_ERR_INI_SIZE:
				return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			case UPLOAD_ERR_FORM_SIZE:
				return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			case UPLOAD_ERR_PARTIAL:
				return 'The uploaded file was only partially uploaded';
			case UPLOAD_ERR_NO_FILE:
				return 'No file was uploaded';
			case UPLOAD_ERR_NO_TMP_DIR:
				return 'Missing a temporary folder';
			case UPLOAD_ERR_CANT_WRITE:
				return 'Failed to write file to disk';
			case UPLOAD_ERR_EXTENSION:
				return 'File upload stopped by extension';
			default:
				return 'Unknown upload error';
		}
	}
	
	private function deleteImage($img)
	{
		if(strpos('/',$img) !== false){
			$img = array_pop(explode('/',$img));
		}
		$this->s3DeleteFile('image',$img);
	}
	
	private function s3DeleteFile($type,$key)
	{
		
		$config = (\App\App::getInstance())->config['aws'];
		$s3 = new \Aws\S3\S3Client($config['config']);
		switch($type){
			default:
			case 'other':
				$bucket = $config['buckets']['other'];
			break;
			case 'image':
				$bucket = $config['buckets']['img'];
			break;
			case 'video':
				$bucket = $config['buckets']['video'];
			break;
			case 'zip':
				$bucket = $config['buckets']['zip'];
			break;
		}
		try{
			$result = $s3->deleteObject(array(
				'Bucket' => $bucket['name'],
				'Key'    => $key
			)); 
		}
		catch(\exception $e){
			
		}
	}
	
	private function s3PutFile($type,$tmp_file,$ext,$mime)
	{
		$config = (\App\App::getInstance())->config['aws'];
		$s3 = new \Aws\S3\S3Client($config['config']);
		switch($type){
			default:
			case 'other':
				$bucket = $config['buckets']['other'];
			break;
			case 'image':
				$bucket = $config['buckets']['img'];
			break;
			case 'video':
				$bucket = $config['buckets']['video'];
			break;
			case 'zip':
				$bucket = $config['buckets']['zip'];
			break;
			//add other cases here.
		}
		try{
			$body = file_get_contents($tmp_file);
			$key = md5($body).'-'.time().$ext;
			//if it fails, i don't care. move on to the next bucket
			$s3->putObject([
				'Bucket'=>$bucket['name'],
				'Key'=>$key,
				'Body'=>$body,
				'CacheControl'=>'max-age=172800',
				'ContentType'=>$mime
			]);
			return $bucket['public_url'].$key; //if we were successful in writing to the bucket, we return the full url to the resource.
		}
		catch(\exception $e){
			throw $e;
		}
		return false;
	}

}