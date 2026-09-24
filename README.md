# NewsPlatform CMS

![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.2-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![Architecture](https://img.shields.io/badge/Architecture-Decoupled_CMA%2FCDA-007ACC?style=flat-square)

A production-grade, decoupled **Content Management System (CMS)** and **Content Delivery Application (CDA)** built for digital newsrooms, editorial publishing, and journalism.

---

> 🎓 **Student & Presentation Summary (Quick Guide for Class):**
> 
> This web application is a full-featured **News Publishing Platform** (similar to BBC News or Fresh News). It is divided into two distinct sections:
> 
> 1. **🌐 Public Reader Website (`/public`)**: Allows readers to browse breaking news, switch between Khmer & English languages instantly, save articles offline to a reading list, preview summaries via popups, and read full articles across 3 custom design templates.
> 2. **🔐 Backend Admin Panel (`/public/admin`)**: A secure control dashboard for journalists and editors to draft articles, manage categories, upload media, select layout blueprints, and publish content.

---

## 💡 Quick Reference Card for Beginners

| Topic | Details & Instructions |
| :--- | :--- |
| 🌐 **Public Reader Website** | `http://localhost/News-platform-1/public/` |
| 🔐 **Admin Login Portal** | `http://localhost/News-platform-1/public/admin/login.php` |
| 🔑 **Default Admin Credentials** | **Username:** `admin` \| **Password:** `admin123` |
| 🔑 **Default Editor Credentials** | **Username:** `eleanor_vane` \| **Password:** `admin123` |
| 🗄️ **Database Setup** | Automatic! Tables & sample news articles auto-create on first page load. |

---

## 📋 Table of Contents

- [💡 Quick Reference Card for Beginners](#-quick-reference-card-for-beginners)
- [✨ Key System Features](#-key-system-features)
- [💻 Prerequisites & Technical Stack](#-prerequisites--technical-stack)
- [🚀 Quick Installation & Setup Guide](#-quick-installation--setup-guide)
- [🔑 Admin Credentials & Access Levels](#-admin-credentials--access-levels)
- [⚙️ Deep-Dive Architecture & Implementation](#️-deep-dive-architecture--implementation)
  - [1. Decoupled Webroot Isolation (CMA vs CDA)](#1-decoupled-webroot-isolation-cma-vs-cda)
  - [2. Public Bridge Script Delegation Pattern](#2-public-bridge-script-delegation-pattern)
  - [3. Centralized Bilingual Engine (Khmer & English)](#3-centralized-bilingual-engine-khmer--english)
  - [4. Interactive Saved Reading List & Drawer](#4-interactive-saved-reading-list--drawer)
  - [5. Quick Article Preview Modal](#5-quick-article-preview-modal)
  - [6. Multi-Template Layout Blueprints](#6-multi-template-layout-blueprints)
  - [7. Media Shortcodes & Text Wrapping](#7-media-shortcodes--text-wrapping)
- [📁 Comprehensive Directory Structure](#-comprehensive-directory-structure)
- [🗄️ Database Relational Schema](#️-database-relational-schema)
- [❓ Troubleshooting & FAQ](#-troubleshooting--faq)

---

## ✨ Key System Features

- 📰 **BBC/CNA Flat Editorial Design**: High-contrast, sharp light mode with crimson red accents (`#c8102e`), navy headings (`#0f172a`), and zero elevation box-shadows (`border: 1px solid #e5e7eb`).
- 🌐 **Single-Source Bilingual System**: Centralized translation dictionary mapping Khmer and English. Allows real-time client-side language switching without losing user state.
- 🔖 **Saved Reading List**: Client-side article bookmarking using `localStorage` with a live header badge counter (`#savedCountBadge`) and offcanvas drawer.
- 👁️ **Quick Article Preview**: Popup modal for reading article summaries, metadata, view counts, and reading times without full page reloads.
- 📐 **3 Custom Article Layout Blueprints**:
  - `Standard`: 2-column editorial layout with sticky sidebar and author spotlight.
  - `Investigative`: 1-column deep-read layout with dark hero banner and verified primary citation cards.
  - `Opinion`: Columnist avatar header card with styled pull quotes and commentary player.
- 🖼️ **Media Shortcodes & Text Wrapping**: Alignment shortcodes (`[image:1:left]`, `[image:1:right]`, `[video:1:full]`) for seamless paragraph text wrapping.
- 📊 **Reading Progress Bar**: Dynamic top scroll progress bar (`#readingProgressBar`) tracking long-form reading depth.

---

## 💻 Prerequisites & Technical Stack

| Component | Minimum Requirement | Recommended | Notes |
| :--- | :--- | :--- | :--- |
| **Web Server** | Apache 2.4+ | XAMPP / WAMP / Laragon | `mod_rewrite` enabled |
| **PHP Engine** | PHP 8.0+ | PHP 8.2 or 8.3 | `pdo_mysql`, `gd`, `session` enabled |
| **Database** | MySQL 5.7+ | MySQL 8.0+ / MariaDB 10.3+ | UTF-8 (`utf8mb4_unicode_ci`) |
| **Frontend Framework** | Bootstrap 5.3.2 | Loaded via CDN | Includes Bootstrap Icons |

---

## 🚀 Quick Installation & Setup Guide

### Step 1: Copy Project to Apache `htdocs`
Copy the project folder to your local web server root directory:
- **XAMPP**: `C:\xampp\htdocs\News-platform-1\`
- **WAMP**: `C:\wamp64\www\News-platform-1\`

### Step 2: Database Configuration
Verify your database connection settings in [config/database.php](file:///c:/xampp/htdocs/News-platform-1/config/database.php):

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
No manual database imports required! Opening the website automatically:
1. Creates the `news_platform` database.
2. Creates all 5 relational tables (`users`, `categories`, `articles`, `subscribers`, `login_attempts`).
3. Seeds initial staff user accounts and news stories.

*(Optional: Run `http://localhost/News-platform-1/seed_news.php` in your browser anytime to re-seed 12 fresh news articles).*

---

## 🔑 Admin Credentials & Access Levels

Log into the backend Content Management Application at `http://localhost/News-platform-1/public/admin/login.php`:

| Role | Username | Password | Privileges & Access |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | Full control (articles, categories, users, subscribers) |
| **Senior Editor** | `eleanor_vane` | `admin123` | Article drafting, editing, and layout publishing |

---

## ⚙️ Deep-Dive Architecture & Implementation

### 1. Decoupled Webroot Isolation (CMA vs CDA)
- **CMA (Content Management Application)**: Located in `/admin/` and `/src/Controllers/AdminController.php`. Provides administrative workflows.
- **CDA (Content Delivery Application)**: Located in `/public/` and `/templates/`. Delivers lightweight, optimized reader views.

### 2. Public Bridge Script Delegation Pattern
To prevent visitors from directly opening private backend files over HTTP, public URLs use **Bridge Scripts**:

```php
<?php
// Inside public/admin/article-create.php
require_once __DIR__ . '/../../admin/article-create.php';
?>
```
This forwards web requests from the public folder to the protected core controller outside the public directory.

### 3. Centralized Bilingual Engine (Khmer & English)
All language translations are stored in a single source of truth in `src/Core/helpers.php` via `get_translation_maps()`. JavaScript receives this data via `json_encode()`, allowing instant client-side translation without page reloads.

### 4. Interactive Saved Reading List & Drawer
- Clicking bookmark `[🔖]` saves the article ID and title metadata into `localStorage`.
- Live badge `#savedCountBadge` updates the count immediately.
- Opening the offcanvas drawer lists all bookmarked items with one-click removal `[✖]`.

### 5. Quick Article Preview Modal
Displays a modal pop-up with full summary, category tags, author details, reading time, view count, and a direct link to the full article page.

### 6. Multi-Template Layout Blueprints
- **Standard (`standard`)**: Classic 2-column layout with main article body, sticky sidebar, and author box.
- **Investigative (`investigative`)**: Deep-read single-column view with dark hero banner and verified primary citation cards.
- **Opinion (`opinion`)**: Author spotlight header with columnist bio avatar, red-bordered pull quotes, and commentary section.

### 7. Media Shortcodes & Text Wrapping
Authors can format uploaded media in the text editor using clean shortcodes:

| Shortcode | Alignment & Layout Behavior |
| :--- | :--- |
| `[image:1:left]` | Floated left with paragraph text wrapping around right side |
| `[image:1:right]` | Floated right with paragraph text wrapping around left side |
| `[image:1:center]` | Centered block image |
| `[video:1:left]` | Floated left video player with text wrapping |
| `[video:1:right]` | Floated right video player with text wrapping |

---

## 📁 Comprehensive Directory Structure

```text
News-platform-1/
├── admin/                          # Backend CMA Controllers & Actions
│   ├── dashboard.php               # Admin Editorial Control Panel
│   ├── article-create.php          # Draft Article Form Controller
│   ├── article-edit.php            # Edit Article Form Controller
│   ├── users.php                   # Staff User Management
│   ├── subscribers.php             # Reader Subscription List
│   └── actions/                    # POST Action Handlers (save, delete, upload)
│
├── config/
│   └── database.php                # PDO Connection Credentials Configuration
│
├── languages/                      # Translation Dictionaries
│   ├── common.php                  # Session Language Controller
│   ├── lang_en.php                 # English Translation Dictionary
│   └── lang_kh.php                 # Khmer Translation Dictionary
│
├── public/                         # Public Webroot (Exposed to Web)
│   ├── index.php                   # Homepage Feed Entry Point
│   ├── article.php                 # Single Article View Controller
│   ├── subscribe.php               # Subscriber AJAX Endpoint
│   ├── admin/                      # Public Admin Bridge Files
│   └── assets/
│       └── css/style.css           # BBC/CNA Flat Light CSS Styling System
│
├── src/                            # Application Core & Business Logic
│   ├── Controllers/                # Controllers (AdminController, PublicController)
│   └── Core/
│       ├── Auth.php                # Authentication, CSRF, Password Hashing
│       ├── Database.php            # PDO Singleton & Auto Schema Migration
│       ├── Sanitizer.php           # XSS Security Cleaner & Shortcode Parser
│       ├── TemplateEngine.php      # Layout Rendering & Date Formatter
│       └── helpers.php             # Helper Functions & Translation Engine
│
├── templates/                      # Presentation Views & UI Components
│   ├── layouts/
│   │   ├── header-public.php       # Reader Navigation & Sticky Header
│   │   ├── header-admin.php        # Admin Dashboard Navigation Header
│   │   └── footer.php              # Footer & JS Interactivity Engine
│   ├── views/
│   │   ├── home.php                # Homepage Feed View
│   │   ├── article-standard.php    # Standard 2-Column Layout View
│   │   ├── article-investigative.php # Investigative Deep-Read Layout View
│   │   └── article-opinion.php     # Opinion Columnist Spotlight Layout View
│   └── components/
│       ├── quick-view-modal.php    # Article Preview Modal Component
│       ├── saved-articles-modal.php# Saved Reading List Drawer Component
│       ├── reading-toolbar.php     # Reading Progress Bar Component
│       ├── citation-box.php        # Verified Primary Source Citation Box
│       └── sidebar.php             # Public Sidebar Component
│
├── seed_news.php                   # Database Seeder (12 Sample Articles)
├── schema.sql                      # Production Database SQL Schema
└── README.md                       # Comprehensive Project Documentation
```

---

## 🗄️ Database Relational Schema

The application uses 5 relational tables:

1. **`users`**: Stores staff accounts with bcrypt password hashes (`id`, `username`, `password_hash`, `email`, `role`, `created_at`).
2. **`categories`**: Stores article categories with Khmer/English names (`id`, `slug`, `name_en`, `name_kh`).
3. **`articles`**: Main repository for articles (`id`, `title_en`, `title_kh`, `slug`, `category_id`, `author_id`, `template`, `content_en`, `content_kh`, `featured_image`, `views`, `created_at`).
4. **`subscribers`**: Email subscription list (`id`, `email`, `category_preference`, `created_at`).
5. **`login_attempts`**: Security log for brute-force rate-limiting (`id`, `ip_address`, `username`, `attempted_at`).

---

## ❓ Troubleshooting & FAQ

### Q1: How do I open the website on XAMPP?
- Start **Apache** and **MySQL** in XAMPP Control Panel.
- Open `http://localhost/News-platform-1/public/` in your browser.

### Q2: What are the admin login details?
- Go to `http://localhost/News-platform-1/public/admin/login.php`.
- **Username:** `admin` | **Password:** `admin123`.

### Q3: How do I re-seed sample news data?
- Visit `http://localhost/News-platform-1/seed_news.php` in your browser to reload 12 sample news stories.

---

## 📄 License
© 2026 **NewsPlatform CMS**. Built for professional digital newsrooms and student learning. All rights reserved.