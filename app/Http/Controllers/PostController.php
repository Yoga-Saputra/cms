<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return view('posts.index', [
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePostRequest $request)
    {
        $input = $request->all();
        $input['image'] = null;

        if ($request->hasFile('image')) {
            $input['image'] = 'image/' . Str::slug($input['title'], '-') . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(storage_path('/app/public/image'), $input['image']);
        }

        $data = Post::create($input);
        if ($data) {
            return response()->json([
                'success' => true,
                'message' => 'Data Successfully Created',
                'data' => $data
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Add data failed',
                'data' => $data
            ], 401);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('posts.create',[
            'post' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $input = $request->all();
        $input['image'] = null;

        if ($request->hasFile('image')) {
            $input['image'] = 'image/' . Str::slug($input['title'], '-') . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(storage_path('/app/public/image'), $input['image']);
        }

        $data = $post->update($input);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::withTrashed()->where('id', $id)->first();
        if ($post->trashed()) {
            $post->forceDelete();
        }else{
            $post->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus'
        ]);
    }

    public function trashed()
    {
        $trashed = Post::onlyTrashed()->get();
        return view('posts.index', [
            'posts' => $trashed
        ]);
    }

    public function restorePost($id)
    {
        Post::withTrashed()->find($id)->restore();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dikembalikan'
        ]);
    }
}
