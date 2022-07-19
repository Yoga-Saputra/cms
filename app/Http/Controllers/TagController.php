<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Http\Requests\Tags\CreateTagRequest;
use App\Http\Requests\Tags\UpdateTagRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all();
        return view('tags.index', [
            'tags' => $tags
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTagRequest $request)
    {
        $this->validate($request, [

        ]);

        $data = Tag::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Successfully Created',
            'data' => $data
        ]);

        // session()->flash('success', 'Tag created successfully');

        // return redirect()->route('tags.index');
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
    public function edit(Tag $tag)
    {
        return view('tags.create',[
            'Tag' => $tag
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {

        $data = $tag->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Diupdate',
            'data' => $data
        ]);

        // session()->flash('success', 'Tag update successfully');

        // return redirect()->route('tags.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        if ($tag->posts->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tag cannot be delete, because it is associated to some posts'
            ]);
        }

        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus'
        ]);
    }
}
