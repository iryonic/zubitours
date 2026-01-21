<?php
require_once '../admin/includes/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Zubi Tours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <script src="https://cdn.tailwindcss.com"></script>
   <!--=============== CSS ===============-->
     <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css" />    
    <style>
        .premium-blur {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.8);
        }
        .accent-gradient {
            background: linear-gradient(135deg, #e8862a 0%, #ffb347 100%);
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    <!-- Navbar -->
    <?php include '../admin/includes/navbar.php'; ?>

    <main class="pt-32 pb-20">
        <div class="max-w-4xl mx-auto px-6">
            <header class="text-center mb-16">
                <span class="accent-gradient text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block shadow-lg">Legal</span>
                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-6">Our Policy</h1>
                <p class="text-slate-500 text-lg">Last updated: January 21, 2026</p>
            </header>

            <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-premium prose prose-slate max-w-none">
                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 accent-gradient rounded-lg flex items-center justify-center text-white text-sm">01</span>
                        Information We Collect
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        At Zubi Tours, we collect personal information mainly to provide you with seamless travel experiences. This includes:
                    </p>
                    <ul class="list-disc pl-6 text-slate-600 mt-4 space-y-2">
                        <li>Name, email address, and phone number when you make an inquiry or booking.</li>
                        <li>Payment information (processed securely through our partners).</li>
                        <li>Travel preferences, interests, and special requirements (e.g., dietary or medical needs).</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 accent-gradient rounded-lg flex items-center justify-center text-white text-sm">02</span>
                        How We Use Your Data
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        Your data helps us customize your Kashmiri and Ladakhi adventures. We use it to:
                    </p>
                    <ul class="list-disc pl-6 text-slate-600 mt-4 space-y-2">
                        <li>Confirm your bookings and manage your itinerary.</li>
                        <li>Send important travel documents and updates.</li>
                        <li>Improve our website and customer service.</li>
                        <li>Send promotional offers (only if you've subscribed).</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 accent-gradient rounded-lg flex items-center justify-center text-white text-sm">03</span>
                        Data Security
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        We implement industry-standard security measures to protect your personal information from unauthorized access, alteration, or disclosure. Your sensitive payment data is encrypted via SSL technology.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 accent-gradient rounded-lg flex items-center justify-center text-white text-sm">04</span>
                        Booking & Cancellation
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        Please refer to our Terms & Conditions for detailed booking and cancellation policies. Generally, we require a partial advance payment to secure your slots, hotels, and permits.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 accent-gradient rounded-lg flex items-center justify-center text-white text-sm">05</span>
                        Contact Us
                    </h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        If you have any questions regarding this Privacy Policy, feel free to contact us at <a href="mailto:info@zubitours.com" class="text-primary font-bold decoration-2 underline-offset-4">info@zubitours.com</a>.
                    </p>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../admin/includes/footer.php'; ?>
</body>
</html>
