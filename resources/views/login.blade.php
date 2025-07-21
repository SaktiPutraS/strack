<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>PIN Login</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('image/favicon.ico') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('image/favicon.ico') }}">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('image/favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('image/favicon.ico') }}">

    <!-- Android Chrome Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('image/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('image/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('image/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/favicon.ico') }}">

    <!-- Microsoft Tiles -->
    <meta name="msapplication-TileImage" content="{{ asset('image/favicon.ico') }}">
    <meta name="msapplication-TileColor" content="#7c3aed">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 60%, rgba(124, 58, 237, 0.1) 0%, transparent 50%);
            animation: backgroundShift 10s ease-in-out infinite;
        }

        @keyframes backgroundShift {

            0%,
            100% {
                transform: translateX(0px) translateY(0px);
            }

            50% {
                transform: translateX(20px) translateY(-20px);
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow:
                0 32px 64px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 380px;
            position: relative;
            z-index: 10;
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
        }

        .login-header {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: rotate 8s linear infinite;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .login-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            position: relative;
            z-index: 1;
            font-weight: 400;
        }

        .login-form {
            padding: 40px 30px;
        }

        .pin-display {
            text-align: center;
            margin-bottom: 30px;
        }

        .pin-dots {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .pin-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .pin-dot.filled {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
        }

        .pin-dot.filled::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            animation: pulse 0.3s ease;
        }

        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(0);
            }

            50% {
                transform: translate(-50%, -50%) scale(1.5);
            }

            100% {
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .pin-keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .pin-key {
            aspect-ratio: 1;
            border: none;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.8);
            color: #374151;
            font-size: 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
            min-height: 60px;
            min-width: 60px;
        }

        .pin-key::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .pin-key:hover::before {
            opacity: 0.1;
        }

        .pin-key:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.2);
        }

        .pin-key:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
        }

        .pin-key span {
            position: relative;
            z-index: 1;
        }

        .pin-key.zero {
            grid-column: 2;
        }

        .pin-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .pin-action {
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.8);
            color: #7c3aed;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
        }

        .pin-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.2);
            background: rgba(124, 58, 237, 0.1);
        }

        .pin-action:active {
            transform: translateY(0);
        }

        .error-message {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border: 1px solid #fecaca;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .login-button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            opacity: 0.5;
            pointer-events: none;
            margin-top: 20px;
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            touch-action: manipulation;
        }

        .login-button.active {
            opacity: 1;
            pointer-events: auto;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(124, 58, 237, 0.4);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .hidden-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        /* Success animation */
        .success-animation {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 600;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .success-animation.show {
            opacity: 1;
            pointer-events: auto;
        }

        /* iPhone 11 & 13 Specific Optimizations */
        @media only screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2),
        only screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) {
            body {
                padding: 0;
                background-attachment: fixed;
            }

            .login-container {
                max-width: 340px;
                margin: 0 auto;
                border-radius: 28px;
                min-height: 580px;
                display: flex;
                flex-direction: column;
            }

            .login-header {
                padding: 35px 25px 30px;
                flex-shrink: 0;
            }

            .login-form {
                padding: 35px 25px 30px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            .pin-keypad {
                gap: 18px;
                margin-bottom: 25px;
            }

            .pin-key {
                font-size: 26px;
                min-height: 70px;
                min-width: 70px;
                border-radius: 22px;
            }

            .pin-action {
                width: 65px;
                height: 65px;
                font-size: 22px;
            }

            .pin-dots {
                gap: 14px;
                margin-bottom: 25px;
            }

            .pin-dot {
                width: 18px;
                height: 18px;
            }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .login-container {
                margin: 0;
                border-radius: 20px;
                min-height: calc(100vh - 30px);
                display: flex;
                flex-direction: column;
            }

            .login-header {
                padding: 30px 20px 25px;
                flex-shrink: 0;
            }

            .login-title {
                font-size: 24px;
            }

            .login-form {
                padding: 30px 20px;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .pin-keypad {
                gap: 14px;
                margin-bottom: 20px;
            }

            .pin-key {
                font-size: 22px;
                border-radius: 18px;
                min-height: 65px;
                min-width: 65px;
            }

            .pin-action {
                width: 60px;
                height: 60px;
                font-size: 18px;
            }

            .pin-dots {
                gap: 12px;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 320px) {
            .login-header {
                padding: 25px 15px 20px;
            }

            .login-form {
                padding: 25px 15px;
            }

            .pin-keypad {
                gap: 10px;
            }
        }

        /* Loading state */
        .loading {
            pointer-events: none;
        }

        .loading .pin-keypad {
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">Masukkan PIN</h1>
            <p class="login-subtitle">Gunakan PIN 6 digit untuk mengakses</p>
        </div>

        <div class="login-form">
            @if (session('error'))
                <div class="error-message">
                    PIN yang Anda masukkan salah!
                </div>
            @endif

            <form method="POST" action="/login" id="loginForm">
                @csrf
                <input type="hidden" name="password" id="hiddenPassword" class="hidden-input">

                <div class="pin-display">
                    <div class="pin-dots">
                        <div class="pin-dot" data-index="0"></div>
                        <div class="pin-dot" data-index="1"></div>
                        <div class="pin-dot" data-index="2"></div>
                        <div class="pin-dot" data-index="3"></div>
                        <div class="pin-dot" data-index="4"></div>
                        <div class="pin-dot" data-index="5"></div>
                    </div>
                </div>

                <div class="pin-keypad">
                    <button type="button" class="pin-key" data-number="1"><span>1</span></button>
                    <button type="button" class="pin-key" data-number="2"><span>2</span></button>
                    <button type="button" class="pin-key" data-number="3"><span>3</span></button>
                    <button type="button" class="pin-key" data-number="4"><span>4</span></button>
                    <button type="button" class="pin-key" data-number="5"><span>5</span></button>
                    <button type="button" class="pin-key" data-number="6"><span>6</span></button>
                    <button type="button" class="pin-key" data-number="7"><span>7</span></button>
                    <button type="button" class="pin-key" data-number="8"><span>8</span></button>
                    <button type="button" class="pin-key" data-number="9"><span>9</span></button>
                    <button type="button" class="pin-key zero" data-number="0"><span>0</span></button>
                </div>

                <div class="pin-actions">
                    <button type="button" class="pin-action" id="clearPin">
                        <span>⌫</span>
                    </button>
                    <button type="button" class="pin-action" id="clearAll">
                        <span>✕</span>
                    </button>
                </div>

                <button type="submit" class="login-button" id="loginBtn">
                    <span>Masuk</span>
                </button>
            </form>
        </div>
    </div>

    <div class="success-animation" id="successAnimation">
        <div>
            <div style="font-size: 48px; margin-bottom: 20px; text-align: center;">✓</div>
            <div>Login Berhasil!</div>
        </div>
    </div>

    <script>
        let currentPin = '';
        const correctPin = '123698';
        const maxPinLength = 6;

        // Get DOM elements
        const pinDots = document.querySelectorAll('.pin-dot');
        const pinKeys = document.querySelectorAll('.pin-key');
        const clearPin = document.getElementById('clearPin');
        const clearAll = document.getElementById('clearAll');
        const loginBtn = document.getElementById('loginBtn');
        const hiddenPassword = document.getElementById('hiddenPassword');
        const loginForm = document.getElementById('loginForm');
        const successAnimation = document.getElementById('successAnimation');

        // Add event listeners to number keys
        pinKeys.forEach(key => {
            key.addEventListener('click', function(e) {
                e.preventDefault();
                const number = this.getAttribute('data-number');
                addNumber(number);
            });

            // Add touch events for better mobile response
            key.addEventListener('touchstart', function(e) {
                e.preventDefault();
                this.style.transform = 'translateY(2px) scale(0.95)';
            });

            key.addEventListener('touchend', function(e) {
                e.preventDefault();
                this.style.transform = '';
                const number = this.getAttribute('data-number');
                addNumber(number);
            });
        });

        // Clear last digit
        clearPin.addEventListener('click', function(e) {
            e.preventDefault();
            removeLastNumber();
        });

        clearPin.addEventListener('touchstart', function(e) {
            e.preventDefault();
            this.style.transform = 'translateY(2px) scale(0.95)';
        });

        clearPin.addEventListener('touchend', function(e) {
            e.preventDefault();
            this.style.transform = '';
            removeLastNumber();
        });

        // Clear all digits
        clearAll.addEventListener('click', function(e) {
            e.preventDefault();
            clearAllNumbers();
        });

        clearAll.addEventListener('touchstart', function(e) {
            e.preventDefault();
            this.style.transform = 'translateY(2px) scale(0.95)';
        });

        clearAll.addEventListener('touchend', function(e) {
            e.preventDefault();
            this.style.transform = '';
            clearAllNumbers();
        });

        // Add number to PIN
        function addNumber(number) {
            if (currentPin.length < maxPinLength) {
                currentPin += number;
                updatePinDisplay();

                // Add haptic feedback (if supported)
                if ('vibrate' in navigator) {
                    navigator.vibrate(50);
                }

                // Check if PIN is complete
                if (currentPin.length === maxPinLength) {
                    checkPin();
                }
            }
        }

        // Remove last number
        function removeLastNumber() {
            if (currentPin.length > 0) {
                currentPin = currentPin.slice(0, -1);
                updatePinDisplay();
            }
        }

        // Clear all numbers
        function clearAllNumbers() {
            currentPin = '';
            updatePinDisplay();
        }

        // Update PIN display
        function updatePinDisplay() {
            pinDots.forEach((dot, index) => {
                if (index < currentPin.length) {
                    dot.classList.add('filled');
                } else {
                    dot.classList.remove('filled');
                }
            });

            // Update login button state
            if (currentPin.length === maxPinLength) {
                loginBtn.classList.add('active');
            } else {
                loginBtn.classList.remove('active');
            }

            // Update hidden input
            hiddenPassword.value = currentPin;
        }

        // Check PIN
        function checkPin() {
            if (currentPin === correctPin) {
                // PIN correct - show success animation
                showSuccessAnimation();

                // Submit form after animation
                setTimeout(() => {
                    loginForm.submit();
                }, 1500);
            } else {
                // PIN incorrect - shake and clear
                setTimeout(() => {
                    shakeAndClear();
                }, 500);
            }
        }

        // Show success animation
        function showSuccessAnimation() {
            successAnimation.classList.add('show');
            document.body.classList.add('loading');
        }

        // Shake animation and clear PIN
        function shakeAndClear() {
            pinDots.forEach(dot => {
                dot.style.animation = 'shake 0.5s ease-in-out';
            });

            setTimeout(() => {
                clearAllNumbers();
                pinDots.forEach(dot => {
                    dot.style.animation = '';
                });
            }, 500);
        }

        // Handle form submission
        loginForm.addEventListener('submit', function(e) {
            if (currentPin.length !== maxPinLength) {
                e.preventDefault();
                return;
            }

            if (currentPin !== correctPin) {
                e.preventDefault();
                return;
            }
        });

        // Keyboard support
        document.addEventListener('keydown', function(e) {
            if (e.key >= '0' && e.key <= '9') {
                addNumber(e.key);
            } else if (e.key === 'Backspace') {
                removeLastNumber();
            } else if (e.key === 'Escape') {
                clearAllNumbers();
            } else if (e.key === 'Enter' && currentPin.length === maxPinLength) {
                checkPin();
            }
        });

        // Prevent double-tap zoom on iOS
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // Prevent context menu on mobile
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Prevent selection
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
        });

        // Prevent iOS bounce scroll
        document.addEventListener('touchmove', function(e) {
            e.preventDefault();
        }, {
            passive: false
        });

        // Allow scroll only on specific elements if needed
        document.querySelectorAll('.login-container').forEach(element => {
            element.addEventListener('touchmove', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>

</html>
