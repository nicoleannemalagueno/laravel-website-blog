<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Blogpost</title>
  <link rel="stylesheet" href="/css/newsfeed.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>


<!-- Top Navigation Bar -->
<header class="topbar">
  <h2>My Blog</h2>
  <div class="topbar-buttons">
    <!-- Display the username -->
    @auth
      <span class="username">Welcome, {{ Auth::user()->username }}</span>
    @endauth

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
      @csrf
      <button type="submit">Logout</button>
    </form>
  </div>
</header>


  <!-- Blog Post Submission Form -->
  <section class="blog-form">
    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
        <label for="author">Author Name:</label>
        <input type="text" id="author" name="author" required />
        @error('author') <span class="error">{{ $message }}</span> @enderror
      </div>
      <div class="form-group">
        <label for="title">Post Title:</label>
        <input type="text" id="title" name="title" required />
        @error('title') <span class="error">{{ $message }}</span> @enderror
      </div>
      <div class="form-group">
        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="5" required></textarea>
        @error('content') <span class="error">{{ $message }}</span> @enderror
      </div>
      <div class="form-group">
        <label for="thumbnail">Thumbnail Image:</label>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" />
        @error('thumbnail') <span class="error">{{ $message }}</span> @enderror
      </div>
      <button type="submit">Publish</button>
    </form>
  </section>




  
  <!-- Blog Posts Container -->
  <main class="container">
    <header>
      <h1>Newsfeed Blog</h1>
    </header>

    @if($posts->isEmpty())
      <p>No posts available at the moment.</p>
    @else
      @foreach($posts as $post)
        <article class="post">
          <div class="post-header">
            <!-- Edit and Delete buttons only for the logged-in user -->
            @if(auth()->check() && auth()->id() == $post->user_id)
              <div class="post-actions">
                <button class="edit-button" data-id="{{ $post->id }}" data-author="{{ $post->author }}" data-title="{{ $post->title }}" data-content="{{ $post->content }}" data-thumbnail="{{ $post->thumbnail }}">Edit</button>
                <button class="delete-button" data-id="{{ $post->id }}">Delete</button>
              </div>
            @endif
            <div>
              <h2 class="author-name">{{ $post->author }}</h2>
              <p class="post-date">{{ $post->created_at->format('F d, Y') }}</p>
            </div>
          </div>

          @if($post->thumbnail)
            <div class="post-thumbnail">
              <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="Post Thumbnail" />
            </div>
          @endif

          <div class="post-content">
            <h3>{{ $post->title }}</h3>
            <p>{{ $post->content }}</p>
          </div>
        </article>
      @endforeach
    @endif
</main>


 <!-- Edit Post Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-button">&times;</span>
    <h2>Edit Post</h2>
    <form id="editForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" id="postId" name="post_id">
      <div class="form-group">
        <label for="editAuthor">Author Name</label>
        <input type="text" id="editAuthor" name="author" required />
      </div>
      <div class="form-group">
        <label for="editTitle">Title</label>
        <input type="text" id="editTitle" name="title" required />
      </div>
      <div class="form-group">
        <label for="editContent">Content</label>
        <textarea id="editContent" name="content" rows="5" required></textarea>
      </div>
      <div class="form-group">
        <label for="editThumbnail">Thumbnail (Optional)</label>
        <input type="file" id="editThumbnail" name="thumbnail" />
      </div>
      <button type="submit">Save Changes</button>
      <button type="button" class="cancel-button">Cancel</button>
    </form>
  </div>
</div>




 <!-- Delete Post Modal -->
<div id="deleteModal" class="modal">
  <div class="modal-content">
    <span class="close-button">&times;</span>
    <h2>Are you sure you want to delete this post?</h2>
    <form id="deleteForm">
      @csrf
      @method('DELETE')
      <input type="hidden" id="deletePostId" name="post_id">
      <button type="submit">Delete</button>
      <button type="button" class="cancel-button">Cancel</button>
    </form>
  </div>
</div>


  <script>
   // Show Edit Modal
$('.edit-button').click(function() {
  var postId = $(this).data('id');
  var title = $(this).data('title');
  var content = $(this).data('content');
  var author = $(this).data('author');
  var thumbnail = $(this).data('thumbnail');
  
  $('#postId').val(postId);
  $('#editTitle').val(title);
  $('#editContent').val(content);
  $('#editAuthor').val(author); 
  

  $('#editModal').show();
});

// Show Delete Modal
$('.delete-button').click(function() {
  var postId = $(this).data('id');
  $('#deletePostId').val(postId);
  
  $('#deleteModal').show();
});

// Close Modals
$('.close-button, .cancel-button').click(function() {
  $('#editModal').hide();
  $('#deleteModal').hide();
});

// Edit Post AJAX (Updated for PUT method)
$('#editForm').submit(function(e) {
  e.preventDefault();

  var postId = $('#postId').val();
  var title = $('#editTitle').val();
  var content = $('#editContent').val();
  var author = $('#editAuthor').val();  // Get updated author name
  var formData = new FormData();

  formData.append('_token', $('input[name="_token"]').val());
  formData.append('_method', 'PUT');  // Simulate PUT method
  formData.append('title', title);
  formData.append('content', content);
  formData.append('author', author);  // Send updated author

  // Check if thumbnail file is selected, and append it to the form data
  var thumbnail = $('#editThumbnail')[0].files[0];  // Get file from the input
  if (thumbnail) {
    formData.append('thumbnail', thumbnail);
  }

  $.ajax({
    url: '/update_posts/' + postId,
    type: 'POST',  // Use POST to simulate PUT
    data: formData,
    processData: false,  // Don't process the data
    contentType: false,  // Don't set content type
    success: function(response) {
      if (response.success) {
        alert('Post updated successfully!');
        location.reload();
        // Close the modal
        $('#editModal').hide();
      } else {
        alert('Failed to update post.');
      }
    },
    error: function(xhr, status, error) {
      alert('Failed to update post.');
    }
  });
});




// Delete Post AJAX
$('#deleteForm').submit(function(e) {
  e.preventDefault();
  
  var postId = $('#deletePostId').val();
  
  $.ajax({
    url: '/posts/' + postId,
    method: 'DELETE',
    data: {
      _token: $('input[name="_token"]').val()
    },
    success: function(response) {
      alert('Post deleted successfully!');
      // Remove the post from the DOM
      $('#post-' + postId).remove();
      location.reload();  
    },
    error: function() {
      alert('Failed to delete post.');
    }
  });
});



  </script>

</body>
</html>
