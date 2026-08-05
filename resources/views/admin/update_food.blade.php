<!DOCTYPE html>
<html>
  <head> 

  <base href="\public">
    
  @include('admin.css')

  <style>
    .form-frame
    {
        background:radial-gradient(circle at top left, rgba(255, 255, 255, 0.08), transparent 28%), #0c0d10;
        border:1px solid #2b2f38;
        border-radius:8px;
        color:#fff;
        margin:0;
        min-height:calc(100vh - 120px);
        padding:24px;
    }

    .form-panel
    {
        background:#15171c;
        border:1px solid #2b2f38;
        border-radius:8px;
        max-width:760px;
        padding:24px;
    }

    .div_deg
    {
        padding: 10px 0;
    }
    label
    {
        display: inline-block;
        width: 200px;
        color:#a6abb6;
        font-weight:700;
    }

    input,
    textarea
    {
        width:calc(100% - 210px);
    }

    input[type="file"],
    input[type="submit"]
    {
        width:auto;
    }

    .file-type-hint {
        color:#a6abb6;
        display:none;
        font-size:12px;
        margin-left:210px;
        margin-top:6px;
    }

    .file-type-hint.is-visible { display:block; }

    .upload-error {
        color:#FFA69E;
        display:block;
        font-size:12px;
        margin-left:210px;
        margin-top:6px;
    }

    .current-food-image
    {
        border-radius:6px;
        height:100px;
        object-fit:cover;
        width:120px;
    }

    .transaction-meta
    {
        background:#0f1218;
        border:1px solid #2b2f38;
        border-radius:8px;
        display:grid;
        gap:12px;
        grid-template-columns:repeat(2, minmax(0, 1fr));
        margin-bottom:18px;
        max-width:760px;
        padding:14px;
    }

    .transaction-meta span
    {
        color:#94a3b8;
        display:block;
        font-size:11px;
        font-weight:800;
        margin-bottom:4px;
        text-transform:uppercase;
    }

    .transaction-meta strong
    {
        color:#dbeafe;
        font-size:13px;
    }
  </style>
  </head>
  <body>


       @include('admin.header')
        @include('admin.sidebar')

        
      <div class="page-content">
        <div class="page-header"> 
          <div class="container-fluid form-frame">


            <h1>Update Food</h1>

            <div class="transaction-meta">
                <div>
                    <span>Added At</span>
                    <strong>{{ $food->created_at ? $food->created_at->format('M d, Y g:i A') : 'N/A' }}</strong>
                </div>
                <div>
                    <span>Last Updated</span>
                    <strong>{{ $food->updated_at ? $food->updated_at->format('M d, Y g:i A') : 'N/A' }}</strong>
                </div>
            </div>

            <form class="form-panel" action="{{ url('edit_food', $food->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="div_deg">
                    <label for="">Food Title</label>
                    <input type="text" name="title" value="{{ $food->title }}">
                </div>

                 <div class="div_deg">
                    <label for="">Details</label>
                   <textarea name="details" id="">{{ $food->detail }}</textarea>
                </div>

                 <div class="div_deg">
                    <label for="">Price</label>
                    <input type="text" name="price" value="{{ $food->price }}">
                </div>

                 <div class="div_deg">
                    <label for="">Available Stock</label>
                    <input type="number" name="stock" min="0" value="{{ $food->stock }}">
                </div>

                 <div class="div_deg">
                    <label for="">Current Image</label>
                    <img class="current-food-image" src="/food_img/{{ $food->image }}" alt="">
                    
                </div>

                
                 <div class="div_deg">
                    <label for="">Change Image</label>
                   <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                   <small class="file-type-hint">Accepted image types: .jpg, .jpeg, .png, .webp — maximum 50 MB</small>
                   @error('image')<small class="upload-error">{{ $message }}</small>@enderror

                </div>

                
                 <div class="div_deg">
                    
                 <input class="btn btn-warning" type="submit" value="Update Food">
                    

                </div>  

            </form>


        </div>
      </div>
    </div>
    @include('admin.js')
    <script>
      document.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.addEventListener('change', function () {
          var hint = input.parentElement.querySelector('.file-type-hint');
          if (hint) hint.classList.toggle('is-visible', input.files.length > 0);
        });
      });
    </script>
  </body>
</html>
