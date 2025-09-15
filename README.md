#  Borrow App for SMK Informatika Pesat

This application helps manage the borrowing and returning of various items used within the school.  
It streamlines the process for both administrators and users, allowing easy tracking of item availability and loan status.

#  How to Run the Project

Follow the steps below to set up and run the Borrow App on your local machine.

###  Prerequisites
Make sure you have the following installed:

- PHP (v8.3 or higher)
- Composer
- Node.js and npm
- MySQL or another database supported by Laravel

**Steps to Get Started**

##  Borrow App Setup Script for SMK Informatika Pesat

#  Clone the repository
```bash
git clone https://github.com/FadhlanPutra/inventaris_sekolah.git
```

#  Navigate to the project directory
```bash
cd inventaris_sekolah
```

#  Open the project in VS Code
```bash
code .
```

#  Install PHP dependencies using Composer
```bash
composer install
```

#  Run npm install
```bash
npm install
```

#  Copy .env file
```bash
cp .env.example .env
```

#  Generate a new application key
```bash
php artisan key:generate
```

#  Build assets (JS & CSS)
```bash
npm run build
```

#  Run database migrations and seed data
```bash
php artisan migrate --seed
```

#  Run storage link
```bash
php artisan storage:link
```

<!-- #  Compile frontend assets
```bash
npm run dev
``` -->

#  Start the Laravel development server
```bash
php artisan serve
```

Output success message and login details
Setup complete! You can now access the application at:
http://127.0.0.1:8000
or
http://localhost:8000

Login using the following credentials:

```pgsql
Email: admin@gmail.com
Password: admin123
```

Run Email Verification and notification
```bash
php artisan queue:work
```
















