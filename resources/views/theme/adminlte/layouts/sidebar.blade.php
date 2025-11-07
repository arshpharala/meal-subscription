  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link text-center">
      <span class="brand-text font-weight-light text-center">{{ env('APP_NAME') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('theme/adminlte/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2"
            alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ auth('admin')->user()->name }}</a>
        </div>
      </div>


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          @if (auth()->user()->can('viewAny', App\Models\Admin::class) ||
                  auth()->user()->can('viewAny', App\Models\Role::class) ||
                  auth()->user()->can('viewAny', App\Models\Permission::class) ||
                  auth()->user()->can('viewAny', App\Models\Module::class))
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>
                  User Management
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @can('viewAny', App\Models\Admin::class)
                  <li class="nav-item">
                    <a href="{{ route('admin.auth.admins.index') }}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Users</p>
                    </a>
                  </li>
                @endcan
                @can('viewAny', App\Models\Role::class)
                  <li class="nav-item">
                    <a href="{{ route('admin.auth.roles.index') }}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Roles</p>
                    </a>
                  </li>
                @endcan

                @can('viewAny', App\Models\Permission::class)
                  <li class="nav-item">
                    <a href="{{ route('admin.auth.permissions.index') }}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Permissions</p>
                    </a>
                  </li>
                @endcan

                @can('viewAny', App\Models\Module::class)
                  <li class="nav-item">
                    <a href="{{ route('admin.auth.modules.index') }}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Modules</p>
                    </a>
                  </li>
                @endcan
              </ul>
            </li>
          @endif

          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-search-dollar"></i>
              <p>
                Sales
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('admin.sales.customers.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Customers</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.sales.payment-links.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Payment Links</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('admin.sales.subscriptions.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Subscriptions</p>
                </a>
              </li>
              {{-- <li class="nav-item">
                <a href="{{ route('admin.sales.subscribers.index') }}" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Subscribers</p>
                </a>
              </li> --}}
            </ul>
          </li>

          @if (auth()->user()->can('viewAny', App\Models\Catalog\Meal::class) ||
                  auth()->user()->can('viewAny', App\Models\Catalog\Package::class) ||
                  auth()->user()->can('viewAny', App\Models\Catalog\Calorie::class))
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-shopping-bag"></i>
                <p>
                  Catalog
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">

                @can('viewAny', App\Models\Catalog\Meal::class)
                  <li class="nav-item">
                    <a href="{{ route('admin.catalog.meals.index') }}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Meals</p>
                    </a>
                  </li>
                @endcan

                @can('viewAny', App\Models\Catalog\Package::class)
                  <li class="nav-item">
                    <a href="{{ route('admin.catalog.packages.index') }}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Packages</p>
                    </a>
                  </li>
                @endcan

                @can('viewAny', App\Models\Catalog\Calorie::class)
                  <li class="nav-item">
                    <a href="{{ route('admin.catalog.calories.index') }}" class="nav-link">
                      <i class="far fa-circle nav-icon"></i>
                      <p>Calories</p>
                    </a>
                  </li>
                @endcan



              </ul>
            </li>
          @endif



        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
