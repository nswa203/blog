<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li class="sidebar-brand mt-3">
            <h2><a href="#menu-toggle" id="menu-toggle"><span class="fas fa-bars ml-1 mr-2 mt-4"></span>Manage</a></h2>
        </li>
        <li><a href="{{ route('manage.dashboard')   }}" class="{{ Request::is('manage'            	)?'active':'' }}"><span class="fas fa-cog      mr-2"></span>Dashboard</a></li>
        <li><a href="{{ route('users.index')        }}" class="{{ Request::is('manage/users*'      	)?'active':'' }}"><span class="fas fa-user-cog mr-2"></span>Manage Users</a></li>
        <li><a href="{{ route('roles.index')        }}" class="{{ Request::is('manage/roles*'      	)?'active':'' }}"><span class="fas fa-user-cog mr-2"></span>Manage Roles</a></li>
        <li><a href="{{ route('permissions.index') 	}}" class="{{ Request::is('manage/permissions*'	)?'active':'' }}"><span class="fas fa-user-cog mr-2"></span>Manage Permissions</a></li>
        <li><a href="{{ route('posts.index') 		}}" class="{{ Request::is('manage/posts*'		)?'active':'' }}"><span class="fas fa-file-alt mr-3"></span>Manage Posts</a></li>
    </ul>
</div>
