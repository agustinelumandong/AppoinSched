@php
    $offices = App\Models\Offices::all();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>eSantoTomas - Municipality of Santo Tomas</title>
    <meta name="description" content="Book your appointments easily at MTO, MCRO, and BPLS offices in Santo Tomas." />
    <meta name="keywords" content="Santo Tomas, Appointment, MTO, MCRO, BPLS, Municipality" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- FluxUI Styles -->
    <link href="{{ asset('css/fluxUI.css') }}" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .hero-section {
            background: linear-gradient(rgba(37, 99, 235, 0.5), rgba(59, 130, 246, 0.3)),
                url("{{ asset('images/MUNICIPAL_HALL.jpg') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            position: relative;
        }

        .service-card {
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-4px);
        }

        .service-card .service-description {
            opacity: 0;
            max-height: 0;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .service-card:hover .service-description {
            opacity: 1;
            max-height: 100px;
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .service-card:hover .icon-wrapper {
            transform: scale(1.1);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-gray-800 text-white py-4">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/LGU_logo.png') }}" alt="Logo" class="h-20 w-20 object-contain">
                <div class="text-white">
                    <div class="text-sm">Republic of the Philippines</div>
                    <hr class="border-white opacity-75 my-1 max-w-xs" />
                    <div class="text-xl font-bold">Santo Tomas, Davao del Norte</div>
                    <div class="text-sm"><strong>Katawhan ang UNA</strong></div>
                </div>
            </div>

            <nav class="flex gap-4">
                @guest
                    <a href="{{ route('login') }}"
                        class="flux-btn flux-btn-outline text-white border-white hover:bg-white hover:text-gray-800">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="flux-btn flux-btn-primary">
                        Register
                    </a>
                @endguest
                @auth
                    <a href="{{ route('dashboard') }}" class="flux-btn flux-btn-primary">
                        Dashboard
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="hero-section py-20">
        <div class="max-w-7xl mx-auto px-4 ">
            <div class="flex flex-col items-center justify-between gap-12">
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- MCRO -->
                    <a href="{{ route('dashboard') }}" class="service-card flux-card p-8 text-center transition hover:shadow-xl hover:-translate-y-2 block">
                        <div class="icon-wrapper">
                            {{-- <i class="bi bi-person-vcard text-2xl"></i> --}}
                            <img src="{{ asset('images/MCR_logo.jpg') }}" alt="MCRO" class="w-20 h-20 object-contain mx-auto">
                        </div>
                        <h5 class="text-xl font-semibold mb-3 text-gray-900">Municipal Civil Registrar</h5>
                        <p class="service-description text-gray-600 mt-2">
                            Handles birth certificates, marriage licenses, and other civil registry documents.
                        </p>
                    </a>

                    <!-- BPLS -->
                    <a href="{{ route('dashboard') }}" class="service-card flux-card p-8 text-center transition hover:shadow-xl hover:-translate-y-2 block">
                        <div class="icon-wrapper">
                            {{-- <i class="bi bi-briefcase-fill text-2xl"></i> --}}
                            <img src="{{ asset('images/BPLS_logo.jpg') }}" alt="BPLS" class="w-20 h-20 object-contain mx-auto">
                        </div>
                        <h5 class="text-xl font-semibold mb-3 text-gray-900">Business Permit and Licensing Section</h5>
                        <p class="service-description text-gray-600 mt-2">
                            Assists with securing business permits, renewals, and compliance certificates.
                        </p>
                    </a>

                    <!-- Treasurer -->
                    <a href="{{ route('dashboard') }}" class="service-card flux-card p-8 text-center transition hover:shadow-xl hover:-translate-y-2 block">
                        <div class="icon-wrapper">
                            {{-- <i class="bi bi-cash-stack text-2xl"></i> --}}
                            <img src="{{ asset('images/MTO_logo.jpg') }}" alt="MTO" class="w-20 h-20 object-contain mx-auto">
                        </div>
                        <h5 class="text-xl font-semibold mb-3 text-gray-900">Municipal Treasurer's Office</h5>
                        <p class="service-description text-gray-600 mt-2">
                            Responsible for tax payments, assessments, and other municipal financial services.
                        </p>
                    </a>
                </div>
                <div class="text-center lg:w-full w-full flex flex-col items-center justify-center">

                    <h1 class="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                        Book Your Municipal <br>Appointment Online
                    </h1>
                    <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto lg:mx-0 text-center">
                        Access government services faster and easier with our new digital scheduling system.
                        Set up your appointment today.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        @guest
                            <a href="{{ route('login') }}"
                                class="flux-btn flux-btn-secondary px-8 py-3 text-lg bg-white text-blue-600 hover:bg-gray-100">
                                <i class="bi bi-arrow-right-circle me-2"></i> Get Started
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                                class="flux-btn flux-btn-secondary px-8 py-3 text-lg bg-white text-blue-600 hover:bg-gray-100">
                                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- How It Works Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900">How It Works</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="flux-card p-8 text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-calendar2-check-fill text-blue-600 text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-semibold mb-3 text-gray-900">Set Appointment</h5>
                    <p class="text-gray-600">Choose your preferred office and date. Quick and easy process.</p>
                </div>
                <div class="flux-card p-8 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-envelope-paper-fill text-green-600 text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-semibold mb-3 text-gray-900">Get Confirmation</h5>
                    <p class="text-gray-600">Receive an SMS or email confirming your appointment details.</p>
                </div>
                <div class="flux-card p-8 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-person-check-fill text-red-600 text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-semibold mb-3 text-gray-900">Visit the Office</h5>
                    <p class="text-gray-600">Show your confirmation and proceed with your transaction hassle-free.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Municipal Offices Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900">Our Municipal Offices</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <!-- MCRO -->
                <div class="service-card flux-card p-8 text-center">
                    <div class="icon-wrapper">
                        <i class="bi bi-person-vcard text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-semibold mb-3 text-gray-900">Municipal Civil Registrar</h5>
                    <p class="service-description text-gray-600 mt-2">
                        Handles birth certificates, marriage licenses, and other civil registry documents.
                    </p>
                </div>

                <!-- BPLS -->
                <div class="service-card flux-card p-8 text-center">
                    <div class="icon-wrapper">
                        <i class="bi bi-briefcase-fill text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-semibold mb-3 text-gray-900">Business Permit and Licensing Section</h5>
                    <p class="service-description text-gray-600 mt-2">
                        Assists with securing business permits, renewals, and compliance certificates.
                    </p>
                </div>

                <!-- Treasurer -->
                <div class="service-card flux-card p-8 text-center">
                    <div class="icon-wrapper">
                        <i class="bi bi-cash-stack text-2xl"></i>
                    </div>
                    <h5 class="text-xl font-semibold mb-3 text-gray-900">Municipal Treasurer's Office</h5>
                    <p class="service-description text-gray-600 mt-2">
                        Responsible for tax payments, assessments, and other municipal financial services.
                    </p>
                </div>
            </div>
        </div>
    </section>


    <!-- Call to Action Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <div class="flux-card p-12 hero-section text-white">
                <h2 class="text-3xl font-bold mb-4">Ready to Get Started?</h2>
                <p class="text-xl mb-8 opacity-90">
                    Join thousands of Santo Tomas residents making their government visits easier.
                </p>
                @guest
                    <a href="{{ route('login') }}"
                        class="flux-btn flux-btn-secondary px-8 py-3 text-lg bg-white text-blue-600 hover:bg-gray-100">
                        <i class="bi bi-arrow-right-circle me-2"></i> Get Started Now
                    </a>
                @else
                    <a href="{{ route('dashboard') }}"
                        class="flux-btn flux-btn-secondary px-8 py-3 text-lg bg-white text-blue-600 hover:bg-gray-100">
                        <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                    </a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Info Section -->
    <section class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-600">More services coming soon. Stay tuned!</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h5 class="text-lg font-semibold mb-4">Municipality of Santo Tomas</h5>
                    <p class="text-gray-300 text-sm">
                        Empowering citizens with accessible government services through a modern appointment system.
                    </p>
                </div>
                <div>
                    <h6 class="font-semibold mb-4">Quick Links</h6>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('login') }}"
                                class="text-gray-300 hover:text-white transition-colors">Login</a></li>
                        <li><a href="{{ route('register') }}"
                                class="text-gray-300 hover:text-white transition-colors">Register</a></li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">About Us</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Help Center</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h6 class="font-semibold mb-4">Contact Us</h6>
                    <div class="text-gray-300 text-sm space-y-2">
                        <p>Santo Tomas Municipal Hall<br />Santo Tomas, Davao del Norte</p>
                        <p>
                            <i class="bi bi-envelope me-2"></i> support@stotomasmunicipality.gov.ph
                        </p>
                        <p>
                            <i class="bi bi-phone me-2"></i> 09913724619
                        </p>
                    </div>
                </div>
            </div>
            <hr class="border-gray-600 my-8" />
            <div class="text-center text-sm text-gray-300">
                &copy; {{ date('Y') }} Municipality of Santo Tomas. All rights reserved.
            </div>
        </div>
    </footer>
</body>

</html>