<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <i class="fas fa-tools text-primary me-2"></i>
            <h5 class="mb-0 text-white">HouseCall Pro</h5>
        </div>
        <button class="btn-close btn-close-white" id="sidebarClose"></button>
    </div>

    <div class="sidebar-user">
        <div class="d-flex align-items-center">
            <div class="avatar">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="user-info">
                <h6 class="mb-0 text-white">{{ Auth::user()->name }}</h6>
                <small class="text-light">{{ ucfirst(Auth::user()->role) }}</small>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        @if(Auth::user()->isAdmin())
            {{-- Admin Navigation --}}
            <div class="nav-section">
                <h6 class="nav-section-title">Dashboard</h6>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Overview</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-section">
                <h6 class="nav-section-title">Management</h6>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                            <i class="fas fa-calendar-check"></i>
                            <span>Appointments</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('technicians.index') }}" class="nav-link {{ request()->routeIs('technicians.*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog"></i>
                            <span>Technicians</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('service-types.index') }}" class="nav-link {{ request()->routeIs('service-types.*') ? 'active' : '' }}">
                            <i class="fas fa-cogs"></i>
                            <span>Service Types</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('technician-availabilities.index') }}" class="nav-link {{ request()->routeIs('technician-availabilities.*') ? 'active' : '' }}">
                            <i class="fas fa-clock"></i>
                            <span>Time Slots</span>
                        </a>
                    </li>
                </ul>
            </div>

        @elseif(Auth::user()->isTechnician())
            {{-- Technician Navigation --}}
            <div class="nav-section">
                <h6 class="nav-section-title">Dashboard</h6>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('technician.dashboard') }}" class="nav-link {{ request()->routeIs('technician.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Overview</span>
                        </a>
                    </li>
                </ul>
            </div>

            {{--  <div class="nav-section">
                <h6 class="nav-section-title">Work</h6>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('technician.dashboard') }}#pending" class="nav-link">
                            <i class="fas fa-clock"></i>
                            <span>Pending Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('technician.dashboard') }}#confirmed" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Confirmed Jobs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('technician.dashboard') }}#completed" class="nav-link">
                            <i class="fas fa-check-circle"></i>
                            <span>Completed Jobs</span>
                        </a>
                    </li>
                </ul>
            </div>  --}}

            <div class="nav-section">
                <h6 class="nav-section-title">Tools</h6>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('time-logs.current') }}" class="nav-link">
                            <i class="fas fa-stopwatch"></i>
                            <span>Time Tracker</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('technician-availabilities.my-availability') }}" class="nav-link">
                            <i class="fas fa-clock"></i>
                            <span>My Availability</span>
                        </a>
                    </li>
                </ul>
            </div>

        @else
            {{-- Customer Navigation --}}
            <div class="nav-section">
                <h6 class="nav-section-title">Dashboard</h6>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Overview</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="nav-section">
                <h6 class="nav-section-title">Services</h6>
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="{{ route('appointments.create') }}" class="nav-link {{ request()->routeIs('appointments.create') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle"></i>
                            <span>Book Appointment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.index') ? 'active' : '' }}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>My Appointments</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        <div class="nav-section">
            <h6 class="nav-section-title">Account</h6>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>

<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: 280px;
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    z-index: 1000;
    transition: transform 0.3s ease;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sidebar-user {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.avatar {
    margin-right: 1rem;
}

.user-info h6 {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.user-info small {
    font-size: 0.75rem;
    opacity: 0.8;
}

.sidebar-nav {
    padding: 1rem 0;
    overflow-y: auto;
    height: calc(100vh - 200px);
}

.nav-section {
    margin-bottom: 2rem;
}

.nav-section-title {
    color: rgba(255,255,255,0.6);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 0 1.5rem;
    margin-bottom: 0.75rem;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 0.25rem;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    border-left-color: #3498db;
}

.nav-link.active {
    background: rgba(52, 152, 219, 0.2);
    color: white;
    border-left-color: #3498db;
}

.nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    font-size: 0.9rem;
}

.nav-link span {
    font-size: 0.9rem;
    font-weight: 500;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar.show {
        transform: translateX(0);
    }
}

/* Overlay for mobile */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 999;
    display: none;
}

.sidebar-overlay.show {
    display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
        });
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            document.body.classList.toggle('sidebar-open');
        });
    }

    // Close sidebar when clicking overlay
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('sidebar-overlay')) {
            sidebar.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }
    });
});
</script>
