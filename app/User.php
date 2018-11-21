<?php

namespace App;

use App\Models\Posts;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Request;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function update(Request $request, $id)
    {
        $post = Posts::findOrFail($id);

        if ($request->user()->cannot('update-post', $post)) {
            abort(403);
        }

        // 更新文章...
    }
}
