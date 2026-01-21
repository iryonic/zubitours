<?php
require_once '../admin/includes/connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - Zubi Tours</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css">
    <script src="https://cdn.tailwindcss.com"></script>
  <!--=============== CSS ===============-->
     <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/styles.css" />    
    <style>
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
                <span class="accent-gradient text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block shadow-lg">Legal Agreement</span>
                <h1 class="text-4xl md:text-6xl font-black tracking-tight mb-6">Terms & Conditions</h1>
                <p class="text-slate-500 text-lg">Effective Date: January 21, 2026</p>
            </header>

            <div class="bg-white rounded-[2.5rem] p-8 md:p-12 shadow-premium prose prose-slate max-w-none">
                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4">1. Acceptance of Terms</h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        By visiting our website and/or purchasing services from Zubi Tours, you engage in our "Service" and agree to be bound by the following terms and conditions. These terms apply to all users of the site, including browsers, customers, and contributors.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4">2. Booking Policy</h2>
                    <ul class="list-disc pl-6 text-slate-600 space-y-3 text-lg">
                        <li>All bookings are subject to availability.</li>
                        <li>A package is considered "Confirmed" only after the receipt of the initial deposit as specified in your quotation.</li>
                        <li>Prices are subject to change based on seasonality, government taxes, or fuel price hikes. However, once a package is booked, the price is locked (excluding statutory changes).</li>
                    </ul>
                </section>

                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4">3. Cancellation & Refunds</h2>
                    <p class="text-slate-600 leading-relaxed mb-4 text-lg">
                        Our cancellation policy is designed to be fair while covering the costs already incurred with our ground partners:
                    </p>
                    <div class="bg-slate-50 border border-slate-100 p-6 rounded-2xl">
                        <ul class="space-y-2 text-slate-600 font-medium">
                            <li class="flex justify-between border-b pb-2"><span>30+ Days before travel:</span> <span class="text-primary font-bold">10% of Package Cost</span></li>
                            <li class="flex justify-between border-b py-2"><span>15-29 Days before travel:</span> <span class="text-primary font-bold">25% of Package Cost</span></li>
                            <li class="flex justify-between border-b py-2"><span>7-14 Days before travel:</span> <span class="text-primary font-bold">50% of Package Cost</span></li>
                            <li class="flex justify-between pt-2"><span>Less than 7 Days / No Show:</span> <span class="text-primary font-bold">100% of Package Cost</span></li>
                        </ul>
                    </div>
                </section>

                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4">4. Liability & Insurance</h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        Zubi Tours acts as an intermediary between the travelers and various service providers (Hotels, Transport, Airlines). While we only partner with the best, we are not liable for any personal injury, property damage, or loss incurred during the trip. We strongly recommend all travelers to purchase adequate Travel Insurance.
                    </p>
                </section>

                <section class="mb-10">
                    <h2 class="text-2xl font-black mb-4">5. Force Majeure</h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        Zubi Tours shall not be responsible for any delays, changes in itinerary, or cancellations due to weather conditions, landslides, civil unrest, government restrictions, or other "Acts of God" beyond our control.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black mb-4">6. Governing Law</h2>
                    <p class="text-slate-600 leading-relaxed text-lg">
                        These terms and conditions are governed by and construed in accordance with the laws of Jammu & Kashmir and India.
                    </p>
                </section>
            </div>

            <div class="mt-12 text-center text-slate-500">
                <p>Questions about Terms? Contact <a href="mailto:info@zubitours.com" class="text-primary font-bold">info@zubitours.com</a></p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../admin/includes/footer.php'; ?>
</body>
</html>
