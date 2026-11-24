# Online Medical Admission Management System

A web-based admissions portal for a medical university or healthcare college. Applicants register, build a profile, apply to programs (MBBS, Nursing, Pharmacy, Medical Laboratory Sciences) and track their application. Admissions staff review applications, verify documents, update statuses and run merit-based shortlisting.

Built with plain **PHP 8 (custom MVC)**, **MySQL/MariaDB (PDO)** and **Bootstrap 5**, designed to run on a local **WAMP Server**.

## Features

**Applicant portal**
- Registration, login and profile management (personal details, A/L marks)
- Browse programs with eligibility criteria, intake and seats remaining
- Draft, document upload (PDF/JPG/PNG, max 2 MB) and submission
- Automatic eligibility check and merit score calculation
- Status tracking: Draft, Submitted, Under Review, Shortlisted, Interview, Accepted, Rejected
- Printable acknowledgment slip

**Admissions staff portal (Admin / Officer)**
- Role-based access control
- Dashboard: applicants, submissions, pending reviews, shortlisted, accepted, seats left per program
- Search and filter applications by name, email, reference, status or program
- Review applicant details and verify uploaded documents
- Controlled status workflow with notes and a full audit log
- Merit-based shortlisting that fills the remaining seats
- Admin-only program and quota management

**Security**
- CSRF tokens on all POST requests
- Password hashing with `password_hash`
- Prepared statements (PDO) for all queries
- Session ID regeneration on login
- Uploaded files stored outside the public folder with random names

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8+, custom MVC (front controller, routes, controllers, views) |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap 5 |
| Server | Apache (WAMP), `mod_rewrite` |

## Project structure

```
medadmit/
├── app/
│   ├── Controllers/      Auth, Applicant, Admin controllers
│   ├── views/            Layout and page templates
│   ├── bootstrap.php     Helpers: DB, CSRF, auth, eligibility
│   └── config.php        App and database settings
├── database/schema.sql   Tables, indexes and program seed data
├── public/               Web root (index.php, .htaccess, setup.php)
└── storage/uploads/      Uploaded documents (not public)
```

## Local setup (WAMP)

1. Install [WAMP Server](https://www.wampserver.com/) with PHP 8.0 or higher.
2. Enable the `rewrite_module` in Apache and the `fileinfo` and `pdo_mysql` PHP extensions.
3. Clone or copy this project to `C:\wamp64\www\medadmit`.
4. Open phpMyAdmin, go to **Import**, and import `database/schema.sql`.
5. Check `app/config.php` (default: user `root`, empty password, `base_url` = `/medadmit`).
6. Visit `http://localhost/medadmit/setup.php` once to create demo users, then **delete `public/setup.php`**.
7. Open `http://localhost/medadmit/`.

To use a virtual host instead, point it at the `public/` folder and set `base_url` to `''`.

## Demo accounts

All demo accounts use the password `Password@123` (demo data only, change before any real use).

| Role | Email |
|---|---|
| Admin | admin@medadmit.test |
| Admissions Officer | officer@medadmit.test |
| Applicant | nimali@example.test |

## Suggested test flow

1. Log in as an applicant, open an application, upload ID proof and exam results, and submit.
2. Log in as the officer, review the application and verify the documents.
3. Move it to Under Review, then run the merit shortlist from Manage Programs.
4. Accept the application and check the applicant's status history and admission slip.

## Troubleshooting

- **404 on all pages except home:** enable `rewrite_module` and set `AllowOverride All` for the `www` directory, then restart Apache.
- **"could not find driver":** enable `pdo_mysql`.
- **"Class finfo not found":** enable `fileinfo`.
- **Uploads failing:** check that `storage/uploads` exists, is writable, and that `upload_max_filesize` is at least 2M.
- **Wrong links:** correct `base_url` in `app/config.php`.

## Roadmap

- Email notifications for status changes
- Password reset
- Pagination and CSV export for the applications list
- Migration to Laravel



## License

For educational and portfolio use.

## Author

Chandrakumar Vishanan ( HND ICT ) SLIATE
