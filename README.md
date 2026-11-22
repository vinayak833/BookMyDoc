# BookMyDoc: complete project (Checkpoints 2 + 3 + 4 combined)

One app: multi-role auth, clinics/doctors/slots management, conflict-free booking, doctor schedule,
appointment PDF, email confirmation (Mailtrap), admin daily bookings report, API, 70 tests, GitHub Actions CI.

## Install
Needs PHP 8.2+, Composer, Node 20+, Git, MySQL (or XAMPP).

    ./install.sh

Then:
1. Create an empty MySQL database named `bookmydoc`.
2. In `bookmydoc/.env` set: DB_CONNECTION=mysql, DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=bookmydoc, DB_USERNAME=root, DB_PASSWORD=(yours; blank on XAMPP)
3. `php artisan migrate:fresh --seed`
4. `php artisan test`
5. `php artisan serve` -> http://127.0.0.1:8000/login

Already have the project from Checkpoint 2? Skip install.sh and just run, inside it:

    composer require barryvdh/laravel-dompdf
    cp -R /path/to/overlay/. .
    php artisan migrate && php artisan test

## Demo logins (password: password)
admin@bookmydoc.test, dr.rao@bookmydoc.test (Indiranagar), dr.nair@bookmydoc.test (Koramangala), patient@bookmydoc.test
First, as admin: Slots -> Generate slots for a doctor, so patients have something to book.

## Email (Mailtrap)
Put in .env (not .env.example): MAIL_MAILER=smtp, MAIL_HOST=sandbox.smtp.mailtrap.io, MAIL_PORT=2525,
MAIL_USERNAME, MAIL_PASSWORD (from your Mailtrap inbox), MAIL_FROM_ADDRESS="bookings@bookmydoc.test", MAIL_FROM_NAME="BookMyDoc".
Without these, mail goes to the log file and everything still works.

## GitHub
    git init && git add . && git commit -m "BookMyDoc"
    git branch -M main
    git remote add origin https://github.com/<you>/bookmydoc.git
    git push -u origin main
Commit composer.lock too, so CI can install DomPDF.

## Submitting checkpoints separately from this one project
Checkpoint 3 submission should not show Checkpoint 4 features. To stage it, delete these before your first commit,
commit + `git tag checkpoint-3`, then restore them and commit + `git tag checkpoint-4`:
  app/Services/{AppointmentPdf,AppointmentNotifier,ReportService}.php, app/Mail/, app/Http/Controllers/AppointmentPdfController.php,
  app/Http/Controllers/Admin/ReportController.php, app/Http/Controllers/Api/V1/ReportController.php, resources/views/{pdf,emails,admin/reports}/,
  tests/Feature/{AppointmentPdfTest,AppointmentMailTest,DailyReportTest,EndToEndJourneyTest}.php
(or simply keep two zips and apply Checkpoint 3 first; this combined zip is for running the finished app).

## Features
- Roles: patient, doctor, admin (Breeze + role middleware)
- Admin: clinics, doctors (with logins), slot generation, slot list, daily bookings report + CSV
- Patient: clinic -> doctor -> slot -> book, my appointments, cancel, PDF
- Doctor: daily schedule, mark Completed / No-Show
- Booking safety: transaction + row lock + unique constraints
- API v1: login/logout/me, clinics, doctors, slots, book, appointment PDF, admin report
