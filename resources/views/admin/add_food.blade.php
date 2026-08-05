<!DOCTYPE html>
<html>
  <head> 
    
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

        label
        {
            display: inline-block;
            width: 200px;
            color: #a6abb6;
            font-weight:700;
        }


        .div_deg
        {
            padding: 10px 0;
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
            color: #a6abb6;
            display: none;
            font-size: 12px;
            margin-left: 210px;
            margin-top: 6px;
        }

        .file-type-hint.is-visible { display: block; }

        .upload-error {
            color: #FFA69E;
            display: block;
            font-size: 12px;
            margin-left: 210px;
            margin-top: 6px;
        }

     </style>
  </head>
  <body>


       @include('admin.header')
        @include('admin.sidebar')

        
      <div class="page-content">
        <div class="page-header"> 
          <div class="container-fluid form-frame">

        <form class="form-panel" action="{{ url('upload_food') }}" method="post" enctype="multipart/form-data" autocomplete="off">

        @csrf

        <div class="div_deg">
          <label for="">Food title</label>
          <input type="text" name="title" autocomplete="off" required>
        </div>

        <div class="div_deg">
          <label for="">Food details</label>
          <textarea name="details" cols="50" rows="5" autocomplete="off" required></textarea>
        </div>

        <div class="div_deg">
          <label for="">Price</label>
          <input type="number" name="price" min="0" step="0.01" autocomplete="off" required>
        </div>

        <div class="div_deg">
          <label for="">Available Stock</label>
          <input type="number" name="stock" min="0" value="0" autocomplete="off" required>
        </div>

        <div class="div_deg">
          <label for="">Image</label>
          <input type="file" name="img" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
          <small class="file-type-hint">Accepted image types: .jpg, .jpeg, .png, .webp — maximum 50 MB</small>
          @error('img')<small class="upload-error">{{ $message }}</small>@enderror
        </div>

        <div class="div_deg">
          <input type="submit" value="Add Food" class="btn btn-warning">
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
