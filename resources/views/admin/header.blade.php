<header class="header">
  <nav class="navbar navbar-expand-lg">
    <div class="search-panel">
      <div class="search-inner d-flex align-items-center justify-content-center">
        <div class="close-btn">Close <i class="fa fa-close"></i></div>
        <form id="searchForm" action="#">
          <div class="form-group">
            <input type="search" name="search" placeholder="What are you searching for...">
            <button type="submit" class="submit">Search</button>
          </div>
        </form>
      </div>
    </div>
    <div class="container-fluid d-flex align-items-center">
      <a href="{{ url('home') }}" class="admin-brand" aria-label="Mi Cusina admin home">
        <img src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="Mi Cusina">
        <span>Mi Cusina</span>
      </a>

      <div class="admin-user dropdown">
        <button class="admin-user-trigger dropdown-toggle" type="button" id="adminUserMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="{{ Auth::user()->profile_photo_path ? Auth::user()->profile_photo_url : asset('admin/img/avatar-6.jpg') }}" alt="{{ Auth::user()->name ?? 'Admin' }}">
        </button>

        <div class="dropdown-menu dropdown-menu-right admin-user-menu" aria-labelledby="adminUserMenu">
          <div class="admin-user-info">
            <strong>{{ Auth::user()->name ?? 'Mi Cusina' }}</strong>
            <span>Logged in as {{ ucfirst(Auth::user()->staff_role ?? Auth::user()->usertype ?? 'Admin') }}</span>
          </div>
          <div class="dropdown-divider"></div>
          <form class="admin-photo-form" method="POST" action="{{ route('admin.profile-photo.update') }}" enctype="multipart/form-data">
            @csrf
            <label class="admin-photo-label" for="adminProfilePhoto">
              <i class="fa fa-camera"></i>
              Upload Profile Picture
            </label>
            <input id="adminProfilePhoto" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" hidden>
            @error('photo')<small class="admin-photo-error">{{ $message }}</small>@enderror
          </form>
          <div class="dropdown-divider"></div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="dropdown-item" type="submit">Log Out</button>
          </form>
        </div>
      </div>
    </div>
  </nav>
</header>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var photoInput = document.getElementById('adminProfilePhoto');
    if (photoInput) {
      photoInput.addEventListener('change', function () {
        if (photoInput.files.length) photoInput.form.submit();
      });
    }
  });
</script>
