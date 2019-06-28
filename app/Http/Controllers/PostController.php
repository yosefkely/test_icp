<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class PostController extends Controller
{
    protected $user;

    public function __construct()
    {
        try {
			$user = JWTAuth::parseToken()->authenticate();
		} catch (Exception $e) {
			if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
				return response()->json(['status' => 'Token is Invalid']);
			}else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
				return response()->json(['status' => 'Token is Expired']);
			}else{
				return response()->json(['status' => 'Authorization Token not found']);
			}
		}
		//return $next($request);
    }

    public function index()
    {
        return Post::paginate(10);
    }

    public function show($id)
    {
        return Post::find($id);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required'
        ]);

        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->created_by = Auth::user()->id;
        
        if ($post->save())
            return response()->json([
                'success' => true,
                'post' => $post
            ]);
        else
            return response()->json([
                'success' => false,
                'message' => 'Sorry, post could not be added'
            ], 500);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, post with id ' . $id . ' cannot be found'
            ], 400);
        }
		$post->title = $request->title;
        $post->content = $request->content;
		$post->updated_by = Auth::user()->id;
        $post->save();
		
		return response()->json([
                'success' => true,
				'message' => 'update success'
            ]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
		$post->updated_by = Auth::user()->id;
        $post->save();
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, post with id ' . $id . ' cannot be found'
            ], 400);
        }

        if ($post->delete()) {
            return response()->json([
                'success' => true,
				'message' => 'delete success'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Post could not be deleted'
            ], 500);
        }
    }
}
