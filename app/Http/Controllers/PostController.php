<?php

namespace App\Http\Controllers;

use App\Category;
use App\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Tag;
use Carbon\Carbon;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('verifyCategoryCount')->only(['store']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::join('categories as c', 'c.id', 'posts.category_id')
            ->select([
                'c.name',
                'posts.*'
            ])->get();
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
        return view('posts.create')->with('categories', Category::all())->with('tags', Tag::all());
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
        $input['category_id'] = $input['category'];

        if ($request->hasFile('image')) {
            $input['image'] = 'image/' . Str::slug($input['title'], '-') . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(storage_path('/app/public/image'), $input['image']);
        }

        $data = Post::create($input);

        // save data of relationship many to many
        if ($request->tags) {
            $data->tags()->attach($request->tags);
        }

        if ($data) {
            return response()->json([
                'status' => true,
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
            'categories' => Category::all(),
            'post' => $post,
            'tags' => Tag::all()
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
        $input['image'] = $post->image;
        $input['category_id'] = $input['category'];

        if ($request->hasFile('image')) {
            $input['image'] = 'image/' . Str::slug($input['title'], '-') . '.' . $request->image->getClientOriginalExtension();
            $request->image->move(storage_path('/app/public/image'), $input['image']);

            if(file_exists(storage_path('app/public/' . $post->image))){
                unlink(storage_path('app/public/' . $post->image));
            }
        }

        $post->update($input);

        if ($request->tags) {
            $post->tags()->sync($request->tags);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
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
            if ($post->image != NULL) {
                if(file_exists(storage_path('app/public/' . $post->image))){
                    unlink(storage_path('app/public/' . $post->image));
                }
            }
            $post->forceDelete();
        }else{
            $post->delete();
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);

    }

    public function trashed()
    {
        $trashed = Post::onlyTrashed()->get();
        return view('posts.index', [
            'posts' => $trashed
        ]);
    }

    public function restorePost(Post $post)
    {
        $post->restore();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dikembalikan'
        ]);
    }
}
