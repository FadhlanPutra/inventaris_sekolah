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
git clone https://github.com/FadhlanPutra/inventaris_sekol.git
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

#  Copy .env file
```bash
cp .env.example .env
```

#  Generate a new application key
```bash
php artisan key:generate
```

#  Run database migrations and seed data
```bash
php artisan migrate --seed
```

#  Create a Super Admin user (ID = 1)
```bash
php artisan shield:generate --all
php artisan shield:super-admin --user=1
```

#  Install Node modules
```bash
npm install
```

#  Compile frontend assets
```bash
npm run dev
```

#  Start the Laravel development server
```bash
php artisan serve
```

Output success message and login details
Setup complete! You can now access the application at:
http://localhost:8000

Login using the following credentials:

```pgsql
Email: admin@gmail.com
Password: admin123
```

Run Email Verification
```bash
php artisan queue:work
```
















