# NewsPlatform CMS

**A production-grade Decoupled Content Management System (CMS) for independent journalism, media outlets, and editorial publishing.**

Built with standard, beginner-friendly PHP 8.0+, a clean decoupled CMA (Admin Backend) and CDA (Public Reader Frontend) architecture, dynamic multi-template layout blueprints, MS Word-style media text wrapping, full bilingual English & Khmer support, and a rich admin editorial control panel.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Prerequisites & Requirements](#prerequisites--requirements)
- [Installation & Setup Guide](#installation--setup-guide)
- [🔑 How to Login as Admin (Default Credentials)](#-how-to-login-as-admin-default-credentials)
- [⚙️ How It Works](#️-how-it-works)
  - [1. Decoupled Architecture (CMA vs CDA)](#1-decoupled-architecture-cma-vs-cda)
  - [2. Multi-Template Layout Blueprints](#2-multi-template-layout-blueprints)
  - [3. MS Word-Style Media Shortcodes & Text Wrapping](#3-ms-word-style-media-shortcodes--text-wrapping)
  - [4. Smart Media Deduplication](#4-smart-media-deduplication)
  - [5. Manual Drop-Cap Control](#5-manual-drop-cap-control)
  - [6. Interactive Reader Font Size Control (`A-` `100%` `A+`)](#6-interactive-reader-font-size-control-a--100-a)
  - [7. Bilingual System (Khmer & English)](#7-bilingual-system-khmer--english)
- [Project Directory Structure](#project-directory-structure)
- [Database Schema](#database-schema)
- [Admin Editorial Features (CMA)](#admin-editorial-features-cma)
- [Troubleshooting & FAQs](#troubleshooting--faqs)

---

## ℹ️ Overview

NewsPlatform CMS is an editorial newsroom management platform inspired by modern publishing stacks (such as WordPress VIP, Arc Publishing, and CNA/The Guardian). It strictly separates:
- **CMA (Content Management Application)**: Backend admin panel (`/admin/*`) for reporters, editors, and administrators.
- **CDA (Content Delivery Application)**: Public frontend reader experience (`/public/*` or root) for visitors.

The application features **3 distinct article layout blueprints** (`Standard`, `Investigative`, `Opinion`), an integrated **Quill WYSIWYG rich text editor**, **MS Word-style floating media alignment** (`left`, `right`, `center`, `full`), **multi-image drag-and-drop file upload**, **smart media deduplication**, and **bilingual Khmer/English UI translation**.

---

## 💻 Prerequisites & Requirements

Before setting up NewsPlatform CMS, ensure your local web environment has:

| Dependency | Minimum Requirement | Recommended |
|------------|---------------------|-------------|
| **Web Server** | Apache 2.4+ (with `mod_rewrite` enabled) | XAMPP / WAMP / Laragon |
| **PHP Version** | PHP 8.0+ | PHP 8.2 or 8.3 |
| **PHP Extensions** | `pdo`, `pdo_mysql`, `gd` (for image processing), `json`, `session` | Default in XAMPP |
| **Database** | MySQL 5.7+ or MariaDB 10.3+ | MySQL 8.0+ |
| **Browser** | Any modern web browser | Chrome / Edge / Firefox |

---

## 🚀 Installation & Setup Guide

### Step 1: Copy Project to Web Server Root

Place the project directory inside your local web server root:
- **XAMPP**: `C:\xampp\htdocs\News-platefrom-1\`
- **WAMP**: `C:\wamp64\www\News-platefrom-1\`
- **Laragon**: `C:\laragon\www\News-platefrom-1\`

### Step 2: Database Configuration

Edit `config/database.php` to set your local MySQL connection details:

```php
return [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'news_platform',
    'username' => 'root',
    'password' => '', // Leave empty if default XAMPP MySQL root password
    'charset'  => 'utf8mb4',
];
```

### Step 3: Automatic Database & Table Initialization

You **do not need to manually import SQL files!**  
On your first browser access, `Database::autoInitializeSchema()` automatically:
1. Creates the `news_platform` database if it does not exist.
2. Imports all required tables (`users`, `categories`, `articles`, `subscribers`, `login_attempts`).
3. Seeds default admin staff accounts and sample news articles.

*(Optional manual import: You can also import `schema.sql` into phpMyAdmin if desired).*

### Step 4: Access the Website

- **Public Reader Homepage (CDA)**:  
  `http://localhost/News-platefrom-1/public/`  
  *(or `http://localhost/News-platefrom-1/`)*

- **Admin Login Portal (CMA)**:  
  `http://localhost/News-platefrom-1/public/admin/login.php`  
  *(or `http://localhost/News-platefrom-1/admin/login.php`)*

---

## 🔑 How to Login as Admin (Default Credentials)

To access the backend editorial control panel (`/admin/login.php`), use the pre-configured staff accounts:

### 👑 Administrator Account (Full Control)
- **Login URL**: `http://localhost/News-platefrom-1/public/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`
- **Permissions**: Full access to draft, publish, edit, delete articles, view subscriber list, and manage system users.

### ✍️ Senior Editor Account
- **Username**: `eleanor_vane`
- **Password**: `admin123`
- **Permissions**: Draft, edit, and publish articles across all category blueprints.

> 🔒 *Security Tip: After logging in for the first time, you can update staff credentials or change passwords in the database.*

---

## ⚙️ How It Works

### 1. Decoupled Architecture (CMA vs CDA)
- **CMA (Admin Editorial Panel)** handles content creation, user authentication, CSRF validation, file uploads, and status management (`draft` / `published` / `archived`).
- **CDA (Public Reader Frontend)** consumes content safely through XSS sanitization (`Sanitizer::cleanHtml`), formats dates, tracks view counts, and renders responsive layouts.

---

### 2. Multi-Template Layout Blueprints
When creating or editing an article in `admin/article-create.php` or `admin/article-edit.php`, editors can select one of **3 layout blueprints**:

1. **Standard Blueprint (`standard`)**:
   - Classic 2-column newsroom layout with main editorial body on the left and sticky sidebar on the right.
2. **Investigative Blueprint (`investigative`)**:
   - Single-column deep-read layout featuring a dark hero header banner, verified primary citation cards, and pull quote callout boxes.
3. **Opinion Blueprint (`opinion`)**:
   - Columnist profile spotlight header card with columnist bio, avatar, and warm editorial typography.

---

### 3. MS Word-Style Media Shortcodes & Text Wrapping
Authors can insert photos and videos **anywhere between paragraphs** in the Quill WYSIWYG editor using MS Word-style alignment options:

#### Image Shortcodes
- `[image:1]` or `[image:1:center]` &rarr; Centered Image #1
- `[image:1:left]` &rarr; **Float Left with Text Wrapping** (text wraps around right side of photo)
- `[image:1:right]` &rarr; **Float Right with Text Wrapping** (text wraps around left side of photo)
- `[image:1:full]` &rarr; Full-width photo block
- `[image:1:Custom Caption Text:left]` &rarr; Float Left with custom caption

#### Video Shortcodes
- `[video:1]` or `[video:1:center]` &rarr; Centered Video #1
- `[video:1:left]` &rarr; **Float Left with Text Wrapping** (text wraps around right side of video frame)
- `[video:1:right]` &rarr; **Float Right with Text Wrapping** (text wraps around left side of video frame)
- `[video:1:full]` &rarr; Full-width video block
- `[video:https://www.youtube.com/watch?v=...:left]` &rarr; Direct YouTube embed floated left

#### Quick Insertion in Admin Editor
Under the Quill WYSIWYG editor in `admin/article-create.php`, quick insert button groups (`[image:1]`, `Left`, `Right`, `Full`) let you insert aligned media tags with 1 click!

---

### 4. Smart Media Deduplication
If an author embeds a photo (`[image:1]`) or video (`[video:1]`) directly inside the article body text:
- The system automatically detects its presence inside `$article['content']`.
- **Top Video Player** and **Top Photo Carousel** automatically suppress referenced items so photos/videos **never display twice**!

---

### 5. Manual Drop-Cap Control
- Near the top header of the article body editor in `admin/article-edit.php`, there is a clean toggle switch:
  `[ ] បង្ហាញអក្សរធំដើមកថាខណ្ឌ (Enable First Paragraph Drop-Cap)`
- By default, it is **turned OFF**, preventing unwanted large initial letters on complex scripts.
- Turning it **ON** enables a styled, large drop-cap initial letter for the article's lead paragraph.

---

### 6. Interactive Reader Font Size Control (`A-` `100%` `A+`)
On public article pages (`article.php`), readers have a compact font control pill widget right above the article text:
- **`A-`**: Decreases font size for comfortable reading.
- **`100%`**: Resets font size back to default (18px).
- **`A+`**: Increases font size.
- User preference is saved in `localStorage` across page visits.

---

### 7. Bilingual System (Khmer & English)
- **Language Switcher**: Click `EN` or `🇰🇭 KH` in the header navbar to switch UI languages instantly.
- **Language Dictionaries**:
  - `languages/lang_en.php` (English dictionary)
  - `languages/lang_kh.php` (Khmer dictionary)
- **Single-Language Captions**: Captions render strictly in the active language (`វីដេអូរាយការណ៍ #១` / `Video Report #1`) without awkward dual slashes (`/`).

---

## 📁 Project Directory Structure

```
News-platefrom-1/
│
├── admin/                          # Backend CMA Entry Points
│   ├── dashboard.php               # Admin Editorial Dashboard
│   ├── article-create.php          # Draft New Article Form
│   ├── article-edit.php            # Edit Existing Article Form
│   ├── users.php                   # Staff User Management
│   ├── subscribers.php             # Reader Feed Subscribers List
│   ├── export-subscribers.php      # Export Subscribers to CSV
│   └── actions/
│       ├── save-article.php        # POST: Save/Update Article
│       ├── delete-article.php      # POST: Delete Article
│       └── upload-image.php        # AJAX: Direct Quill Image Upload
│
├── config/
│   └── database.php                # Database Connection Credentials
│
├── languages/                      # Translation Dictionaries
│   ├── common.php                  # Session Language Controller
│   ├── lang_en.php                 # English Translations
│   └── lang_kh.php                 # Khmer Translations
│
├── public/                         # Public CDA Reader Root
│   ├── index.php                   # Homepage News Feed
│   ├── article.php                 # Single Article View
│   ├── subscribe.php               # AJAX Subscriber Endpoint
│   ├── assets/
│   │   └── css/style.css           # CNA-Style Editorial CSS System
│   └── uploads/                    # Uploaded Featured & Gallery Images
│
├── src/
│   ├── Controllers/
│   │   ├── AdminController.php     # Admin CMA Business Logic
│   │   └── PublicController.php    # Reader CDA Business Logic
│   └── Core/
│       ├── Auth.php                # Authentication, RBAC, CSRF, Rate Limit
│       ├── Database.php            # PDO Singleton & Auto Schema Migration
│       ├── Sanitizer.php           # XSS Prevention & Media Shortcodes Parser
│       ├── TemplateEngine.php      # Page Layout Renderer
│       └── helpers.php             # Global Helper Functions (e, __, url, km_num)
│
├── templates/                      # View Templates & Layout Components
│   ├── layouts/
│   │   ├── header-public.php       # Public Header & Sticky Nav
│   │   ├── header-admin.php        # Admin Navbar
│   │   └── footer.php              # Shared Footer
│   ├── views/
│   │   ├── home.php                # Homepage Feed Grid
│   │   ├── article-standard.php    # Standard 2-Column Layout View
│   │   ├── article-investigative.php # Single-Column Dark Hero View
│   │   └── article-opinion.php     # Columnist Spotlight View
│   ├── admin/views/                # Admin Backend Views
│   │   ├── dashboard.php
│   │   └── article-form.php
│   └── components/                 # Reusable Components
│       ├── reading-toolbar.php     # Reader Font Size Control (A-/100%/A+)
│       ├── citation-box.php        # Compact Verified Source Citation Bar
│       ├── sidebar.php             # Public Sidebar (Most Read, Digest CTA)
│       └── share-buttons.php       # Social Share Buttons
│
├── seed_news.php                   # Real Data Seeder (12 Khmer News Posts)
├── schema.sql                      # Production Database Schema SQL
└── README.md                       # Complete Project Documentation
```

---

## 🗄️ Database Schema

The database consists of 5 relational tables:

1. **`users`**: Staff accounts (`admin`, `editor`, `reporter`) with bcrypt password hashes.
2. **`categories`**: Topic categories (`technology-ai`, `global-politics`, `climate-science`, `economy-markets`, etc.).
3. **`articles`**: Article repository storing headlines, WYSIWYG HTML content, template blueprints (`standard`, `investigative`, `opinion`), media URLs, `has_drop_cap` flag, and views counter.
4. **`subscribers`**: Reader feed subscription registrations with category preferences.
5. **`login_attempts`**: Security table for brute-force rate-limiting on login.

---

## ❓ Troubleshooting & FAQs

### Q: Why is my database empty after cloning?
- **A**: Access the homepage `http://localhost/News-platefrom-1/public/` in your browser. The system will automatically create the database tables and seed sample news articles.

### Q: How do I float a video or photo to the left of my text paragraph?
- **A**: In the editor, click the **Left** button under the image/video reference list (e.g., `[image:1:left]` or `[video:1:left]`). The text paragraph placed right after it will float and wrap around the media frame automatically.

### Q: How do I turn on or turn off the large initial Drop-Cap letter?
- **A**: When editing an article in `admin/article-edit.php`, check or uncheck the switch **`[ ] បង្ហាញអក្សរធំដើមកថាខណ្ឌ (Enable First Paragraph Drop-Cap)`** located right above the main text editor box.

---

## 📄 License

This project is built for professional independent journalism and news publishing.  
© 2026 **NewsPlatform CMS**. All rights reserved.