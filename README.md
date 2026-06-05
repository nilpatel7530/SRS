# Solar Reporting System (SRS)

A modern, modular web application designed for managing and tracking solar energy projects. It streamlines project lifecycles, financial document management (Capex/Opex), invoicing, bank guarantees, and provides extensive year-wise and reconciliation reports.

---

## 🏗️ Architecture: Custom Modular Structure

Unlike traditional monolithic Laravel setups, **SRS** is built using a custom **Modular Architecture** located in `app/Modules`. Each module is encapsulated with its own models, migrations, views, and controllers, allowing for independent scaling and high maintainability.

```
app/Modules/
├── Admin/        # Branding configs, logo management, custom themes
├── Documents/    # Core document storage and project associations
├── Finance/      # Capex/Opex ledgers, Invoice pipelines, Bank Guarantees
├── Projects/     # Project lifecycle state, department owners, proposals
├── Reports/      # Reconciliation reports, CSV exports, year-wise analytics
├── Roles/        # RBAC (Role-Based Access Control)
└── Users/        # Profile and user accounts
```

Modules are automatically registered via the `App\Providers\ModuleServiceProvider`, which dynamically loads migrations from each module's `Migrations/` directory and exposes the relevant route definitions.

---

## ⚡ Key Capabilities & Features

1. **Project Management:** Track solar projects from proposal through implementation, assigning departmental ownership.
2. **Financial Ledger:** Track Capex (Capital Expenditure) and Opex (Operational Expenditure) transactions against specific solar projects.
3. **Billing & Guarantees:** Manage client-facing invoices and bank guarantee deposits (performance or financial security records).
4. **Document Management:** Upload and link relevant legal, architectural, and financial PDFs to solar projects.
5. **Interactive Frontend:** Built with **Livewire 3** and **Volt** (single-file Livewire components) to provide dynamic, reactive UI elements without loading bulky single-page application (SPA) frameworks.
6. **Robust Admin Dashboard:** Integrated with the **AdminLTE v3** dashboard layout for a premium back-office experience.
7. **Fine-Grained RBAC:** Implemented using Spatie Laravel Permissions with slug-based route middleware protections (e.g. `admin`, `projects.view`).

---

## 🛠️ Technology Stack

- **Backend Framework:** Laravel 13 (Latest)
- **Language:** PHP 8.3
- **Frontend Interactivity:** [Livewire 3](https://livewire.laravel.com/) & [Volt](https://livewire.laravel.com/docs/volt)
- **UI Framework:** [AdminLTE 3](https://adminlte.io/) (Bootstrap-based dashboard)
- **Styling:** Tailwind CSS (via Vite compile)
- **Database:** MySQL
- **Code Styling:** [Laravel Pint](https://laravel.com/docs/pint)

---

## ⚙️ Core Database Entities

- **Project:** The central model. Belongs to a Department and has many Finance/Document records.
- **Finance Entities:**
  - `CapexEntry` / `OpexEntry`: Expenditures tracked against projects.
  - `Invoice`: Financial billing sheets linked to projects.
  - `BankGuarantee`: Performance and financial security certificates.
- **Role/Permission:** Spatie Models mapped to roles like `admin`, `finance.view`, `projects.edit`.

---

## 💻 Getting Started & Installation

### Prerequisites
- PHP 8.3+
- Composer
- Node.js & NPM
- MySQL Database

### Installation Steps

1. Install dependencies and run the automated setup script (which installs Composer packages, runs migrations, copies `.env`, generates app keys, and runs npm installs):
   ```bash
   npm run setup
   ```

2. Configure your MySQL credentials in the generated `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=srs_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. Build and run the development environment:
   ```bash
   # Run Vite, local server, queue listener, and Pail logs concurrently
   npm run dev
   ```

4. Access the application in your browser at `http://localhost:8000`.

---

## 🧹 Code Quality & Standards

Pint is configured for code style enforcement. To automatically format your PHP code to conform to project standards, run:
```bash
./vendor/bin/pint
```


---
*Made with Antigravity*
