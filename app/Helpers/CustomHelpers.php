<?php
use App\UserList;
use App\Customer;
use Illuminate\Support\Facades\Storage;

  // GET MEMBERSHIP NUMBER
	function getMembership($membership)
	{
		  $membership_value = substr($membership,-1,1);
      return (int)$membership_value;
	}

  // CHECK IMAGE
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

  // SCALE IMAGE
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

  // RESIZE AND REDUCE IMAGE SIZE AND DIMENTION
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

   // TO DISPLAY LIST WITH CONTACT ON PAGE CREATE CAMPAIGN AND APPOINTMENT
   function displayListWithContact($userid)
   {
      $data = array();
      $lists = UserList::where('user_id',$userid)->select('label','id')->get();

      if($lists->count() > 0)
      {
        foreach($lists as $row)
        {
          $customer = Customer::where([['list_id',$row->id],['status','=',1]])->get();
          $data[] = array(
            'id'=>$row->id,
            'label'=>$row->label,
            'customer_count'=>'('.$customer->count().')',
          );
        }
      }

      return $data;
   }

   //  MESSAGE DELIVERY STATUS
   function message_status($status)
    {
      if($status == 1)
      {
        return 'Success';
      }
      elseif($status == 2)
      {
        return '<span class="act-tel-apt-create">Phone Offline</span>';
      }
      elseif($status == 3)
      {
        return '<span class="act-tel-apt-create">Phone Not Available</span>';
      }
      elseif($status == 4)
      {
        return '<span class="act-tel-apt-create">Cancelled</span>';
      }
      else
      {
         return '<span class="act-tel-apt-create">Queued</span>';
      }
    }

    function getPackage($id_package,$check = null)
    {
      $package = array(
        1 => ['package'=>'basic1','price'=>195000],
        2 => ['package'=>'bestseller1','price'=>370500],
        3 => ['package'=>'supervalue1','price'=>526500],
        '-----------',
        4 => ['package'=>'basic2','price'=>275000],
        5 => ['package'=>'bestseller2','price'=>522500],
        6 => ['package'=>'supervalue2','price'=>742500],
        '-----------',
        7 => ['package'=>'basic3','price'=>345000],
        8 => ['package'=>'bestseller3','price'=>655500],
        9 => ['package'=>'supervalue3','price'=>931500],
        '-----------',
        10 => ['package'=>'basic4','price'=>415000],
        11 => ['package'=>'bestseller4','price'=>788500],
        12 => ['package'=>'supervalue4','price'=>1120500],
        '-----------',
        13 => ['package'=>'basic5','price'=>555000],
        14 => ['package'=>'bestseller5','price'=>1054500],
        15 => ['package'=>'supervalue5','price'=>1498500],
        '-----------',
        16 => ['package'=>'basic6','price'=>695000],
        17 => ['package'=>'bestseller6','price'=>1320500],
        18 => ['package'=>'supervalue6','price'=>1876500],
        '-----------',
        19 => ['package'=>'basic7','price'=>975000],
        20 => ['package'=>'bestseller7','price'=>1852500],
        21 => ['package'=>'supervalue7','price'=>2632500],
        '-----------',
        22 => ['package'=>'basic8','price'=>1255000],
        23 => ['package'=>'bestseller8','price'=>2384500],
        24 => ['package'=>'supervalue8','price'=>3388500],
        '-----------',
        25 => ['package'=>'basic9','price'=>155000],
        26 => ['package'=>'bestseller9','price'=>2954500],
        27 => ['package'=>'supervalue9','price'=>4288500],
      );

      if($id_package == '0')
      {
          return 'All';
      }
      elseif($id_package <> null && $check == 1)
      {
          return $package[$id_package];
      }
      elseif($id_package <> null || is_numeric($id_package))
      {
          return $package[$id_package]['package'];
      }
      else
      {
          return $package;
      }
      
    }

?>