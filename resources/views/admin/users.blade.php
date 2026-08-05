<!DOCTYPE html>
<html>
  <head>
    @include('admin.css')
    <style>
      .users-frame {
        background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.08), transparent 28%), #0c0d10;
        border: 1px solid #2b2f38;
        border-radius: 8px;
        color: #fff;
        margin: 0;
        min-height: calc(100vh - 120px);
        padding: 24px;
      }

      .users-frame h2 {
        color: #fff;
        font-size: 26px;
        font-weight: 800;
        margin-bottom: 18px;
      }

      .users-table-panel {
        background: #15171c;
        border: 1px solid #2b2f38;
        border-radius: 8px;
        overflow-x: visible;
      }

      .users-table {
        border-collapse: collapse;
        table-layout: fixed;
        width: 100%;
      }

      .users-table th {
        background: #101116;
        border-bottom: 1px solid #2b2f38;
        color: #a6abb6;
        font-size: 12px;
        letter-spacing: 0;
        padding: 14px 16px;
        text-align: left;
        text-transform: uppercase;
        white-space: normal;
        word-break: break-word;
      }

      .users-table td {
        background: #15171c;
        border-top: 1px solid #2b2f38;
        color: #f3f4f6;
        padding: 14px 16px;
        vertical-align: top;
        white-space: normal;
        word-break: break-word;
      }

      .users-table tr:hover td {
        background: #1f2229;
      }

      .role-badge {
        background: #242424;
        border-radius: 999px;
        color: #fff;
        display: inline-flex;
        font-size: 12px;
        font-weight: 800;
        padding: 5px 10px;
        text-transform: uppercase;
      }

      .role-admin { background: #b91c1c; }
      .role-staff { background: #2563eb; }
      .role-user { background: #15803d; }
    </style>
  </head>
  <body>
    @include('admin.header')
    @include('admin.sidebar')

    <div class="page-content">
      <div class="page-header">
        <div class="container-fluid users-frame">
          <h2>User Details</h2>

          <div class="users-table-panel">
            <table class="users-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Address</th>
                  <th>Role</th>
                  <th>Staff Type</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                @forelse($users as $user)
                  <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? 'No phone yet' }}</td>
                    <td>{{ $user->address ?? 'No address yet' }}</td>
                    <td>
                      <span class="role-badge role-{{ $user->usertype }}">
                        {{ $user->usertype }}
                      </span>
                    </td>
                    <td>{{ $user->staff_role ? ucfirst($user->staff_role) : '-' }}</td>
                    <td>{{ $user->created_at ? $user->created_at->format('M d, Y g:i A') : '' }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8">No users found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    @include('admin.js')
  </body>
</html>
