<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BookMyDoc - Doctor Appointments</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900">
        <header class="border-b border-slate-200 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="/" class="flex items-center gap-3" aria-label="BookMyDoc home">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-cyan-600 text-white">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/>
                            <path d="M12 7v10M7 12h10"/>
                        </svg>
                    </span>
                    <span class="text-xl font-bold tracking-tight">BookMyDoc</span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700">
                            My dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-cyan-700">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700">
                            Register
                        </a>
                    @endauth
                </div>
            </nav>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-[1.1fr_.9fr] lg:py-24">
                <div>
                    <p class="mb-5 inline-flex rounded-full bg-cyan-100 px-4 py-1.5 text-sm font-semibold text-cyan-800">
                        Simple, secure appointments
                    </p>
                    <h1 class="max-w-3xl text-5xl font-bold leading-tight tracking-tight text-slate-950 sm:text-6xl">
                        Healthcare appointments, made simple.
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Find a clinic, choose an available doctor, and book your visit in just a few clicks.
                        Patients, doctors, and administrators each get a focused workspace.
                    </p>

                    <div class="mt-9 flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-cyan-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-cyan-200 hover:bg-cyan-700">
                                Open dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="rounded-lg bg-cyan-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-cyan-200 hover:bg-cyan-700">
                                Book an appointment
                            </a>
                            <a href="{{ route('login') }}" class="rounded-lg border border-slate-300 bg-white px-6 py-3.5 font-semibold text-slate-800 hover:border-cyan-600 hover:text-cyan-700">
                                Sign in
                            </a>
                        @endauth
                    </div>

                    <div class="mt-10 flex flex-wrap gap-x-8 gap-y-3 text-sm font-medium text-slate-600">
                        <span class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-cyan-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Conflict-free scheduling
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-cyan-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            Appointment PDFs
                        </span>
                    </div>
                </div>

                <div class="rounded-3xl border border-cyan-100 bg-cyan-50 p-6 shadow-xl shadow-slate-200/60 sm:p-8">
                    <div class="rounded-2xl bg-white p-6 shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-500">Your next visit</p>
                                <h2 class="mt-1 text-xl font-bold text-slate-900">General consultation</h2>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">Available</span>
                        </div>
                        <div class="space-y-4 border-y border-slate-100 py-5">
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-full bg-cyan-100 text-cyan-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10ZM3 21a9 9 0 0 1 18 0"/></svg>
                                </span>
                                <div><p class="text-sm text-slate-500">Doctor</p><p class="font-semibold">Dr. Anita Rao</p></div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-full bg-cyan-100 text-cyan-700">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
                                </span>
                                <div><p class="text-sm text-slate-500">Clinic</p><p class="font-semibold">BookMyDoc Indiranagar</p></div>
                            </div>
                        </div>
                        <a href="{{ route('login') }}" class="mt-6 block rounded-lg bg-slate-900 px-5 py-3 text-center font-semibold text-white hover:bg-slate-800">
                            Find an appointment
                        </a>
                    </div>
                </div>
            </section>

            <section class="border-t border-slate-200 bg-white">
                <div class="mx-auto grid max-w-7xl gap-6 px-6 py-12 md:grid-cols-3">
                    <article class="rounded-2xl border border-slate-200 p-6">
                        <p class="text-sm font-bold text-cyan-700">01 / PATIENTS</p>
                        <h2 class="mt-3 text-xl font-bold">Choose and book</h2>
                        <p class="mt-2 leading-7 text-slate-600">Browse clinics and doctors, select an open time slot, and manage appointments.</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 p-6">
                        <p class="text-sm font-bold text-cyan-700">02 / DOCTORS</p>
                        <h2 class="mt-3 text-xl font-bold">Manage the day</h2>
                        <p class="mt-2 leading-7 text-slate-600">Review daily schedules and update appointment status from one workspace.</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 p-6">
                        <p class="text-sm font-bold text-cyan-700">03 / ADMINS</p>
                        <h2 class="mt-3 text-xl font-bold">Keep care organized</h2>
                        <p class="mt-2 leading-7 text-slate-600">Manage clinics, doctors, slots, and daily booking reports.</p>
                    </article>
                </div>
            </section>
        </main>
    </body>
</html>
