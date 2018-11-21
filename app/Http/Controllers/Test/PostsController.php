<?php
namespace App\Http\Controllers\Test;

use App\Models\Posts;

class PostsController
{
    public function index()
    {
        $posts = Posts::popular()->orderBy('views','desc')->get();
        foreach ($posts as $post) {
            echo '&lt;'.$post->title.'&gt; '.$post->views.'views<br>';
        }
    }
}