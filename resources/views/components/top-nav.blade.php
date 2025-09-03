<nav class="top-navbar">
    <div class="nav-left">
        <button class="btn btn-link sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="breadcrumb-container">
            @yield('breadcrumb', 'Dashboard')
        </div>
    </div>

    <div class="nav-right">
        <div class="nav-actions">
            @if(Auth::user()->isTechnician())
                <div class="timer-widget me-3">
                    <i class="fas fa-stopwatch text-warning"></i>
                    <span id="currentTimer" class="ms-2">00:00:00</span>
                </div>
            @endif

            <div class="notifications dropdown">
                <button class="btn btn-link position-relative" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ Auth::user()->isAdmin() ? App\Models\Appointment::where('status', 'pending')->count() : 0 }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if(Auth::user()->isAdmin())
                        @php
                            $pendingAppointments = App\Models\Appointment::where('status', 'pending')->take(5)->get();
                        @endphp
                        @forelse($pendingAppointments as $appointment)
                            <li><a class="dropdown-item" href="{{ route('appointments.show', $appointment) }}">
                                <small class="text-muted">{{ $appointment->created_at->diffForHumans() }}</small><br>
                                <strong>{{ $appointment->customer_name }}</strong> - {{ $appointment->serviceType->name }}
                            </a></li>
                        @empty
                            <li><span class="dropdown-item-text">No pending appointments</span></li>
                        @endforelse
                    @else
                        <li><span class="dropdown-item-text">No new notifications</span></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="user-menu dropdown">
            <button class="btn btn-link d-flex align-items-center" data-bs-toggle="dropdown">
                <div class="user-avatar me-2">
                    <i class="fas fa-user-circle fa-lg text-primary"></i>
                </div>
                <div class="user-info d-none d-md-block">
                    <small class="text-muted d-block">{{ ucfirst(Auth::user()->role) }}</small>
                    <strong>{{ Auth::user()->name }}</strong>
                </div>
                <i class="fas fa-chevron-down ms-2"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-start">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
.top-navbar {
    position: fixed;
    top: 0;
    left: 280px;
    right: 0;
    height: 70px;
    background: white;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1.5rem;
    z-index: 999;
    transition: left 0.3s ease;
}

.nav-left {
    display: flex;
    align-items: center;
}

.sidebar-toggle {
    background: none;
    border: none;
    color: #6c757d;
    font-size: 1.2rem;
    padding: 0.5rem;
    margin-right: 1rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.sidebar-toggle:hover {
    background: #f8f9fa;
    color: #495057;
}

.breadcrumb-container {
    font-size: 1.1rem;
    font-weight: 500;
    color: #495057;
}

.nav-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.nav-actions {
    display: flex;
    align-items: center;
}

.timer-widget {
    background: #fff3cd;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #ffeaa7;
    font-weight: 600;
    color: #856404;
}

.notifications .btn {
    color: #6c757d;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.notifications .btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.user-menu .btn {
    color: #495057;
    text-decoration: none;
    padding: 0.5rem;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
}

.user-menu .btn:hover {
    background: #f8f9fa;
}

.user-avatar {
    color: #6c757d;
}

.user-info small {
    font-size: 0.75rem;
    line-height: 1;
}

.user-info strong {
    font-size: 0.9rem;
    line-height: 1.2;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.5rem;
    padding: 0.5rem 0;
}

.dropdown-item {
    padding: 0.5rem 1rem;
    color: #495057;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: #f8f9fa;
    color: #212529;
}

.dropdown-item i {
    width: 16px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .top-navbar {
        left: 0;
    }

    .user-info {
        display: none !important;
    }

    .breadcrumb-container {
        font-size: 1rem;
    }
}

/* When sidebar is closed on mobile */
@media (max-width: 768px) {
    .sidebar-open .top-navbar {
        left: 0;
    }
}

/* Main content adjustment */
.main-content {
    margin-left: 280px;
    margin-top: 70px;
    padding: 2rem;
    min-height: calc(100vh - 70px);
    transition: margin-left 0.3s ease;
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');
            }
        });
    }

    // Update timer for technicians
    @if(Auth::user()->isTechnician())
        updateTimer();
        setInterval(updateTimer, 1000);
    @endif
});

@if(Auth::user()->isTechnician())
function updateTimer() {
    // This would be updated with actual timer data from the backend
    const timerElement = document.getElementById('currentTimer');
    if (timerElement) {
        // Placeholder timer - replace with actual implementation
        const now = new Date();
        const timeString = now.toTimeString().split(' ')[0];
        timerElement.textContent = timeString;
    }
}
@endif
</script>
