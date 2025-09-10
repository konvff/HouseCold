@props(['message' => 'Payment processed successfully!', 'type' => 'success'])

<div class="payment-success-alert alert alert-{{ $type }} border-0 shadow-lg" style="
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-radius: 15px;
    border-left: 4px solid #10b981;
    animation: slideInDown 0.5s ease-out;
">
    <div class="d-flex align-items-center">
        <div class="success-icon me-3" style="
            width: 50px;
            height: 50px;
            background: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        ">
            <i class="fas fa-check text-white" style="font-size: 1.2rem;"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-1" style="color: #065f46; font-weight: 600;">
                <i class="fas fa-shield-alt me-2"></i>Payment Confirmed!
            </h6>
            <p class="mb-0" style="color: #047857; font-size: 0.95rem;">
                {{ $message }}
            </p>
        </div>
        <div class="celebration-icons" style="opacity: 0.7;">
            <i class="fas fa-star text-warning me-1" style="animation: float 2s ease-in-out infinite;"></i>
            <i class="fas fa-heart text-danger me-1" style="animation: float 2s ease-in-out infinite; animation-delay: 0.5s;"></i>
            <i class="fas fa-thumbs-up text-primary" style="animation: float 2s ease-in-out infinite; animation-delay: 1s;"></i>
        </div>
    </div>
</div>

<style>
@keyframes slideInDown {
    0% {
        opacity: 0;
        transform: translateY(-30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
}
</style>
