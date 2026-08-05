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
          display:inline-block;
          width:200px;
          color:#a6abb6;
          font-weight:700;
      }

      .div_deg
      {
          padding:10px ;
      }

      h1
      {
          color:white;
          padding-bottom:20px;
      }

      input,
      select
      {
          width:calc(100% - 210px);
      }

      input[type="submit"]
      {
          width:auto;
      }
    </style>
  </head>
  <body>
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-content">
      <div class="page-header">
        <div class="container-fluid form-frame">

          <h1>Create Staff</h1>

          @if(session('message'))
            <div class="alert alert-success">
              {{ session('message') }}
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form class="form-panel" action="{{ url('store_staff') }}" method="post">
            @csrf

            <div class="div_deg">
              <label for="name">Name</label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="div_deg">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="div_deg">
              <label for="phone">Phone</label>
              <input type="tel" id="phone" name="phone" value="{{ old('phone', '+639') }}" inputmode="tel" pattern="\+639[0-9]{9}" maxlength="13" required oninput="this.value = '+639' + this.value.replace(/[^0-9]/g, '').replace(/^639/, '').slice(0, 9)">
            </div>

            <div class="div_deg">
              <label for="address">Address</label>
              <input type="text" id="address" name="address" value="{{ old('address') }}">
            </div>

            <div class="div_deg">
              <label for="staff_role">Staff Type</label>
              <select id="staff_role" name="staff_role" required>
                <option value="">Select staff type</option>
                <option value="cashier" {{ old('staff_role') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                <option value="rider" {{ old('staff_role') === 'rider' ? 'selected' : '' }}>Rider</option>
              </select>
            </div>

            <div class="div_deg">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" minlength="8" maxlength="255" title="Password must be at least 8 characters." required>
            </div>

            <div class="div_deg">
              <label for="password_confirmation">Confirm Password</label>
              <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" maxlength="255" title="Please type the same password again." required>
            </div>

            <div class="div_deg">
              <input type="submit" value="Create Staff" class="btn btn-warning">
            </div>
          </form>

        </div>
      </div>
    </div>

    @include('admin.js')
  </body>
</html>
