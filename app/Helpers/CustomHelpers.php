<?php

	function getMembership($membership)
	{
		  $membership_value = substr($membership,-1,1);
      return (int)$membership_value;
	}

  function checkImageSize($image)
  {
      $image_file_size = (int)number_format($image->getSize() / 1024, 2);
      if($image_file_size > 500)
      {
         return true;
      }
      else
      {
         return false;
      }
  }

  function scaleImageRatio($width,$height)
  {
    if($width > 1280)
    {
      $scale = $width/1280;
      $newHeight = (int)$height/$scale;
      $data = array(
          'width'=>1280,
          'height'=>(int)$newHeight,
      );
    }
    else
    {
      $scale = $height/1280;
      $newWidth = (int)$width/$scale;
      $data = array(
          'width'=>(int)$newWidth,
          'height'=>1280,
      );
    }
      
    return $data;
  }

  function resize_image($file, $w, $h, $crop=false,$folder_name,$file_name) {
      list($width, $height) = getimagesize($file);
      $r = $width / $height;

      if ($crop) {
          if ($width > $height) {
              $width = ceil($width-($width*abs($r-$w/$h)));
          } else {
              $height = ceil($height-($height*abs($r-$w/$h)));
          }
          $newwidth = $w;
          $newheight = $h;
      } else {
          if ($w/$h > $r) {
              $newwidth = $h*$r;
              $newheight = $h;
          } else {
              $newheight = $w/$r;
              $newwidth = $w;
          }
      }

      $check_image_ext = exif_imagetype($file);
      #Check whether the file is valid jpg or not
      $tempExtension = $file->getClientOriginalExtension();
      switch(image_type_to_mime_type($check_image_ext)){
        case 'image/png':
          $ext = 'png';
        break;
        case 'image/gif':
          $ext = 'gif';
        break;
        case 'image/jpeg':
          $ext = 'jpg';
        break;
      }

      $newfile = $file;
      switch($ext){
          case "png":
              $src = imagecreatefrompng($newfile);
          break;
          case "jpeg":
          case "jpg":
              $src = imagecreatefromjpeg($newfile);
          break;
          case "gif":
              $src = imagecreatefromgif($newfile);
          break;
          default:
              $src = imagecreatefromjpeg($newfile);
          break;
      }
      
      $path = $folder_name.$file_name;

      if($ext == "png")
      {
         $dst = imagecreate($newwidth, $newheight);
         imagealphablending( $dst, false );
         imagesavealpha( $dst, true );
         imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

         ob_start();
         imagepng($dst);
         $image_contents = ob_get_clean();

         Storage::disk('s3')->put($path,$image_contents,'public');
          // Storage::disk('local')->put('test/'.$path,$image_contents);
      }
      else if($ext == "gif")
      {
         $dst = imagecreate($newwidth, $newheight);
         imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
         ob_start();
         imagegif($dst);
         $image_contents = ob_get_clean();

         Storage::disk('s3')->put($path,$image_contents,'public');         
         // Storage::disk('local')->put('test/'.$path,$image_contents);
      }
      else
      {
         $dst = imagecreatetruecolor($newwidth, $newheight);
         imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
         ob_start();
         imagejpeg($dst);
         $image_contents = ob_get_clean();

         Storage::disk('s3')->put($path,$image_contents,'public');
         // Storage::disk('local')->put('test/'.$path,$image_contents);
      }
      
   }

?>