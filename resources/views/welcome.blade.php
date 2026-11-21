<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BookMyDoc - Healthcare made simple</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-slate-800">
    <header class="border-b border-slate-100 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8" aria-label="Main navigation">
            <a href="/" class="flex items-center gap-2 text-xl font-bold text-slate-900">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-600 text-white">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M12 21s-7-4.35-7-10A4 4 0 0 1 12 7.5 4 4 0 0 1 19 11c0 5.65-7 10-7 10Z"/>
                        <path d="M9 12h6M12 9v6"/>
                    </svg>
                </span>
                BookMyDoc
            </a>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hidden px-3 py-2 text-sm font-semibold text-slate-700 hover:text-blue-600 sm:block">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Register</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.05fr_.95fr] lg:px-8 lg:py-20">
                <div>
                    <div class="mb-5 inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        Trusted healthcare, made simple
                    </div>
                    <h1 class="max-w-2xl text-5xl font-bold leading-[1.08] tracking-tight text-slate-950 sm:text-6xl">
                        Your health,<br>
                        <span class="text-blue-600">our priority.</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                        Find trusted doctors, choose a convenient time, and manage appointments in one easy-to-use place.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-blue-600 px-6 py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                                Open dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-6 py-3.5 text-center text-sm font-semibold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-700">
                                Book an appointment
                            </a>
                            <a href="{{ route('login') }}" class="rounded-lg border border-slate-200 px-6 py-3.5 text-center text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-600">
                                I already have an account
                            </a>
                        @endauth
                    </div>

                    <div class="mt-10 flex flex-wrap gap-x-8 gap-y-4 text-sm text-slate-600">
                        <span class="flex items-center gap-2"><span class="text-blue-600">✓</span> Verified doctors</span>
                        <span class="flex items-center gap-2"><span class="text-blue-600">✓</span> Easy scheduling</span>
                        <span class="flex items-center gap-2"><span class="text-blue-600">✓</span> Secure bookings</span>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-lg">
                    <div class="absolute -inset-8 -z-10 rounded-full bg-blue-100/70 blur-3xl"></div>
                    <div class="rounded-[2rem] bg-slate-900 p-3 shadow-2xl">
                        <div class="rounded-[1.5rem] bg-gradient-to-br from-blue-50 to-white px-6 pb-6 pt-8">
                            <div class="mb-5 flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Next appointment</p>
                                    <p class="mt-1 text-lg font-bold text-slate-900">General Checkup</p>
                                </div>
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Confirmed</span>
                            </div>

                            <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                                        <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <circle cx="12" cy="8" r="4"/>
                                            <path d="M4.5 21a7.5 7.5 0 0 1 15 0"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">Dr. Anjali Rao</p>
                                        <p class="mt-0.5 text-sm text-slate-500">General Physician</p>
                                    </div>
                                </div>
                                <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4 text-sm">
                                    <div>
                                        <p class="text-slate-400">Date</p>
                                        <p class="mt-1 font-semibold text-slate-800">Tomorrow</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-400">Time</p>
                                        <p class="mt-1 font-semibold text-slate-800">10:30 AM</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-3">
                                <div class="rounded-xl bg-white p-3 text-center shadow-sm ring-1 ring-slate-100">
                                    <p class="text-xs text-slate-400">Mon</p><p class="mt-1 font-bold">22</p>
                                </div>
                                <div class="rounded-xl bg-blue-600 p-3 text-center text-white shadow-lg shadow-blue-600/20">
                                    <p class="text-xs text-blue-100">Tue</p><p class="mt-1 font-bold">23</p>
                                </div>
                                <div class="rounded-xl bg-white p-3 text-center shadow-sm ring-1 ring-slate-100">
                                    <p class="text-xs text-slate-400">Wed</p><p class="mt-1 font-bold">24</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-t border-slate-100 bg-slate-50/60">
            <div class="mx-auto grid max-w-7xl gap-8 px-6 py-12 sm:grid-cols-3 lg:px-8">
                <div>
                    <p class="text-3xl font-bold text-slate-900">Find care</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Browse clinics and doctors by specialty.</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-slate-900">Choose a slot</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Pick a date and time that works for you.</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-slate-900">Stay in control</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">View or cancel appointments anytime.</p>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
