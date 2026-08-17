 <div class="d-flex align-items-stretch">
      <!-- Sidebar Navigation-->
      <nav id="sidebar">
        <!-- Sidebar Header-->
        <div class="sidebar-header d-flex align-items-center">
          <div class="avatar"><img src="admin/img/avatar-6.jpg" alt="..." class="img-fluid rounded-circle"></div>
          <div class="title">
            <h1 class="h5">{{ str(Auth::user()->name ?? 'Mi Cusina')->title() }}</h1>
            <p>
              {{ (Auth::user()->staff_role ?? Auth::user()->usertype) === 'admin' ? 'Administrator' : str(Auth::user()->staff_role ?? Auth::user()->usertype ?? 'User')->title() }}
            </p>
          </div>
        </div>
        <!-- Sidebar Navidation Menus--><span class="heading">Main</span>
        <ul class="list-unstyled">
                <li class="{{ request()->is('home') ? 'active' : '' }}"><a href="{{ url('home') }}"> <i class="icon-home"></i>Dashboard </a></li>
                @if(Auth::check() && Auth::user()->usertype == 'admin')
                <li class="food-menu {{ request()->is('view_food') || request()->is('add_food') || request()->is('inventory') ? 'active' : '' }}">
                  <a href="#foodMenu" data-toggle="collapse" aria-expanded="false">
                    <i class="icon-windows"></i>Food
                  </a>
                  <ul id="foodMenu" class="collapse list-unstyled">
                    <li class="{{ request()->is('view_food') ? 'active' : '' }}">
                      <a href="{{ url('view_food') }}">View Food</a>
                    </li>
                    <li class="{{ request()->is('add_food') ? 'active' : '' }}">
                      <a href="{{ url('add_food') }}">Add Food</a>
                    </li>
                    <li class="{{ request()->is('inventory') ? 'active' : '' }}">
                      <a href="{{ url('inventory') }}">Inventory</a>
                    </li>
                  </ul>
                </li>
                @endif
                <li class="{{ request()->is('orders') ? 'active' : '' }}">
                  <a href="{{ url('orders') }}"> <i class="icon-logout"></i>Orders</a>
                </li>

                @if(Auth::check() && Auth::user()->usertype == 'admin')
                <li class="{{ request()->is('sales-report') ? 'active' : '' }}">
                  <a href="{{ route('admin.sales-report') }}"><img src="{{ asset('assets/imgs/mi-cusina-transparent.png') }}" alt="" style="height:20px; margin-right:10px; object-fit:contain; vertical-align:middle; width:20px;">Sales Report</a>
                </li>
                <li class="{{ request()->is('transaction-history') ? 'active' : '' }}">
                  <a href="{{ route('admin.transaction-history') }}"> <i class="icon-list"></i>Transaction History</a>
                </li>
                @endif

                <li class="{{ request()->is('riders') ? 'active' : '' }}">
                  <a href="{{ url('riders') }}"> <i class="fa fa-motorcycle"></i>Riders</a>
                </li>

                <li class="{{ request()->is('reservations') ? 'active' : '' }}">
                  <a href="{{ url('reservations') }}"> <i class="icon-logout"></i>Book a Table</a>
                </li>
                @if(Auth::check() && Auth::user()->usertype == 'admin')
                <li class="{{ request()->is('users') ? 'active' : '' }}">
                  <a href="{{ url('users') }}"> <i class="icon-user"></i>Users</a>
                </li>

                <li class="{{ request()->is('add_staff') ? 'active' : '' }}">
                  <a href="{{ url('add_staff') }}"> <i class="icon-user-1"></i>Create Staff</a>
                </li>
                @endif
        </ul>
      </nav>
    </div>
