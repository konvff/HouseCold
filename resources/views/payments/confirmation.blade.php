@extends('layouts.guest')

@section('title', 'Payment Confirmed')

@push('styles')
<style>
    :root {
        --primary-color: #3b82f6;
        --secondary-color: #10b981;
        --success-color: #059669;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --info-color: #06b6d4;
        --accent-color: #8b5cf6;
        --light-bg: rgba(255, 255, 255, 0.95);
        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
        --shadow-light: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-medium: 0 10px 25px rgba(0, 0, 0, 0.15);
        --shadow-heavy: 0 20px 40px rgba(0, 0, 0, 0.2);
        --shadow-glow: 0 0 30px rgba(59, 130, 246, 0.3);
        --border-radius: 12px;
        --border-radius-lg: 20px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --transition-slow: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .confirmation-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        position: relative;
        overflow: hidden;
    }

    .confirmation-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(circle at 20% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
        animation: backgroundFloat 20s ease-in-out infinite;
    }

    .confirmation-card {
        background: var(--light-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-heavy);
        overflow: hidden;
        position: relative;
        max-width: 700px;
        width: 100%;
        animation: cardSlideIn 0.8s ease-out;
    }

    .confirmation-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color));
    }

    .confirmation-header {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(16, 185, 129, 0.1));
        backdrop-filter: blur(20px);
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .confirmation-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background:
            radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%),
            radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
        animation: headerFloat 15s ease-in-out infinite;
    }

    .success-icon-container {
        position: relative;
        display: inline-block;
        margin-bottom: 2rem;
        z-index: 2;
    }

    .success-icon {
        font-size: 6rem;
        color: var(--success-color);
        animation: successPulse 2s ease-in-out infinite;
        position: relative;
        z-index: 1;
    }

    .success-icon::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120%;
        height: 120%;
        background: radial-gradient(circle, rgba(5, 150, 105, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        animation: iconGlow 2s ease-in-out infinite;
        z-index: -1;
    }

    .floating-icons {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
        z-index: 0;
    }

    .floating-icon {
        position: absolute;
        font-size: 1.5rem;
        opacity: 0.3;
        animation: floatAround 20s linear infinite;
    }

    .floating-icon:nth-child(1) {
        top: 20%;
        left: 10%;
        animation-delay: 0s;
        color: var(--primary-color);
    }

    .floating-icon:nth-child(2) {
        top: 30%;
        right: 15%;
        animation-delay: 5s;
        color: var(--secondary-color);
    }

    .floating-icon:nth-child(3) {
        bottom: 25%;
        left: 20%;
        animation-delay: 10s;
        color: var(--accent-color);
    }

    .floating-icon:nth-child(4) {
        bottom: 35%;
        right: 10%;
        animation-delay: 15s;
        color: var(--info-color);
    }
    }

    .confirmation-title {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: titleSlideIn 1s ease-out 0.3s both;
    }

    .confirmation-subtitle {
        font-size: 1.3rem;
        color: #4b5563;
        font-weight: 500;
        position: relative;
        z-index: 2;
        animation: subtitleSlideIn 1s ease-out 0.5s both;
    }

    .confirmation-body {
        padding: 3rem 2rem;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
    }

    .payment-details {
        background: var(--light-bg);
        border-radius: var(--border-radius);
        padding: 2rem;
        margin: 2rem 0;
        border: 1px solid var(--glass-border);
        box-shadow: var(--shadow-light);
        animation: detailsSlideIn 1s ease-out 0.7s both;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        animation: rowSlideIn 0.6s ease-out both;
    }

    .detail-row:last-child {
        border-bottom: none;
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--success-color);
    }

    .detail-label {
        font-weight: 600;
        color: #374151;
    }

    .detail-value {
        color: #1f2937;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        animation: buttonsSlideIn 1s ease-out 0.9s both;
    }

    .btn-confirmation {
        flex: 1;
        padding: 1rem 2rem;
        border-radius: var(--border-radius);
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .btn-primary-confirmation {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        box-shadow: var(--shadow-medium);
    }

    .btn-primary-confirmation:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-heavy);
        color: white;
    }

    .btn-secondary-confirmation {
        background: var(--light-bg);
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .btn-secondary-confirmation:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }

    .celebration-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1000;
    }

    .particle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: var(--success-color);
        border-radius: 50%;
        animation: particleFall 3s linear infinite;
    }

    .particle:nth-child(odd) {
        background: var(--primary-color);
    }

    .particle:nth-child(3n) {
        background: var(--accent-color);
    }

    /* Beautiful Animations */
    @keyframes backgroundFloat {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(120deg); }
        66% { transform: translate(-20px, 20px) rotate(240deg); }
    }

    @keyframes cardSlideIn {
        from {
            opacity: 0;
            transform: translateY(50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes headerFloat {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        50% { transform: translate(20px, -20px) rotate(180deg); }
    }

    @keyframes successPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    @keyframes iconGlow {
        0%, 100% { opacity: 0.3; transform: translate(-50%, -50%) scale(1); }
        50% { opacity: 0.6; transform: translate(-50%, -50%) scale(1.2); }
    }

    @keyframes floatAround {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(20px, -20px) rotate(90deg); }
        50% { transform: translate(-10px, -30px) rotate(180deg); }
        75% { transform: translate(-30px, 10px) rotate(270deg); }
        100% { transform: translate(0, 0) rotate(360deg); }
    }

    @keyframes titleSlideIn {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes subtitleSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes detailsSlideIn {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes rowSlideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes buttonsSlideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes particleFall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .confirmation-container {
            padding: 1rem 0.5rem;
        }

        .confirmation-title {
            font-size: 2.5rem;
        }

        .confirmation-subtitle {
            font-size: 1.1rem;
        }

        .success-icon {
            font-size: 4rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-confirmation {
            width: 100%;
        }
    }

    @media (max-width: 576px) {
        .confirmation-title {
            font-size: 2rem;
        }

        .confirmation-header {
            padding: 2rem 1rem;
        }

        .confirmation-body {
            padding: 2rem 1rem;
        }

        .payment-details {
            padding: 1.5rem;
        }
    }

    .payment-details {
        background: #f8fafc;
        border-radius: 15px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        border-left: 4px solid #10b981;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .detail-row:last-child {
        border-bottom: none;
        font-weight: 600;
        font-size: 1.1rem;
        color: #059669;
    }

    .detail-label {
        color: #6b7280;
        font-weight: 500;
    }

    .detail-value {
        color: #111827;
        font-weight: 600;
    }

    .celebration-icons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin: 1.5rem 0;
        opacity: 0.7;
    }

    .celebration-icon {
        font-size: 1.5rem;
        animation: float 3s ease-in-out infinite;
    }

    .celebration-icon:nth-child(2) { animation-delay: 0.5s; }
    .celebration-icon:nth-child(3) { animation-delay: 1s; }
    .celebration-icon:nth-child(4) { animation-delay: 1.5s; }

    .next-steps {
        background: #fef3c7;
        border-radius: 15px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        border-left: 4px solid #f59e0b;
    }

    .next-steps h6 {
        color: #92400e;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .next-steps ul {
        margin: 0;
        padding-left: 1.5rem;
        color: #92400e;
    }

    .next-steps li {
        margin-bottom: 0.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .btn-confirmation {
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary-confirmation {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
    }

    .btn-primary-confirmation:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .btn-outline-confirmation {
        background: transparent;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-outline-confirmation:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }
        50% {
            opacity: 1;
            transform: scale(1.05);
        }
        70% {
            transform: scale(0.9);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .confetti {
        position: absolute;
        width: 10px;
        height: 10px;
        background: #4ade80;
        animation: confetti-fall 3s linear infinite;
    }

    .confetti:nth-child(odd) {
        background: #f59e0b;
        animation-delay: 0.5s;
    }

    .confetti:nth-child(even) {
        background: #ef4444;
        animation-delay: 1s;
    }

    @keyframes confetti-fall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }

    .pulse-animation {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(74, 222, 128, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(74, 222, 128, 0);
        }
    }
</style>
@endpush

@section('content')
<!-- Celebration Particles -->
<div class="celebration-particles" id="particles"></div>

<div class="confirmation-container">
    <div class="confirmation-card">
        <div class="confirmation-header">
            <!-- Floating Icons -->
            <div class="floating-icons">
                <i class="fas fa-star floating-icon"></i>
                <i class="fas fa-heart floating-icon"></i>
                <i class="fas fa-gem floating-icon"></i>
                <i class="fas fa-crown floating-icon"></i>
            </div>

            <!-- Success Icon -->
            <div class="success-icon-container">
                <i class="fas fa-check-circle success-icon"></i>
            </div>

            <!-- Title and Subtitle -->
            <h1 class="confirmation-title">🎉 Payment Confirmed!</h1>
            <p class="confirmation-subtitle">Your appointment has been successfully booked and payment authorized</p>
        </div>

        <div class="confirmation-body">
            <div class="celebration-icons">
                <i class="fas fa-star celebration-icon"></i>
                <i class="fas fa-heart celebration-icon"></i>
                <i class="fas fa-thumbs-up celebration-icon"></i>
                <i class="fas fa-gem celebration-icon"></i>
            </div>

            <div class="text-center mb-4">
                <h3 class="text-success mb-2">🎊 Congratulations! 🎊</h3>
                <p class="lead text-muted">Your house call service has been scheduled and your payment is secure!</p>
                @if($isGuest)
                <div class="alert alert-info border-0 mt-3" style="background: linear-gradient(45deg, #dbeafe, #bfdbfe);">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Guest Booking:</strong> You can create an account anytime to track your appointments and manage future bookings.
                </div>
                @endif
            </div>

            @if(isset($appointment))
            <div class="payment-details">
                <h6 class="mb-3">
                    <i class="fas fa-receipt me-2"></i>Payment & Appointment Details
                </h6>
                <div class="detail-row">
                    <span class="detail-label">Appointment ID</span>
                    <span class="detail-value">#{{ $appointment->id }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Service</span>
                    <span class="detail-value">{{ $appointment->serviceType->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Scheduled Date</span>
                    <span class="detail-value">{{ $appointment->scheduled_at->format('M j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Scheduled Time</span>
                    <span class="detail-value">{{ $appointment->scheduled_at->format('g:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer</span>
                    <span class="detail-value">{{ $appointment->customer_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Status</span>
                    <span class="detail-value text-success">
                        <i class="fas fa-shield-alt me-1"></i>Authorized & Secure
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount Authorized</span>
                    <span class="detail-value text-success">${{ number_format($appointment->estimated_cost, 2) }}</span>
                </div>
            </div>
            @endif

            <div class="next-steps">
                <h6>
                    <i class="fas fa-list-check me-2"></i>What Happens Next?
                </h6>
                <ul>
                    <li><strong>📧 Confirmation Email:</strong> You'll receive a detailed confirmation email shortly</li>
                    <li><strong>👨‍🔧 Technician Assignment:</strong> We'll assign a qualified technician to your appointment</li>
                    <li><strong>📱 SMS Notifications:</strong> You'll get updates via text message</li>
                    <li><strong>💳 Payment Processing:</strong> Your card will only be charged after service completion</li>
                    <li><strong>⭐ Service Delivery:</strong> Our technician will arrive at your scheduled time</li>
                </ul>
            </div>

            <div class="text-center mb-4">
                <div class="alert alert-success border-0" style="background: linear-gradient(45deg, #d1fae5, #a7f3d0);">
                    <i class="fas fa-lock me-2"></i>
                    <strong>Your payment is 100% secure!</strong> We use bank-level encryption and your card details are never stored on our servers.
                </div>
            </div>



            <!-- Action Buttons -->
            <div class="action-buttons">
                @if($isGuest)
                    <a href="{{ route('login') }}" class="btn-confirmation btn-primary-confirmation">
                        <i class="fas fa-sign-in-alt me-2"></i>Login to Track
                    </a>
                    <a href="{{ route('home') }}" class="btn-confirmation btn-secondary-confirmation">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                @else
                    <a href="{{ route('appointments.show', $appointment) }}" class="btn-confirmation btn-primary-confirmation">
                        <i class="fas fa-calendar-check me-2"></i>View Appointment
                    </a>
                    <a href="{{ route('home') }}" class="btn-confirmation btn-secondary-confirmation">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                @endif
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-phone me-1"></i>
                    Need help? Call us at <strong>(555) 123-4567</strong> or email <strong>support@housecallpro.com</strong>
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some interactive effects
    const successIcon = document.querySelector('.success-icon');
    const card = document.querySelector('.confirmation-card');

    // Add a subtle hover effect to the card
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.transition = 'transform 0.3s ease';
    });

    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });

    // Add click effect to success icon
    successIcon.addEventListener('click', function() {
        this.style.transform = 'scale(1.2)';
        setTimeout(() => {
            this.style.transform = 'scale(1)';
        }, 200);
    });

    // Auto-scroll to top
    window.scrollTo(0, 0);

    // Add a success sound effect (if browser supports it)
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance('Payment confirmed successfully!');
        utterance.rate = 0.8;
        utterance.pitch = 1.2;
        speechSynthesis.speak(utterance);
    }
});
</script>
@endpush
