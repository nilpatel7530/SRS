# Project Knowledge Transfer: SRS (Solar Reporting System)

Welcome to the SRS project. This document provides a comprehensive overview of the project's architecture, tech stack, and development workflows to help you get up to speed quickly.

---

## 1. Project Overview
**SRS** is a modular Laravel-based web application designed for managing solar energy projects. It handles project tracking, financial documentation (Capex/Opex), invoicing, bank guarantees, and provides extensive reporting capabilities.

### Key Capabilities:
- **Project Management:** Track lifecycle and departmental ownership.
- **Financial Tracking:** Manage Capex/Opex entries, Invoices, and Bank Guarantees.
- **Document Management:** Centralized storage and access for project-related documents.
- **Reporting:** Year-wise analytics and reconciliation reports.
- **RBAC:** Fine-grained access control using roles and permissions.

---

## 2. Tech Stack
- **Framework:** Laravel 13 (Latest)
- **Language:** PHP 8.3
- **Frontend Interactivity:** [Livewire 3](https://livewire.laravel.com/) & [Volt](https://livewire.laravel.com/docs/volt)
- **UI Framework:** [AdminLTE 3](https://adminlte.io/)
- **Styling:** Tailwind CSS
- **Build Tool:** Vite
- **Database:** MySQL
- **Permission Management:** [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)

---

## 3. Architecture: Modular Structure
Unlike a standard Laravel app, this project uses a custom **Modular Architecture** located in `app/Modules`. Each module encapsulates its own Controllers, Models, and Migrations.

### Current Modules:
- **`Admin`:** Dashboard logic and branding (logos, theme settings).
- **`Documents`:** Document storage and retrieval logic.
- **`Finance`:** Core financial logic (Invoices, Capex, Opex, Bank Guarantees).
- **`Projects`:** Project lifecycle, Departments, and Proposals.
- **`Reports`:** Data aggregation for year-wise and reconciliation views.
- **`Roles`:** Role and Permission (RBAC) management.
- **`Users`:** User profile and account management.

### Module Registration:
Modules are registered via the `App\Providers\ModuleServiceProvider`, which automatically loads migrations from each module's `Migrations/` directory.

---

## 4. Getting Started

### Prerequisites:
- PHP 8.3+
- Composer
- Node.js & NPM
- MySQL

### Installation Steps:
The project includes a convenient setup script defined in `composer.json`:

```bash
# Clone the repository
git clone <repository-url>
cd SRS_project

# Run the automated setup
npm run setup
```

**Note:** `npm run setup` will install composer/npm dependencies, generate the app key, run migrations, and build assets.

### Development Workflow:
To start the development environment (Server, Vite, Queue, and Pail logs), run:
```bash
npm run dev
```

---

## 5. Core Entities & Database
- **Project:** The central entity. Belongs to a Department and has many Finance/Document records.
- **Finance Entities:**
    - `CapexEntry` / `OpexEntry`: Capital and Operational expenditures.
    - `Invoice`: Billing records linked to projects.
    - `BankGuarantee`: Performance or financial security records.
- **Role/Permission:** Uses `slug` based identification (e.g., `admin`, `projects.view`).

---

## 6. Key Features & Workflows

### Authentication & Authorization
- **Breeze:** Standard Laravel Breeze for authentication.
- **RBAC:** Permissions are enforced via Laravel's native `can` middleware in `routes/web.php` and `@can` directives in Blade/Livewire components.

### Branding & Customization
Admin users can update the application's logo and branding through the **Branding Settings** (`/branding`). This is handled by `Modules\Admin\Controllers\BrandingController`.

### Reporting
The **Reports** module (`/reports`) aggregates data from Projects and Finance modules. It includes CSV export functionality for reconciliation.

---

## 7. Coding Standards
- **Pint:** The project uses [Laravel Pint](https://laravel.com/docs/pint) for code style enforcement. Run `./vendor/bin/pint` to fix styling issues.
- **Modularity:** When adding new features, try to keep logic within the relevant module. Use cross-module service calls if needed.
- **Interactivity:** Use Livewire/Volt for dynamic UI elements to maintain a reactive experience without writing heavy custom JavaScript.

---

## 8. Deployment Notes
- Ensure `APP_ENV` is set to `production`.
- Run `npm run build` to generate optimized assets.
- Ensure the `storage/app/public` link is created (`php artisan storage:link`).

---
*For any specific queries, refer to the code in `app/Modules` or check the existing route definitions in `routes/web.php`.*
