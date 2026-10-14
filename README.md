<p align="center">
  <a href="https://aureuserp.com">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/aureuserp/temp-media/main/aureus-logo-dark.png">
      <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/aureuserp/temp-media/main/aureus-logo-light.png">
      <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/aureus-logo-light.png" alt="auraERP logo">
    </picture>
  </a>  
</p>

<p align="center">
<a href="https://packagist.org/packages/aureuserp/aureuserp"><img src="https://poser.pugx.org/aureuserp/aureuserp/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/aureuserp/aureuserp"><img src="https://poser.pugx.org/aureuserp/aureuserp/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/aureuserp/aureuserp"><img src="https://poser.pugx.org/aureuserp/aureuserp/license.svg" alt="License"></a>
</p>

<h1 align="center">auraERP</h1>

<p align="center">
  <strong>Open-Source Enterprise Resource Planning for Modern Businesses</strong>
</p>

<p align="center">
  Built with Laravel 13 • Powered by FilamentPHP 5 • PHP 8.3+
</p>

---

## 📋 Table of Contents

1. [Introduction](#-introduction)
2. [Key Features](#-key-features)
3. [Why Choose auraERP?](#-why-choose-auraerp)
4. [Requirements](#-requirements)
5. [Quick Start](#-quick-start)
6. [Plugin System](#-plugin-system)
7. [Plugin Installation & Management](#-plugin-installation--management)
8. [Customization](#-customization)
9. [Contributing](#-contributing)
10. [License](#-license)
11. [Security](#-security)
12. [Support & Community](#-support--community)

---

<p align="center">
  <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/introduction.png" alt="auraERP Introduction" width="100%">
</p>

## 🚀 Introduction

auraERP is a comprehensive, open-source Enterprise Resource Planning (ERP) solution designed for Small and Medium Enterprises (SMEs) and large-scale organizations. Built on **[Laravel 13](https://laravel.com)**, the most popular PHP framework, and **[FilamentPHP 5](https://filamentphp.com)**, a cutting-edge admin panel framework, auraERP offers an extensible and developer-friendly platform for managing every aspect of your business operations.

Whether you're managing accounting, inventory, HR, CRM, or projects, auraERP provides a modular approach that grows with your business.

---

## ✨ Key Features

  ### 🏗️ Modern Architecture
  Built with Laravel 13 and FilamentPHP 5 for maximum performance and developer experience.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/mordern-architecture.png" alt="Modern Architecture" width="100%">
  </p>

  ### 🧩 Modular Plugin System
  Install only the features you need - from accounting to project management.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/modular-plugin-system.png" alt="Modular Plugin System" width="100%">
  </p>

  ### 🎨 Beautiful UI/UX
  Responsive design with TailwindCSS 4, optimized for desktop and mobile.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/mordern-ui-responsive-design.png" alt="Beautiful UI/UX" width="100%">
  </p>

  ### 🔐 Advanced Security
  Role-based access control with Filament Shield integration.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/advanced-security.png" alt="Advanced Security" width="100%">
  </p>

  ### 📊 Business Intelligence
  Built-in analytics and reporting tools.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/business-intelligence.png" alt="Business Intelligence" width="100%">
  </p>

  ### 🌐 Multi-Language Support
  Easily translate and localize for global businesses.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/multi-language-support.png" alt="Multi-Language Support" width="100%">
  </p>

  ### ⚡ High Performance
  Optimized database queries and caching strategies.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/high-performance.png" alt="High Performance" width="100%">
  </p>

  ### 🔧 Developer-Friendly
  Clean code, comprehensive documentation, and extensive APIs.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/developer-friendly.png" alt="Developer-Friendly" width="100%">
  </p>

  ### 🔄 Real-Time Updates
  LiveWire 4 integration for dynamic interfaces.

  <p align="center">
    <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/real-time-updates.png" alt="Real-Time Updates" width="100%">
  </p>

---

## 🎯 Why Choose auraERP?

| Feature | Benefit |
|---------|---------|
| **Open Source** | Free to use, modify, and extend. No vendor lock-in |
| **Modern Stack** | Latest Laravel & FilamentPHP for cutting-edge features |
| **Scalable** | Handles everything from startups to enterprise operations |
| **Customizable** | Extend with your own plugins and modifications |
| **Community-Driven** | Active community support and continuous improvements |
| **Production-Ready** | Battle-tested with real-world business requirements |

---

## 📦 Requirements

Ensure your development environment meets the following requirements:

### Server Requirements
-   **PHP**: 8.3 or 8.4. PHP 8.5 is not supported, because PhpSpreadsheet requires PHP below 8.5. If both are installed, run Artisan with the 8.4 binary (`php8.4 artisan ...`).
-   **Database**: MySQL 8.0+, MariaDB 10.6+, or PostgreSQL 16+
-   **Web Server**: Apache 2.4+ or Nginx 1.18+

### Development Tools
-   **Composer**: Latest version (2.0+)
-   **Node.js**: 18.x or higher
-   **NPM/Yarn**: Latest stable version

### Framework Versions
-   **Laravel**: 13.x
-   **FilamentPHP**: 5.x
-   **Livewire**: 4.x
-   **TailwindCSS**: 4.x

---

## ⚡ Quick Start

Get auraERP up and running in just 4 simple steps:

### Step 1: Clone the Repository

```bash
git clone https://github.com/aureuserp/aureuserp.git
cd aureuserp
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Run the Installation

```bash
php artisan erp:install
```

**What happens during installation:**

✅ Database migrations are executed  
✅ Core seeders populate initial data  
✅ Roles & permissions are generated (via Filament Shield)  
✅ Admin account is created  
✅ Environment configuration is set up

### Step 4: Start the Development Server

```bash
php artisan serve
```

Visit `http://localhost:8000` and log in with your admin credentials!

---

## ☁️ auraERP Cloud Hosting

<p align="center">
  <img src="https://raw.githubusercontent.com/aureuserp/temp-media/main/cloud-hosting.png" alt="auraERP Cloud Hosting" width="100%">
</p>

[auraERP Cloud Hosting](https://aureuserp.com/cloud-hosting/) is a fully managed hosting solution where our team sets up, secures, and configures your auraERP on reliable infrastructure.

Get a ready-to-use ERP on your own domain, without manual installation or infrastructure complexity, and focus on growing your business while we handle the technology.


---

## 🧩 Plugin System

auraERP features a powerful modular plugin system that allows you to customize your ERP installation based on your business needs. Choose only the modules you need to keep your system lean and efficient.

### 📦 Core Plugins (System Plugins)

These plugins are essential components of the system and are installed by default:

| Module     | Description                                       |
| ---------- | ------------------------------------------------- |
| Analytics  | Business intelligence and reporting tools         |
| Chatter    | Internal communication and collaboration platform |
| Fields     | Customizable data structure management            |
| Security   | Role-based access control and authentication      |
| Support    | Help desk and documentation                       |
| Table View | Customizable data presentation framework          |

### ⚡ Installable Plugins

These plugins can be installed as needed to extend system functionality:

#### 💼 Financial Management
| Module     | Description                           |
| ---------- | ------------------------------------- |
| Accounting | Financial accounting and reporting    |
| Accounts   | Core accounting operations            |
| Invoices   | Invoice generation and management     |
| Payments   | Payment processing and tracking       |

#### 📦 Operations
| Module        | Description                                                                     |
| ------------- | ------------------------------------------------------------------------------- |
| Inventories   | Inventory and warehouse management                                              |
| Manufacturing | Bill of Materials (BOM), Manufacturing Orders, Work Orders, Work Centers & Operations |
| Products      | Product catalog and management                                                  |
| Purchases     | Procurement and purchase order management                                       |
| Sales         | Sales pipeline and opportunity management                                       |

#### 👥 Human Resources
| Module       | Description                       |
| ------------ | --------------------------------- |
| Employees    | Employee management               |
| Attendances  | Clock-in and clock-out, with a face check and a GPS point on the phone page |
| Performance  | Performance plans and weighted goals |
| Evaluations  | Manager reviews with weighted 1–5 ratings |
| Recruitments | Applicant tracking and hiring     |
| Timeoffs     | Leave management and tracking     |
| Timesheet    | Employee work hour tracking       |

#### 🤝 Customer & Partner Management
| Module   | Description                                  |
| -------- | -------------------------------------------- |
| Contacts | Contact management for customers and vendors |
| Partners | Partner relationship management              |

#### 📊 Project & Content Management
| Module   | Description                     |
| -------- | ------------------------------- |
| Blogs    | Content management and blogging |
| Projects | Project planning and management |
| Website  | Customer-facing website module  |

---

## 🔧 Plugin Installation & Management

### Installing a Plugin

To install a plugin, use the following Artisan command:

```bash
php artisan <plugin-name>:install
```

**Example:** Install the Inventories plugin

```bash
php artisan inventories:install
```

During installation, the system automatically checks for dependencies. If dependencies are detected, you'll see:

```
This package products is already installed. What would you like to do? [Skip]:
  [0] Reseed
  [1] Skip
  [2] Show Seeders
```

**Options:**
- **Reseed**: Reinstall the plugin's seed data (overwrites existing data)
- **Skip**: Continue without modifying the already installed dependency
- **Show Seeders**: Display available data seeders for the plugin

### Uninstalling a Plugin

To remove a plugin from your system:

```bash
php artisan <plugin-name>:uninstall
```

**Example:** Uninstall the Inventories plugin

```bash
php artisan inventories:uninstall
```

⚠️ **Warning:** Uninstalling a plugin will remove its database tables and data. Make sure to backup your data before uninstalling.

### Plugin Dependencies

Some plugins require other plugins to function properly. The installation system:
- ✅ Automatically detects dependencies
- ✅ Prompts you to install required plugins
- ✅ Prevents conflicts and missing prerequisites
- ✅ Validates the installation order

### Production servers

`erp:install` installs the core only. Business plugins stay uninstalled until you add them.

When `APP_ENV=production`, Laravel asks for confirmation before `migrate` and `db:seed`. The Plugins screen runs `<plugin>:install --no-interaction` and does not pass `--force`, so that confirmation is cancelled. The plugin is still marked installed, its menu can appear, and opening it returns a 500 because the tables were never created.

`--force` only allows the command to run on a production server. It does not drop tables or wipe data.

After each plugin install, create its tables. Use the migration folder, which is not always the same word as the menu label:

```bash
php artisan migrate --force --no-interaction --path=plugins/webkul/<folder>/database/migrations
```

| Menu label | Artisan command | Folder |
| --- | --- | --- |
| Attendances | `attendances:install` | `attendances` |
| Performance | `performance:install` | `performance` |
| Evaluations | `evaluations:install` | `evaluations` |
| Recruitment | `recruitments:install` | `recruitments` |
| Sales | `sales:install` | `sales` |
| Purchases | `purchases:install` | `purchases` |
| Invoices | `invoices:install` | `invoices` |
| Accounts | `accounts:install` | `accounts` |
| Accounting | `accounting:install` | `accounting` |
| Manufacturing | `manufacturing:install` | `manufacturing` |
| Time Off | `time-off:install` | `time-off` |
| Website | `website:install` | `website` |
| Blogs | `blogs:install` | `blogs` |

Install a plugin's dependencies first. Purchases needs Invoices, Invoices and Accounting need Accounts, and Accounts needs Products.

Where a module has sample data, seed it after the tables exist. These seeders replace that module's master rows, so run them on a new database before real records are entered:

```bash
php artisan db:seed --force --no-interaction --class="Webkul\Employee\Database\Seeders\DatabaseSeeder"
php artisan db:seed --force --no-interaction --class="Webkul\Product\Database\Seeders\DatabaseSeeder"
php artisan db:seed --force --no-interaction --class="Webkul\Inventory\Database\Seeders\DatabaseSeeder"
php artisan db:seed --force --no-interaction --class="Webkul\Project\Database\Seeders\DatabaseSeeder"
php artisan db:seed --force --no-interaction --class="Webkul\Maintenance\Database\Seeders\DatabaseSeeder"
php artisan db:seed --force --no-interaction --class="Webkul\Recruitment\Database\Seeders\DatabaseSeeder"
```

Contacts, barcode, timesheets, attendances, performance plans, and evaluations have no sample seeder. Their pages stay empty until someone saves a record.

If a newly installed group is missing from the launcher, generate the missing permissions and attach every permission to the Admin role:

```bash
php artisan shield:generate --all --option=permissions --panel=admin
php artisan tinker --execute="\$role = Spatie\Permission\Models\Role::query()->where('name', 'Admin')->first(); \$role->permissions()->sync(Spatie\Permission\Models\Permission::query()->pluck('id')->all());"
```

That gives the Admin role every `view`, `create`, `update`, and `delete` permission. The launcher shows a group only when that role can open at least one of its pages. Attendances, performance plans, and evaluations appear inside Employees. Timesheets appear inside Project. The Dashboard launcher opens the first installed module dashboard. With only Projects installed, that page is the project summary at `/admin/project`, not a combined home for every module.

Set `APP_URL` to the public site URL, including `https://`. Asset URLs are built from it. `http://localhost` still serves the HTML, but the stylesheet, scripts, and logo fail to load.

The deploy user and the web server user both need to write `storage` and `bootstrap/cache`. Otherwise a 500 can occur with no `storage/logs/laravel.log` to read:

```bash
sudo chown -R "$USER":www-data storage bootstrap/cache
sudo chmod -R ug+rwx storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod g+s {} \;
```

---

## 🎨 Customization

auraERP is designed to be highly customizable, allowing you to tailor the system to your specific business needs:

### Plugin Customization
- 🔹 Install only the plugins you need
- 🔹 Extend existing plugins with custom functionality
- 🔹 Create custom plugins using the modular architecture

### UI/UX Customization
- 🔹 Create custom dashboards and reports
- 🔹 Modify themes and branding
- 🔹 Design custom forms and views with Filament

### Access Control
- 🔹 Define custom user roles and permissions
- 🔹 Configure role-based access control (RBAC)
- 🔹 Set granular permissions using Filament Shield

### Business Logic
- 🔹 Extend models with custom business rules
- 🔹 Create custom workflows and automations
- 🔹 Integrate with third-party services via APIs

---

## 🤝 Contributing

We welcome contributions from the community! Whether you're fixing bugs, adding features, or improving documentation, your help is appreciated.

### How to Contribute

1. **Fork the Repository**
   ```bash
   git clone https://github.com/aureuserp/aureuserp.git
   ```

2. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

3. **Make Your Changes**
   - Follow the existing code style and conventions
   - Write tests for new features
   - Update documentation as needed

4. **Commit Your Changes**
   ```bash
   git commit -m "Add: Brief description of your changes"
   ```

5. **Push to Your Fork**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Submit a Pull Request**
   - Provide a clear description of the changes
   - Reference any related issues
   - Ensure all tests pass

### Development Guidelines
- Follow Laravel and Filament best practices
- Maintain code quality with Laravel Pint: `vendor/bin/pint`
- Write PHPUnit tests for new functionality
- Use meaningful commit messages

---

## 📄 License

auraERP is truly open-source ERP framework that will always be **free** under the [MIT License](LICENSE).

### What This Means
- ✅ Free to use for commercial and personal projects
- ✅ Modify and distribute as you wish
- ✅ No licensing fees or restrictions
- ✅ Community-driven development

---

## 🔒 Security

Security is a top priority for auraERP. We take all security vulnerabilities seriously.

### Reporting Security Vulnerabilities

**⚠️ Please DO NOT disclose security vulnerabilities publicly.**

If you discover a security vulnerability in auraERP, please report it responsibly:

📧 **Email:** support@webkul.com

### What to Include
- Description of the vulnerability
- Steps to reproduce the issue
- Potential impact assessment
- Suggested fix (if available)

We will acknowledge your email within 48 hours and provide a detailed response within 7 days, including our assessment and a timeline for a fix.

---

## 💬 Support & Community

Need help or want to connect with other auraERP users?

### 📚 Documentation
- 📖 **Developer Documentation:** [devdocs.aureuserp.com](https://devdocs.aureuserp.com/) - Technical guides, API references, and development resources
- 📘 **User Guide:** [docs.aureuserp.com](https://docs.aureuserp.com/) - Step-by-step tutorials and user manuals

### 🤝 Get Support
- 💬 **Community Forum:** [Coming Soon] - Connect with other users and share experiences
- 🐛 **Issue Tracker:** [GitHub Issues](https://github.com/aureuserp/aureuserp/issues) - Report bugs and request features
- 📧 **Email Support:** support@webkul.com - Direct support from the team

### 🔔 Stay Updated
- ⭐ **Star** this repository to show your support
- 👁️ **Watch** for new releases and updates
- 🍴 **Fork** to contribute to the project

---

<div align="center">

Made with ❤️ by [Webkul](https://webkul.com)

[⬆ Back to Top](#-table-of-contents)

</div>
