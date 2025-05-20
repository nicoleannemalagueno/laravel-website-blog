<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // Display all published posts
    public function index()
    {
        // Fetch all posts ordered by created_at in descending order
        $posts = Post::orderBy('created_at', 'desc')->get();
        return view('user.newsfeed', compact('posts'));
    }


    // Store a new post
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'author' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        // Handle file upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }
    
        // Create and save the post with authenticated user's ID
        Post::create([
            'user_id' => auth()->id(),  // Automatically set user ID from authentication
            'author' => $request->author,  // Author can be entered manually or set to authenticated user name
            'title' => $request->title,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
        ]);
    
        return redirect()->route('posts.index');
    }

    // Update an existing post
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
    
        // Check if the authenticated user is the owner of the post
        if ($post->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Handle file upload if thumbnail is being updated
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if it exists
            if ($post->thumbnail && Storage::exists('public/' . $post->thumbnail)) {
                Storage::delete('public/' . $post->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $post->thumbnail = $thumbnailPath;
        }
    
        // Update the post with title, content, author
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,  // Update the author field
            'thumbnail' => $post->thumbnail,  // Only update the thumbnail if new file uploaded
        ]);
    
        return response()->json(['success' => true, 'post' => $post]);
    }
    
    
    

    // Destroy a post
    public function destroy($id)
    {
        // Find the post or fail with a 404
        $post = Post::findOrFail($id);
    
        // Ensure the user is authorized to delete the post
        if ($post->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        // Delete the thumbnail image if it exists
        if ($post->thumbnail && Storage::exists('public/' . $post->thumbnail)) {
            Storage::delete('public/' . $post->thumbnail);
        }
    
        // Delete the post from the database
        $post->delete();
    
        return response()->json(['success' => true]);
    }
}
