# TODO List App

Simple Laravel + Vue 3 (Inertia + Vite) todo app with authentication.

## Requirements
- PHP 8.2+
- Composer
- Node.js 18+ (20+ recommended) and npm
- SQLite/MySQL/PostgreSQL

## Quick Start
1. Install dependencies:
   ```bash
   composer install
   npm install
   ```
2. Create env and app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Configure database in `.env`.
   - SQLite (quick):
     ```bash
     touch database/database.sqlite
     # .env
     # DB_CONNECTION=sqlite
     # DB_DATABASE=database/database.sqlite
     ```
4. Migrate database:
   ```bash
   php artisan migrate
   ```
5. Start dev servers (two terminals):
   ```bash
   php artisan serve
   npm run dev
   ```
6. Visit http://127.0.0.1:8000

## Scripts
- Backend: `php artisan serve`
- Frontend: `npm run dev`
- Tests: `php artisan test`
- Build: `npm run build`

# DEMO VIDEO LINK 

https://drive.google.com/file/d/1GHdY897qRuTjT0zWuXd_p2CaMHuZ_M0E/view?usp=sharing
