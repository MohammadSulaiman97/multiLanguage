<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $keyword = isset($request->keyword) && $request->keyword != '' ? $request->keyword : null;

        $posts = Post::orderBy('id', 'desc');
        if (!is_null($keyword)) {
            $posts = $posts->search($keyword, null, true);
        }

        $posts = $posts->get();

        return view('posts.index', compact('posts'));
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
    public function store(Request $request)
    {
        $niceNames = [];
        $attr = [];

        foreach (config('locales.languages') as $key => $val) {
            $attr['title.' . $key] = 'required';
            $attr['body.' . $key] = 'required';
            $niceNames['title.' . $key] = __('posts.title'). ' (' . $val['name'] . ')';
            $niceNames['body.' . $key] = __('posts.body'). ' (' . $val['name'] . ')';
        }

        $validation = Validator::make($request->all(), $attr);
        $validation->setAttributeNames($niceNames);

        if ($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $data['title'] = $request->title;
        $data['body'] = $request->body;

        $post = Post::create($data);

        if ($post) {
            return redirect()->route('posts.show', $post)->with([
                'message' => __('posts.created_successfully'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->route('posts.index')->with([
            'message' => __('posts.something_was_wrong'),
            'alert-type' => 'danger'
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($post)
    {
       $post = Post::where('slug->' . app()->getLocale(),$post)->first();

        return view('posts.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit($post)
    {
        $post = Post::where('slug->' . app()->getLocale(),$post)->first();

        return view('posts.edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $post)
    {
        $niceNames = [];
        $attr = [];

        foreach (config('locales.languages') as $key => $val) {
            $attr['title.' . $key] = 'required';
            $attr['body.' . $key] = 'required';
            $niceNames['title.' . $key] = __('posts.title'). ' (' . $val['name'] . ')';
            $niceNames['body.' . $key] = __('posts.body'). ' (' . $val['name'] . ')';
        }

        $validation = Validator::make($request->all(), $attr);
        $validation->setAttributeNames($niceNames);
        if ($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput();
        }

        $post = Post::where('slug->' . app()->getLocale(), $post)->first();


        $data['title'] = $request->title;
        $data['body'] = $request->body;

        $update = $post->update($data);

        if ($update) {
            return redirect()->route('posts.show', $post)->with([
                'message' => __('posts.updated_successfully'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->route('posts.index')->with([
            'message' => __('posts.something_was_wrong'),
            'alert-type' => 'danger'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($post)
    {
        $post = Post::where('slug->' . app()->getLocale(), $post)->first()->delete();

        if ($post) {
            return redirect()->route('posts.index')->with([
                'message' => __('posts.deleted_successfully'),
                'alert-type' => 'success'
            ]);
        }

        return redirect()->route('posts.index')->with([
            'message' => __('posts.something_was_wrong'),
            'alert-type' => 'danger'
        ]);

    }
}
