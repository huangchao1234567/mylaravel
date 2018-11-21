<?php
namespace App\Http\Controllers\Test;
use App\Http\Controllers\Controller;
use App\Models\Comments;
use App\Models\Country;
use App\Models\Posts;
use App\Models\User;
use App\Models\Video;
use App\Providers\UserProviders;
use Illuminate\Http\Request;
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2018/9/3
 * Time: 17:19
 */
class UserController extends Controller
{

  public function index(Request $request)
  {
     /* $comment   = Comments::find(2);
      $item      = $comment->comments;
      dd($item);*/
       $video = Video::find(1);
       $videoComments = $video->comments;
       dd($videoComments);

     /* $country = Country::first();
      $posts = $country->posts;
    //  dd($posts->toArray());

      echo 'Country#'.$country->name.'下的文章：<br>';
      foreach($posts as $post){
          echo '&lt;&lt;'.$post->title.'&gt;&gt;<br>';
      }*/
    /*  $user = User::find(1);
      $roles = $user->with('roles')->get();
      dd($roles->toArray());
      echo 'User#'.$user->name.'所拥有的角色：<br>';
      foreach($roles as $role)
      {
          echo $role->name.'<br>';
      }*/

     /* $account = User::find(1)->with('account')->get();
      dd($account->toArray());*/

      /*$posts = Posts::popular()->status(0)->orderBy('views','desc')->get();
      foreach ($posts as $post) {
          echo '&lt;'.$post->title.'&gt; '.$post->views.'views<br>';
      }*/

    /*  $posts = Posts::popular()->orderBy('views','desc')->get();
      foreach ($posts as $post) {
          echo '&lt;'.$post->title.'&gt; '.$post->views.'views<br>';
      }*/

/*
      $aa       =UserProviders::User();
     if ($aa->count()){
         return $aa->toArray();
     }
     return [];*/
  }

  public function select()
  {
      dd(1);
  }
}