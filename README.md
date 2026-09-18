# NewsPlatform CMS

**A production-grade Decoupled Content Management System (CMS) for independent journalism and editorial publishing.**

Built with standard, beginner-friendly PHP 8.2+, a clean decoupled CMA/CDA architecture, dynamic multi-template layout blueprints, full bilingual English & Khmer support, and a rich admin editorial control panel.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Architecture](#architecture)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Installation & Setup](#installation--setup)
- [Configuration](#configuration)
- [Article Template Blueprints](#article-template-blueprints)
- [Admin Panel (CMA)](#admin-panel-cma)
- [Security Features](#security-features)
- [Bilingual Support (EN / KM)](#bilingual-support-en--km)
- [Media & Embeds](#media--embeds)
- [Core Classes Reference](#core-classes-reference)
- [URL Routing](#url-routing)
- [Tech Stack](#tech-stack)

---

## Overview

NewsPlatform CMS is a fully custom-built editorial CMS inspired by modern newsroom publishing systems like WordPress VIP, Arc Publishing, and The Guardian's CMS. It uses a **decoupled architecture** separating the Content Management Application (CMA) — the admin editorial backend — from the Content Delivery Application (CDA) — the public reader-facing frontend.

The system features **three distinct article template blueprints** that automatically render different layouts depending on the content type assigned by the editor, a **Quill.js WYSIWYG rich text editor**, interactive **photo gallery carousels**, embedded **audio/video media**, and a **full subscriber feed registration system**.

---

## Features

### Editorial / CMA (Admin Panel)
- **Rich Text Editor** — Quill.js WYSIWYG editor with full formatting toolbar (headings, bold, italic, lists, blockquotes, code blocks, links, images, video embeds)
- **Three Template Blueprints** — Standard, Investigative, Opinion layouts with live interactive preview switcher
- **Article Management** — Create, edit, delete, archive, publish/draft articles
- **Auto Slug Generator** — Real-time SEO URL slug auto-generation from title (editable)
- **Featured Image Upload** — JPEG/PNG/WEBP, 5MB max, server-side MIME validation
- **Video Embed** — YouTube/MP4 embed URL support
- **Audio Podcast Embed** — Spotify, SoundCloud, Anchor.fm iframe embed or direct MP3 stream
- **Photo Gallery** — Newline-separated image URL list renders interactive Bootstrap carousel
- **Verified Source Citations** — Primary source name + URL linked citation box
- **Breaking News Flag** — Per-article toggle for the live breaking news ticker
- **Status Control** — Draft / Published / Archived workflow
- **Category Assignment** — Dropdown category selector
- **Author Assignment** — Assign any active staff user as article author
- **Dashboard Analytics** — Live metrics cards (total articles, published, drafts, subscribers), Chart.js pie chart for template ratios and bar chart for traffic impressions
- **Live Search Filter** — Real-time client-side table search across title, category, and author
- **Status Tab Filter** — All / Published / Drafts / Archived tab filter system

### Public Reader / CDA (Frontend)
- **Responsive Homepage Feed** — Article grid with category filter and keyword search
- **Breaking News Ticker** — Scrollable live breaking news bar at top of page
- **Category Navigation Bar** — Sticky top nav with active category highlighting
- **Three Article Layouts** — Dynamic blueprint rendering based on `template_type`
- **Sidebar Widgets** — Most Read Stories, Daily Digest subscription CTA, Explore Topics
- **Subscriber Feed Modal** — AJAX-powered email subscription with topic preference
- **Article View Counter** — Per-article views count tracked on each page load
- **Related Articles** — Context-aware related articles from the same category

### Bilingual System
- Full **English** and **Khmer** UI translations across all pages using standard `languages/` array files
- Language switcher dropdown in header (persists across pages via session + cookie)
- Category names auto-translated in both directions (EN ↔ KM) regardless of DB storage format

---

## Architecture

```
┌─────────────────────────────────────────────────────┐
│                   NewsPlatform CMS                  │
│           Decoupled CMA + CDA Architecture          │
├──────────────────────┬──────────────────────────────┤
│   CMA (Admin Panel)  │    CDA (Public Reader)       │
│   /admin/*           │    /public/*                 │
│                      │                              │
│  AdminController     │  PublicController            │
│  • dashboard()       │  • home()                    │
│  • createArticle()   │  • article()                 │
│  • editArticle()     │  • search()                  │
│  • saveArticle()     │  • subscribe()               │
│  • deleteArticle()   │                              │
└──────────────────────┴──────────────────────────────┘
               │                │
               └────────────────┘
                     Core Layer
         ┌──────────────────────────────┐
         │  Database (PDO Singleton)    │
         │  Auth (RBAC + CSRF + Rate)   │
         │  languages/common.php        │
         │  TemplateEngine (Renderer)   │
         │  helpers.php (Global fns)    │
         └──────────────────────────────┘
```

**Request Flow:**
1. Entry point (`/public/index.php`, `/public/article.php`, etc.) bootstraps the autoloader & helpers
2. Relevant Controller method is called
3. Controller queries Database, applies business logic, and passes data to TemplateEngine
4. TemplateEngine renders the appropriate Layout (header + view + footer)
5. Views are injected into layout using `include` with scoped variable injection

---

## Project Structure

```
News-platefrom/
│
├── admin/                          # Admin panel entry points
│
├── config/
│   └── database.php                # DB connection credentials
│
├── languages/                      # Modular Translation Files
│   ├── common.php                  # Language switcher & loader controller
│   ├── lang_en.php                 # English translation array ($lang)
│   └── lang_kh.php                 # Khmer translation array ($lang)
│
├── public/                         # Web-accessible root (document root)
│   ├── index.php                   # CDA Homepage entry point
│   ├── article.php                 # Single article view entry point
│   ├── subscribe.php               # Subscription AJAX handler
│   ├── admin/
│   │   ├── dashboard.php           # Admin dashboard entry point
│   │   ├── article-create.php      # New article form entry point
│   │   ├── article-edit.php        # Edit article form entry point
│   │   ├── login.php               # Staff login entry point
│   │   ├── logout.php              # Logout action
│   │   └── actions/
│   │       ├── save-article.php    # POST: Create/update article
│   │       └── delete-article.php  # POST: Delete article
│   ├── assets/
│   │   └── css/
│   │       └── style.css           # Custom editorial CSS
│   └── uploads/                    # Uploaded featured images
│
├── src/
│   ├── Controllers/
│   │   ├── AdminController.php     # All CMA (admin) business logic
│   │   └── PublicController.php    # All CDA (public) business logic
│   └── Core/
│       ├── Auth.php                # Session, RBAC, CSRF, rate limiting
│       ├── Database.php            # PDO singleton + auto-migration
│       ├── TemplateEngine.php      # Layout renderer + helper methods
│       └── helpers.php             # Global helper functions (e, __, url, cat_name)
│
├── templates/
│   ├── layouts/
│   │   ├── header-public.php       # CDA HTML head + nav + category bar
│   │   ├── header-admin.php        # CMA HTML head + admin nav
│   │   └── footer.php              # Shared footer
│   ├── views/
│   │   ├── home.php                # Homepage article feed
│   │   ├── article-standard.php    # Blueprint 1: Standard 2-column layout
│   │   ├── article-investigative.php # Blueprint 2: Investigative dark hero
│   │   └── article-opinion.php     # Blueprint 3: Opinion columnist layout
│   ├── admin/
│   │   └── views/
│   │       ├── dashboard.php       # Admin dashboard view
│   │       └── article-form.php    # Article create/edit form view
│   └── components/
│       ├── citation-box.php        # Verified source citation card component
│       ├── sidebar.php             # Public sidebar (Most Read, Digest CTA, Topics)
│       └── subscribe-modal.php     # Email subscription modal
│
└── schema.sql                      # Full MySQL database schema
```

---

## Database Schema

The system uses **5 tables**:

### `users` — CMS Staff Accounts
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT AUTO_INCREMENT | Primary key |
| `username` | VARCHAR(50) UNIQUE | Login username |
| `email` | VARCHAR(100) UNIQUE | Staff email |
| `password_hash` | VARCHAR(255) | bcrypt password hash |
| `role` | ENUM | `admin` / `editor` / `reporter` |
| `is_active` | TINYINT(1) | Account enabled flag |

### `categories` — News Topic Categories
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT AUTO_INCREMENT | Primary key |
| `name` | VARCHAR(100) | Category display name (stored in Khmer by default) |
| `slug` | VARCHAR(100) UNIQUE | URL-safe slug |

### `articles` — Main Content Repository
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT AUTO_INCREMENT | Primary key |
| `title` | VARCHAR(255) | Article headline |
| `slug` | VARCHAR(255) UNIQUE | SEO URL slug |
| `summary` | TEXT | Standfirst / lead paragraph |
| `content` | LONGTEXT | Full article body (HTML from Quill WYSIWYG) |
| `featured_image` | VARCHAR(255) | Path to uploaded cover image |
| `video_embed_url` | VARCHAR(255) | YouTube/MP4 embed URL |
| `audio_embed_url` | VARCHAR(500) | Spotify/SoundCloud/MP3 podcast URL |
| `gallery_images` | TEXT | Newline-separated image URLs for carousel |
| `reference_url` | VARCHAR(255) | Primary source citation URL |
| `reference_source_name` | VARCHAR(150) | Primary source citation name |
| `category_id` | INT FK | References `categories.id` |
| `author_id` | INT FK | References `users.id` |
| `template_type` | ENUM | `standard` / `investigative` / `opinion` |
| `is_breaking` | TINYINT(1) | Breaking news ticker flag |
| `status` | ENUM | `draft` / `published` / `archived` |
| `views_count` | INT | Total reader page views |
| `published_at` | DATETIME | Publication timestamp |

### `subscribers` — Public Feed Registrations
| Column | Type | Description |
|--------|------|-------------|
| `email` | VARCHAR(100) UNIQUE | Subscriber email |
| `category_preference` | INT FK | Optional topic preference |
| `status` | ENUM | `active` / `unsubscribed` |

### `login_attempts` — Brute Force Protection
| Column | Type | Description |
|--------|------|-------------|
| `ip_address` | VARCHAR(45) | Requester IP address |
| `attempted_at` | DATETIME | Attempt timestamp |

---

## Installation & Setup

### Requirements
- PHP **8.0+**
- MySQL **5.7+** or MariaDB **10.3+**
- Apache with **mod_rewrite** (or NGINX equivalent)
- XAMPP / WAMP / Laragon (for local development)

### Steps

**1. Clone / Copy the project**
```bash
# Place the project inside your web server root
# For XAMPP:
C:\xampp\htdocs\News-platefrom\
```

**2. Configure the database**

Edit `config/database.php` with your MySQL credentials:
```php
return [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'news_platform',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
];
```

**3. Auto-initialize schema**

The system **automatically creates the database and runs `schema.sql`** on first request via `Database::autoInitializeSchema()`. No manual import needed.

**4. Access the application**

| URL | Description |
|-----|-------------|
| `http://localhost/News-platefrom/public/` | Public reader homepage (CDA) |
| `http://localhost/News-platefrom/public/admin/login.php` | Admin staff login (CMA) |

---

## Article Template Blueprints

The CMS supports **three distinct editorial layout blueprints**, selected per article by the editor:

### Blueprint 1 — Standard (`template_type = 'standard'`)
**File:** `templates/views/article-standard.php`
- Classic **2-column layout** with article body on the left and a sidebar on the right

### Blueprint 2 — Investigative (`template_type = 'investigative'`)
**File:** `templates/views/article-investigative.php`
- **Single-column deep-read format** with a dark hero banner header section

### Blueprint 3 — Opinion (`template_type = 'opinion'`)
**File:** `templates/views/article-opinion.php`
- **Columnist profile-focused layout** with warm sepia aesthetic

---

## Bilingual Support (EN / KM)

The application features a clean, standard language dictionary structure in the `languages/` folder:

- **`languages/common.php`**: Manages session language switching (`?lang=kh` or `?lang=en`) and loads the appropriate dictionary file.
- **`languages/lang_en.php`**: Contains the English translation array (`$lang = array();`).
- **`languages/lang_kh.php`**: Contains the Khmer translation array (`$lang = array();`).

### How To Use In Code

**Procedural Style:**
```php
include 'languages/common.php';
echo $lang['site_title'];
```

**Helper Function Style:**
```php
echo __('site_title');
```

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend Language** | PHP 8.0+ (OOP, PDO) |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ |
| **CSS Framework** | Bootstrap 5.3.2 |
| **Icons** | Bootstrap Icons 1.11.1 |
| **Rich Text Editor** | Quill.js 2.0.2 |
| **Charts** | Chart.js 4.x |
| **Fonts** | Google Fonts — Inter, Lora, Noto Sans Khmer |

---

## License

This project is built for educational and independent journalism purposes.  
© 2026 NewsPlatform CMS. All rights reserved.