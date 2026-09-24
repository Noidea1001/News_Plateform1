# NewsPlatform CMS

![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.2-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![Architecture](https://img.shields.io/badge/Architecture-Decoupled_CMA%2FCDA-007ACC?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

A production-grade, decoupled **Content Management System (CMS)** and **Content Delivery Application (CDA)** engineered for independent journalism, digital newsrooms, and editorial publishing platforms. Built with pure PHP 8.2+, MySQL, modular security layers, custom multi-template layout blueprints, floating toast notifications, and an instant bilingual translation engine.

---

> 🎓 **Student & Presentation Summary (Quick Guide for Class):**
> 
> This web application is a full-featured **News Publishing Platform** (similar to BBC News, CNA, or Fresh News). It is divided into two decoupled operational environments:
> 
> 1. **🌐 Public Reader Website (CDA - Content Delivery Application)**: Located under `/public`. Allows readers to browse breaking news, switch between Khmer & English languages instantly without page reloads, bookmark articles into an offline reading list, preview summaries via quick popups, track reading progress, and read full long-form articles across 3 specialized layout blueprints.
> 2. **🔐 Backend Admin Panel (CMA - Content Management Application)**: Located under `/public/admin`. A secure management dashboard for administrators, editors, and reporters to draft/publish articles, manage categories, upload media with MS Word-style text wrapping, track user permissions, and handle email subscriptions.

---

## 💡 Quick Reference Card for Beginners

| Topic | Details & Instructions |
| :--- | :--- |
| 🌐 **Public Reader Website** | `http://localhost/News-platform-1/public/` |
| 🔐 **Admin Login Portal** | `http://localhost/News-platform-1/public/admin/login.php` |
| 🔑 **Default Admin Account** | **Username:** `admin` \| **Password:** `admin123` *(Full Control)* |
| 🔑 **Default Editor Account** | **Username:** `eleanor_vane` \| **Password:** `admin123` *(Editing & Publishing)* |
| 🔑 **Default Reporter Account**| **Username:** `reporter` \| **Password:** `admin123` *(Drafting & Media Upload)* |
| 🗄️ **Database Auto-Setup** | **Automatic!** Database schema, tables, and sample news articles auto-initialize on first page load. |

---

## 📋 Table of Contents

- [💡 Quick Reference Card for Beginners](#-quick-reference-card-for-beginners)
- [✨ Key System Features](#-key-system-features)
- [💻 Prerequisites & Technical Stack](#-prerequisites--technical-stack)
- [🚀 Quick Installation & Setup Guide](#-quick-installation--setup-guide)
- [🔑 Role-Based Access Control (RBAC) & Credentials](#-role-based-access-control-rbac--credentials)
- [⚙️ Deep-Dive Architecture & Core Components](#️-deep-dive-architecture--core-components)
  - [1. Decoupled Webroot Isolation (CMA vs CDA)](#1-decoupled-webroot-isolation-cma-vs-cda)
  - [2. Bridge Script Delegation Pattern](#2-bridge-script-delegation-pattern)
  - [3. Core Classes & Engine Breakdown](#3-core-classes--engine-breakdown)
  - [4. Centralized Bilingual Engine (Khmer & English)](#4-centralized-bilingual-engine-khmer--english)
  - [5. Floating Toast Notification Engine](#5-floating-toast-notification-engine)
  - [6. Multi-Template Layout Blueprints](#6-multi-template-layout-blueprints)
  - [7. MS Word-Style Media Shortcodes & Text Wrapping](#7-ms-word-style-media-shortcodes--text-wrapping)
  - [8. Interactive Saved Reading List & Offcanvas Drawer](#8-interactive-saved-reading-list--offcanvas-drawer)
  - [9. Quick Article Preview Modal](#9-quick-article-preview-modal)
- [📁 Comprehensive Directory Structure](#-comprehensive-directory-structure)
- [🗄️ Database Relational Schema](#️-database-relational-schema)
- [❓ Troubleshooting & FAQ](#-troubleshooting--faq)

---

## ✨ Key System Features

- 📰 **BBC / CNA Flat Editorial Aesthetic**: Clean, high-contrast light mode with signature crimson red accents (`#c8102e`), dark navy headings (`#0f172a`), flat sharp borders (`#e5e7eb`), and zero box-shadow elevation for maximum readability.
- 🔔 **Context-Aware Floating Toast Notifications**: Custom `window.showAdminToast()` system with floating auto-dismiss status cards positioned neatly below the navigation bar (`top: 96px; right: 1.5rem;`). Displays real-time feedback for published articles, saved drafts, field validation warnings, and errors.
- 👥 **3-Tier Role-Based Access Control (RBAC)**: Granular permission levels enforcing strict authorization across `admin`, `editor`, and `reporter` roles.
- 📝 **Article Publishing & Draft Workflow**: Support for saving articles as drafts or publishing them immediately, complete with status tags and filter tabs in the admin panel.
- 🌐 **Single-Source Bilingual System**: Centralized translation dictionary mapping Khmer and English keys. Allows instant client-side language switching without page refreshes or state loss.
- 🔖 **Saved Reading List Engine**: Client-side article bookmarking stored in `localStorage`, complete with live header counter badge (`#savedCountBadge`) and an offcanvas drawer with one-click article removal.
- 👁️ **Quick Article Preview Modal**: AJAX-powered modal pop-up enabling readers to view summaries, author metadata, publication dates, view counts, and reading times directly from the feed.
- 📐 **3 Custom Article Layout Blueprints**:
  - `Standard`: Classic 2-column editorial layout with sticky sidebar and author spotlight card.
  - `Investigative`: Single-column deep-read view with dark hero banner and verified primary citation callout boxes.
  - `Opinion`: Columnist avatar header spotlight, styled red-bordered pull quotes, and reader commentary box.
- 🖼️ **MS Word-Style Media Shortcodes**: Floating media alignment tags (`[image:1:left]`, `[image:1:right]`, `[video:1:full]`) allowing text to wrap naturally around images and videos.
- 📊 **Reading Progress Bar**: Dynamic top scroll progress bar (`#readingProgressBar`) tracking long-form reading depth in real time.
- ⚡ **Auto Database Schema Initialization & Seeder**: Self-healing database handler that automatically creates database tables, seeds default categories, users, and 12 sample news stories upon initial load.

---

## 💻 Prerequisites & Technical Stack

### Server Environment Requirements

| Component | Minimum Requirement | Recommended | Notes |
| :--- | :--- | :--- | :--- |
| **Web Server** | Apache 2.4+ | XAMPP / WAMP / Laragon | `mod_rewrite` enabled for clean routing |
| **PHP Engine** | PHP 8.0+ | PHP 8.2 or 8.3 | Required extensions: `pdo`, `pdo_mysql`, `gd`, `session`, `json` |
| **Database** | MySQL 5.7+ | MySQL 8.0+ / MariaDB 10.3+ | UTF-8 (`utf8mb4_unicode_ci`) encoding |
| **Frontend Framework** | Bootstrap 5.3.2 | Loaded via CDN | Paired with Bootstrap Icons 1.11.0 |

### Core Architecture Technologies
- **Backend**: Native Object-Oriented & Procedural PHP 8.2 (No heavy external framework bloat).
- **Security**: Custom `Auth` singleton with `PASSWORD_BCRYPT` hashing, CSRF token protection, and `login_attempts` brute-force IP rate-limiting.
- **Sanitization**: Custom `Sanitizer` class featuring dual-layer XSS HTML stripping and shortcode parsing.
- **Frontend Interactivity**: Native JavaScript (ES6+) with Async/Fetch API and `localStorage`.

---

## 🚀 Quick Installation & Setup Guide

### Step 1: Copy Project to Apache `htdocs`
Copy or extract the project folder to your local Apache web server root directory:
- **XAMPP**: `C:\xampp\htdocs\News-platform-1\`
- **WAMP**: `C:\wamp64\www\News-platform-1\`
- **Laragon**: `C:\laragon\www\News-platform-1\`

### Step 2: Configure Database Credentials
Open [config/database.php](file:///c:/xampp/htdocs/News-platform-1/config/database.php) and adjust your local MySQL credentials if needed:

```php
return [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'news_platform',
    'username' => 'root',
    'password' => '', // Default empty password for XAMPP
    'charset'  => 'utf8mb4',
];
```

### Step 3: Automatic Database Schema Creation & Data Seeding
**No manual database imports are required!** Simply open the application in your web browser:
1. Navigating to `http://localhost/News-platform-1/public/` triggers `Database::autoInitializeSchema()`.
2. It automatically creates the `news_platform` database if it does not exist.
3. It creates all 5 relational tables (`users`, `categories`, `articles`, `subscribers`, `login_attempts`).
4. It seeds initial staff accounts (`admin`, `eleanor_vane`, `reporter`) and sample categories.

*(Optional: Visit `http://localhost/News-platform-1/seed_news.php` in your browser anytime to re-seed 12 fresh sample news stories).*

---

## 🔑 Role-Based Access Control (RBAC) & Credentials

The system provides 3 tier levels of staff access:

| Role | Default Username | Default Password | Access Level & Permissions |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | **Full Administrative Access**: Manage all articles, drafts, categories, staff user accounts, reader subscriptions, and system settings. |
| **Senior Editor** | `eleanor_vane` | `admin123` | **Editorial Control**: Create, edit, publish, or delete articles across all layout blueprints, manage categories, and review reader feedback. |
| **Reporter** | `reporter` | `admin123` | **Journalist Access**: Draft new news stories, edit submitted drafts, and upload image/video media. Cannot alter global system users or delete published articles. |

---

## ⚙️ Deep-Dive Architecture & Core Components

### 1. Decoupled Webroot Isolation (CMA vs CDA)
The project isolates administrative content management (CMA) from public content delivery (CDA):
- **CMA (Content Management Application)**: Located in `/admin/` and driven by `src/Controllers/AdminController.php`. Provides secure editorial management dashboards, user management, and article authoring.
- **CDA (Content Delivery Application)**: Located in `/public/` and driven by `src/Controllers/PublicController.php`. Delivers ultra-fast, cached public reader views, category feeds, and article templates.

### 2. Bridge Script Delegation Pattern
To prevent visitors from executing core scripts directly inside private directories over HTTP, public endpoints use **Bridge Delegation Scripts**:

```php
<?php
// Inside public/admin/article-create.php
require_once __DIR__ . '/../../admin/article-create.php';
?>
```
This forwards web requests originating from the exposed `/public/` webroot into protected core controller scripts outside the public directory.

### 3. Core Classes & Engine Breakdown

#### `src/Core/Database.php`
- **Pattern**: PDO Singleton Pattern.
- **Functionality**: Manages database connection pool with prepared statement enforcement, auto-detects missing tables, executes auto-migrations from schema definitions, and seeds default database content.

#### `src/Core/Auth.php`
- **Functionality**: Handles staff user sessions, password hashing via `PASSWORD_BCRYPT`, CSRF token generation and validation (`verifyCsrfToken()`), and brute-force protection using `login_attempts` to temporarily block IP addresses exceeding login limits.

#### `src/Core/Sanitizer.php`
- **Functionality**: Provides strict XSS protection using tag whitelisting, prevents script injection, and contains the core **Shortcode Compiler Engine** (`parseShortcodes()`) which converts raw shortcode markup into responsive HTML elements with CSS float utility classes.

#### `src/Core/TemplateEngine.php`
- **Functionality**: Powers header, view, and footer composition, loads dynamic layout blueprints (`article-standard.php`, `article-investigative.php`, `article-opinion.php`), and formats Khmer and English dates seamlessly.

#### `src/Core/helpers.php`
- **Functionality**: Stores global utility functions including `get_translation_maps()`, `url()`, `asset()`, `sanitize()`, `e()`, and `calculate_reading_time()`.

#### `src/Controllers/AdminController.php`
- **Functionality**: Handles admin authentication logic, article list rendering, draft/published saving, validation error handling, image upload processing, user management actions, and session toast generation.

#### `src/Controllers/PublicController.php`
- **Functionality**: Serves the homepage news grid, category filtering, full-text search query execution, article view counter increments, single article view blueprint selection, subscriber submissions, and AJAX summary preview responses.

---

### 4. Centralized Bilingual Engine (Khmer & English)
All UI labels, categories, navigation items, and button states are centrally declared in `src/Core/helpers.php` via `get_translation_maps()`:

- **Server-Side Rendering**: PHP renders strings in the active session language using `__('key')`.
- **Client-Side Switcher**: JavaScript receives complete translation dictionaries via `json_encode()`. Switching languages (`setLanguage('kh')` or `setLanguage('en')`) updates text dynamically across the DOM without requiring a full page refresh.

---

### 5. Floating Toast Notification Engine
The platform includes a lightweight floating toast notification system.

```javascript
// Dynamic Javascript Invocation
window.showAdminToast("Article draft successfully saved!", "success");
```

- **Styling**: Fixed positioning (`top: 96px; right: 1.5rem; z-index: 1080`), crisp white card background, crimson/navy left accent border, custom padding (`0.9rem 1.25rem`), shadow depth, and close button `[✖]`.
- **Session Flash Integration**: Automatically reads `$_SESSION['toast_message']` and `$_SESSION['toast_type']` set by controller actions upon redirect, rendering immediate feedback to the editor.

---

### 6. Multi-Template Layout Blueprints
Authors can select one of 3 distinct layout blueprints when creating or editing an article:

1. **Standard (`standard`)**:
   - Classic 2-column news layout.
   - Main article column (8 cols) paired with a sticky sidebar (4 cols).
   - Features author spotlight box, related articles feed, and category tags.

2. **Investigative (`investigative`)**:
   - Single-column deep-read layout optimized for long-form reporting.
   - Dark hero header banner featuring full-width lead image and large headline.
   - Styled primary source citation boxes for highlighting key evidence.

3. **Opinion (`opinion`)**:
   - Columnist spotlight layout for op-eds and commentary.
   - Prominent author avatar header card with columnist bio.
   - Red-bordered pull quote callouts (`border-start: 4px solid #c8102e`) and interactive commentary box.

---

### 7. MS Word-Style Media Shortcodes & Text Wrapping
The CMS enables journalists to embed images and videos with natural text wrapping directly inside article body content:

| Shortcode Markup | Output Alignment & CSS Wrapping Behavior |
| :--- | :--- |
| `[image:1:left]` | Floated left (`float: left; margin: 0 1.5rem 1rem 0; max-width: 45%;`) with text wrapping around the right side. |
| `[image:1:right]` | Floated right (`float: right; margin: 0 0 1rem 1.5rem; max-width: 45%;`) with text wrapping around the left side. |
| `[image:1:center]` | Centered block element (`margin: 1.5rem auto; display: block; max-width: 100%;`) with full clear. |
| `[video:1:left]` | Floated left video embed player with text wrapping around the right side. |
| `[video:1:right]` | Floated right video embed player with text wrapping around the left side. |
| `[video:1:full]` | Full-width responsive 16:9 video player block spanning the entire column width. |

---

### 8. Interactive Saved Reading List & Offcanvas Drawer
- Readers click the bookmark icon `[🔖]` on any news card or article page.
- Article ID, title, image, and category metadata are saved locally in `localStorage`.
- The live header badge `#savedCountBadge` updates immediately.
- Opening the offcanvas drawer lists saved articles with one-click direct reading or removal (`[✖]`).

---

### 9. Quick Article Preview Modal
- Clicking **Quick View** on any article card opens `#quickViewModal`.
- An AJAX request fetches article data without navigating away from the main news feed.
- Readers can review article summaries, view counts, estimated reading time, author name, and publication date in a popup.

---

## 📁 Comprehensive Directory Structure

```text
News-platform-1/
├── admin/                          # Backend CMA Action Controllers
│   ├── dashboard.php               # Admin Editorial Control Dashboard
│   ├── article-create.php          # Draft/Create Article Controller
│   ├── article-edit.php            # Edit Article Controller
│   ├── users.php                   # Staff User Management Controller
│   ├── subscribers.php             # Reader Subscriptions Controller
│   ├── login.php                   # Admin Login Authentication View
│   ├── logout.php                  # Session Termination Endpoint
│   └── actions/                    # Form POST Handler Endpoints
│       ├── save-article.php        # Form Handler: Create/Update Article or Draft
│       ├── delete-article.php      # Form Handler: Delete Article Endpoint
│       ├── upload-image.php        # AJAX Image & Media Upload Handler
│       ├── save-user.php           # Form Handler: Create/Update User Account
│       └── delete-user.php         # Form Handler: Delete Staff User Endpoint
│
├── config/
│   └── database.php                # PDO Connection & Credentials Configuration
│
├── languages/                      # Translation Dictionaries
│   ├── common.php                  # Session Language Switcher Logic
│   ├── lang_en.php                 # English Dictionary Matrix
│   └── lang_kh.php                 # Khmer Dictionary Matrix
│
├── public/                         # Public Webroot (Web Accessible Root)
│   ├── index.php                   # Homepage News Feed Controller & Entry Point
│   ├── article.php                 # Single Article Page Router & Controller
│   ├── subscribe.php               # Public Reader Subscription Endpoint
│   ├── admin/                      # Public Webroot Bridge Delegation Files
│   └── assets/
│       ├── css/
│       │   └── style.css           # BBC/CNA Flat Editorial Light Styling System
│       ├── js/
│       │   └── main.js             # Client Interactivity, Toast Engine, Saved List
│       └── uploads/                # Uploaded Media Directory (Images & Thumbnails)
│
├── src/                            # Core Framework Logic & Architecture
│   ├── Controllers/
│   │   ├── AdminController.php     # Administrative Business Logic & Form Validation
│   │   └── PublicController.php    # Reader View Data Fetching & Blueprint Handler
│   └── Core/
│       ├── Auth.php                # Security, Password Hashing, CSRF & Rate Limiting
│       ├── Database.php            # PDO Singleton, Schema Migration & Seeding Engine
│       ├── Sanitizer.php           # XSS Prevention & Media Shortcode Compiler
│       ├── TemplateEngine.php      # View Loader, Layout Dispatcher & Date Parser
│       └── helpers.php             # Translation Dictionaries & Global Utility Functions
│
├── templates/                      # Presentation Views & Layout Components
│   ├── layouts/
│   │   ├── header-public.php       # Reader Navigation Header & Language Switcher
│   │   ├── header-admin.php        # Admin Dashboard Navigation Header & Toast Container
│   │   └── footer.php              # Footer Component & Global JS Script Injections
│   ├── views/
│   │   ├── home.php                # Public Homepage Grid Layout View
│   │   ├── article-standard.php    # Standard 2-Column Article Blueprint
│   │   ├── article-investigative.php # Investigative Deep-Read Blueprint
│   │   └── article-opinion.php     # Opinion Columnist Spotlight Blueprint
│   └── components/
│       ├── quick-view-modal.php    # Article Summary Modal Popup Component
│       ├── saved-articles-modal.php# Offline Reading List Drawer Component
│       ├── reading-toolbar.php     # Top Scroll Reading Progress Bar
│       ├── citation-box.php        # Verified Primary Source Citation Box
│       └── sidebar.php             # Public Article Sidebar Component
│
├── seed_news.php                   # Database Seeder (Seeds 12 Sample Articles)
├── schema.sql                      # Production SQL Schema Dump
└── README.md                       # System Architecture & Usage Documentation
```

---

## 🗄️ Database Relational Schema

The application database `news_platform` utilizes 5 structured relational tables:

```mermaid
erdiagram
    USERS ||--o{ ARTICLES : "authors"
    CATEGORIES ||--o{ ARTICLES : "categorizes"
    USERS {
        int id PK
        string username UK
        string password_hash
        string email
        enum role "admin, editor, reporter"
        datetime created_at
    }
    CATEGORIES {
        int id PK
        string slug UK
        string name_en
        string name_kh
    }
    ARTICLES {
        int id PK
        string title_en
        string title_kh
        string slug UK
        int category_id FK
        int author_id FK
        enum template "standard, investigative, opinion"
        enum status "published, draft"
        text content_en
        text content_kh
        string featured_image
        int views
        datetime created_at
    }
    SUBSCRIBERS {
        int id PK
        string email UK
        string category_preference
        datetime created_at
    }
    LOGIN_ATTEMPTS {
        int id PK
        string ip_address
        string username
        datetime attempted_at
    }
```

---

## ❓ Troubleshooting & FAQ

### Q1: How do I launch the application on XAMPP?
1. Open **XAMPP Control Panel** and start **Apache** and **MySQL**.
2. Ensure the code is located in `C:\xampp\htdocs\News-platform-1\`.
3. Open `http://localhost/News-platform-1/public/` in Google Chrome, Microsoft Edge, or Firefox.

### Q2: What are the admin login credentials?
- Open `http://localhost/News-platform-1/public/admin/login.php`.
- **Username:** `admin` | **Password:** `admin123`
- Additional accounts: Editor (`eleanor_vane` / `admin123`), Reporter (`reporter` / `admin123`).

### Q3: Why did article creation or saving draft fail?
- Ensure **Title**, **Category**, and **Article Content** fields are filled in.
- If you are uploading an image, ensure the file is an image (`.jpg`, `.jpeg`, `.png`, `.webp`) under 5MB.
- Floating toast messages will notify you of specific validation issues.

### Q4: How do I re-seed sample news data?
- Visit `http://localhost/News-platform-1/seed_news.php` in your browser. This resets and loads 12 fresh news stories across all categories.

---

## 📄 License
© 2026 **NewsPlatform CMS**. Built for professional digital newsrooms, journalists, and web application development learning. All rights reserved.