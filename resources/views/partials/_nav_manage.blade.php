<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li class="sidebar-brand">
            <h2><a href="#menu-toggle" id="menu-toggle"><span class="fas fa-bars ml-1 mr-2 mt-4"></span>Manage</a></h2>
        </li>
        <li><a href="{{ route('manage.dashboard')   }}"><span class="fas fa-cog      mr-2"></span>Dashboard</a></li>
        <li><a href="{{ route('users.index')        }}"><span class="fas fa-user-cog mr-2"></span>Manage Users</a></li>
        <li><a href="{{ route('roles.index')        }}"><span class="fas fa-user-cog mr-2"></span>Manage Roles</a></li>
        <li><a href="{{ route('permissions.index')  }}"><span class="fas fa-user-cog mr-2"></span>Manage Permissions</a></li>
    </ul>
</div>