<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Supoort\Facades\Storage;
use Illuminate\Support\Facades\View;

class PostController extends Controller
{

    public function __contruct()
    {
        $this->middleware(middleware: 'auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $posts = Post::with(relations: 'user')->latest()->get();
        return view(view: 'posts.index', data: compact(var_name: 'posts'));
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view(view: 'posts.create');

    }
  
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
   
        $data = $request->validate(rules: [
            'caption' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = $request->file(key: 'image')->store(path: 'uploads', options: 'public');
        
        auth()->user()->posts()->create(atrributes: [
            'caption' => $data['caption'],
            'image_path'=> $imagePath,
        ]);

        return redirect(to: '/profile/' . auth()->user()->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): View
    {
        return view(view: 'posts.show', data: compact(var_name: 'post'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
  // Check if the authenticated user is the same as posts user
  if (auth()->id() !== $post->user_id) {
    abort(code: 403, message: 'Unauthorized action.');
  }
 return view(view: 'posts.edit', data: compact(var_name: 'post'));
  }      
    


  
    public function update(Request $request, Post $post): RedirectResponse
    {
        //Check if the authenticated user is the same as posts user
        if (auth()->id() !== $post->user_id) {
            abort(code: 403, message: 'Unauthorized action.');
        }

        $data = $request->validate(rules: [
            'caption' => 'required',
        ]);

        $post->update(attributes: $data);

        return redirect(to: '/posts/' . $post->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    { (auth()->id() !== $post->user_id) {
        abort(code:403, message: 'Unauthorized action.');
    }

    Storage::disk(name: 'public')->delete(paths: $post->image_path);

    $post->delete();

    return redirect(to: '/profile/' . auth()->user()->id);
        //
    }
}
