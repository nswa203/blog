<?php

use Illuminate\Database\Seeder;
use App\Tag;
use App\Category;
use App\Post;

function genres($post_id, $genres="") {
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
				DB::table('post_tag')->insert(array(
	      			'post_id' => $post_id,
	      			'tag_id' => $tag->id
	      		));
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
        $json = File::get("d:\Temp\movies_type.json");
        $data = json_decode($json);
        $i = 0;
        foreach ($data as $obj) {
        	$i++;
          	Category::create(array(
           		'id' => $i,
            	'name' => $obj->type,
          ));
        }

        DB::table('posts')->delete();
        $json = File::get("d:\Temp\movie1.json");
        $json = utf8_encode($json);
        $data = json_decode($json);
        $i = 0;
        foreach ($data as $obj) {
        	$i++;
          	Post::create(array(
           		'id' 			=> $i,
            	'title' 		=> utf8_decode($obj->title),
            	'body' 			=> utf8_decode($obj->plot),
            	'excerpt' 		=> utf8_decode($obj->title),
            	'slug' 			=> 'movie-'.$i,
            	'author_id' 	=> '1',
            	'category_id' 	=> $obj->series==''?'1':'2',
            	'status' 		=> $obj->moviefiles==''?'2':'4',
            	'image' 		=> substr(strrchr($obj->frontcover, "\\"), 1),
            	'published_at' 	=> $obj->moviefiles==''?null:($obj->moviereleaseyear==''?date('Y-m-d H:i:s'):date('Y-m-d H:i:s',strtotime('01/01/'.$obj->moviereleaseyear)))
          	));
          	genres($i, $obj->genre);
        }
    }
}
