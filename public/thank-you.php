<?php
require_once '../admin/includes/connection.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-M7PZ56RR');</script>
    <!-- End Google Tag Manager -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Zubi Tours & Holidays</title>
    
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/img/zubilogo.jpg" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#e8862a',
                        'primary-dark': '#d1751f',
                        'accent-yellow': '#f9de73',
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'checkmark': 'checkmark 0.5s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        checkmark: {
                            '0%': { height: '0', width: '0', opacity: '0' },
                            '100%': { height: '24px', width: '12px', opacity: '1' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Remix Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" />

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }
        
        .check-icon {
            width: 80px;
            height: 80px;
            position: relative;
            border-radius: 50%;
            box-sizing: content-box;
            border: 4px solid #4CAF50;
        }
        
        .check-icon::before {
            top: 3px;
            left: -2px;
            width: 30px;
            transform-origin: 100% 50%;
            border-radius: 100px 0 0 100px;
        }
        
        .check-icon::after {
            top: 0;
            left: 30px;
            width: 60px;
            transform-origin: 0 50%;
            border-radius: 0 100px 100px 0;
            animation: rotate-circle 4.25s ease-in;
        }
        
        .check-line {
            display: block;
            position: absolute;
            z-index: 10;
            height: 5px;
            background-color: #4CAF50;
            border-radius: 2px;
            transition: all .2s ease;
        }
        
        .check-line.line-tip {
            top: 46px;
            left: 14px;
            width: 25px;
            transform: rotate(45deg);
            animation: icon-line-tip 0.75s;
        }
        
        .check-line.line-long {
            top: 38px;
            right: 8px;
            width: 47px;
            transform: rotate(-45deg);
            animation: icon-line-long 0.75s;
        }
        
        @keyframes icon-line-tip {
            0% { width: 0; left: 1px; top: 19px; }
            54% { width: 0; left: 1px; top: 19px; }
            70% { width: 50px; left: -8px; top: 37px; }
            84% { width: 17px; left: 21px; top: 48px; }
            100% { width: 25px; left: 14px; top: 46px; }
        }
        
        @keyframes icon-line-long {
            0% { width: 0; right: 46px; top: 54px; }
            65% { width: 0; right: 46px; top: 54px; }
            84% { width: 55px; right: 0px; top: 35px; }
            100% { width: 47px; right: 8px; top: 38px; }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4 overflow-hidden relative">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M7PZ56RR"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <!-- Ambient Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-primary/20 rounded-full blur-[100px] animate-float"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-blue-400/20 rounded-full blur-[100px] animate-float" style="animation-delay: -3s;"></div>
    </div>

    <!-- Main Card -->
    <div class="max-w-md w-full glass-card rounded-3xl p-8 md:p-12 text-center relative z-10 animate-fade-in transform translate-y-4 opacity-0" style="animation-fill-mode: forwards;">
        <!-- Success Animation -->
        <div class="success-checkmark mb-8">
            <div class="check-icon">
                <span class="check-line line-tip"></span>
                <span class="check-line line-long"></span>
            </div>
        </div>

        <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-4 tracking-tight">
            Thank You!
        </h1>
        
        <p class="text-gray-600 text-lg mb-8 leading-relaxed">
            We've received your inquiry and our travel experts will be in touch with you shortly to plan your dream journey.
        </p>

        <div class="space-y-4">
            <a href="<?php echo BASE_URL; ?>" class="block w-full bg-slate-900 text-white font-bold py-4 px-6 rounded-xl hover:bg-primary transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-1">
                Back to Home
            </a>
            
            <a href="https://wa.me/917006296814" class="block w-full bg-[#25D366]/10 text-[#25D366] font-bold py-4 px-6 rounded-xl border-2 border-[#25D366]/20 hover:bg-[#25D366] hover:text-white transition-all duration-300 flex items-center justify-center gap-2">
                <i class="ri-whatsapp-line text-xl"></i> Chat Now
            </a>
        </div>

        <!-- Support Info -->
        <div class="mt-10 pt-8 border-t border-gray-100">
            <p class="text-sm text-gray-400 font-medium">
                Need immediate assistance? <br>
                <a href="tel:+917006296814" class="text-primary hover:text-primary-dark underline decoration-2 underline-offset-2">Call us directly</a>
            </p>
        </div>
    </div>
</body>
</html>
