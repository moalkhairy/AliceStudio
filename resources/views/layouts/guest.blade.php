<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>@yield('title', 'Alice Studio')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        * {
            font-family: 'Inter', sans-serif
        }

        body {
            background: #0a0a0f;
            color: #fff
        }

        .glass {
            background: rgba(255, 255, 255, .03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .08)
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #ec4899);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
            box-shadow: 0 4px 20px rgba(99, 102, 241, .4), 0 8px 40px rgba(236, 72, 153, .3);
            transition: .3s
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px rgba(99, 102, 241, .5), 0 12px 60px rgba(236, 72, 153, .4)
        }

        .btn-secondary {
            background: rgba(255, 255, 255, .05);
            border: 1px solid rgba(255, 255, 255, .1);
            transition: .3s
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, .1);
            border-color: rgba(99, 102, 241, .3)
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%
            }
            50% {
                background-position: 100% 50%
            }
        }

        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text
        }

        .input-field {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .15);
            color: #E5E7EB; /* gray-200 */
            caret-color: #E5E7EB;
        }

        .input-field::placeholder {
            color: #9CA3AF; /* gray-400 */
            opacity: 1;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, .08);
            border-color: #6366f1; /* indigo-500 */
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .25);
        }

        /* Chrome/Safari autofill fix (removes yellow bg and keeps text visible) */
        .input-field:-webkit-autofill,
        .input-field:-webkit-autofill:hover,
        .input-field:-webkit-autofill:focus {
            -webkit-text-fill-color: #E5E7EB;
            transition: background-color 9999s ease-out, color 9999s ease-out;
            box-shadow: 0 0 0 1000px rgba(255, 255, 255, .06) inset !important;
            border: 1px solid rgba(255, 255, 255, .15) !important;
        }

        /* Disabled / read-only states */
        .input-field:disabled,
        .input-field[readonly] {
            background: rgba(255, 255, 255, .04);
            color: #9CA3AF;
            border-color: rgba(255, 255, 255, .12);
        }
    </style>
    @stack('head')
</head>

<body class="min-h-screen">

@include("client.partials.navbar")
<!-- soft glows -->
<div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 left-1/2 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>
</div>

<main class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
