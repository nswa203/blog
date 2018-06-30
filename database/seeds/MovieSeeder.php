<?php

use Illuminate\Database\Seeder;
use App\Tag;
use App\Category;
use App\Post;
use App\Photo;
use Intervention\Image\Facades\Image; 
use Illuminate\Support\Facades\Storage;

function genres($id, $genres="", $table="posts") {
	$myrc = 0;
	//echo $genres.PHP_EOL;
	$genreList = explode(',', $genres.',');
	foreach ($genreList as $genre) {
		$genre=trim($genre);
		if ($genre !== '') {
			//echo '['.$genre.' | ';
			$tag = Tag::where('name', $genre)->first();
			if (!$tag) {
				$tag = Tag::create(array(
	           		'name' => $genre,
	          	));
				echo 'Added---->'.$genre.'<-from->'.$genres.'<'.PHP_EOL;
			}	
			
  		if ($tag) {
  			//echo $tag->id.']';
        if ($table=="posts") {
    			DB::table('post_tag')->insert(array(
      			'post_id' => $id,
      			'tag_id' => $tag->id
      		));
        } else {
          DB::table('photo_tag')->insert(array(
            'photo_id' => $id,
            'tag_id' => $tag->id
          ));
        }
      }	
    }	
  }
	//echo PHP_EOL;
	return $myrc;
}

class MovieSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */

  public function run()
  {
	  /* Added on the fly from posts data
    DB::table('tags')->delete();
    $json = File::get("d:\Temp\movies_genre.json");
    $data = json_decode($json);
    $i = 0;
    foreach ($data as $obj) {
    	$i++;
      	Tag::create(array(
       		'id' => $i,
        	'name' => $obj->genre,
      ));
    }*/

    DB::table('categories')->delete();
    //$json = File::get("d:\Temp\movies_type.json");
    //$data = json_decode($json);
    $data = ['Movie', 'TV Show', 'Album', 'Test Data'];
    $i = 0;
    foreach ($data as $obj) {
    	$i++;
      	Category::create(array(
       		'id' => $i,
        	'name' => $obj,
      ));
    }

    DB::table('posts')->delete();
    $json = File::get("d:\Temp\movie1.json");
    $json = utf8_encode($json);
    $data = json_decode($json);
    $i = 0;
    foreach ($data as $obj) {
    	$i++;

      if      ($obj->usertext1 == 'B') { $status = '1'; }
      elseif  ($obj->moviefiles == '') { $status = '2'; }
      else                             { $status = '4'; }

      	Post::create(array(
       		'id' 			     => $i,
        	'title' 		   => utf8_decode($obj->title),
        	'body' 			   => utf8_decode($obj->plot),
        	'excerpt' 		 => utf8_decode($obj->title),
        	'slug'         => 'movie-'.$i,
        	'author_id'    => '1',
        	'category_id'  => $obj->series==''?'1':'2',
        	'status' 		   => $status,
        	'image'        => substr(strrchr($obj->frontcover, "\\"), 1),
          'banner'       => substr(strrchr($obj->backdrop, "\\"), 1),
        	'published_at' => $obj->moviefiles==''?null:($obj->moviereleaseyear==''?date('Y-m-d H:i:s'):date('Y-m-d H:i:s',strtotime('01/01/'.$obj->moviereleaseyear)))
      	));
      genres($i, $obj->genre, "posts");
    }

    DB::table('photos')->delete();
    $json = File::get("d:\Temp\movie1.json");
    $json = utf8_encode($json);
    $data = json_decode($json);
    $i = 0;
    foreach ($data as $obj) {
      $i++;
      $filename = substr(strrchr($obj->frontcover, "\\"), 1);
      $location = public_path('images\\' . $filename);
      echo $location."\r\n";
      try   { $exif = Image::make($location)->exif(); }
      catch (Exception $e) { $exif = null; }  
      try   { $iptc = Image::make($location)->iptc(); }
      catch (Exception $e) { $iptc = null; }
      try   { $size = filesize($location); }
      catch (Exception $e) { $size = null; }

      $taken_at = null;
      if ($exif) {
        if (isset($exif['DateTime'])) {
            $taken_at = date('Y-m-d H:i:s', strtotime($exif['DateTime']));
        }    
      }

      if      ($obj->usertext1 == 'B') { $status = '1'; }
      elseif  ($obj->moviefiles == '') { $status = '2'; }
      else                             { $status = '4'; }

        Photo::create(array(
          'id'           => $i,
          'title'        => utf8_decode($obj->title),
          'description'  => utf8_decode($obj->plot),
          'status'       => $status,
          'image'        => $filename,
          'file'         => $filename,
          'exif'         => json_encode($exif),
          'iptc'         => json_encode($iptc),
          'size'         => $size,
          'taken_at'     => $taken_at,
          'published_at' => $obj->moviefiles==''?null:($obj->moviereleaseyear==''?date('Y-m-d H:i:s'):date('Y-m-d H:i:s',strtotime('01/01/'.$obj->moviereleaseyear)))
        ));
      genres($i, $obj->genre, "photos");
    }

  }
}
