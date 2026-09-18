# NewsPlatform CMS

**A production-grade Decoupled Content Management System (CMS) for independent journalism and editorial publishing.**

Built with strict Object-Oriented PHP 8.2+, a clean decoupled CMA/CDA architecture, dynamic multi-template layout blueprints, full bilingual English & Khmer support, and a rich admin editorial control panel.

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
- Full **English** and **Khmer** UI translations across all pages
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
         │  Language (EN/KM i18n)       │
         │  TemplateEngine (Renderer)   │
         │  helpers.php (Global fns)    │
         └──────────────────────────────┘
```

**Request Flow:**
1. Entry point (`/public/index.php`, `/public/article.php`, etc.) bootstraps the autoloader
2. Relevant Controller method is called
3. Controller queries Database, applies business logic, and passes data to TemplateEngine
4. TemplateEngine renders the appropriate Layout (header + view + footer)
5. Views are injected into layout using `include` with scoped variable injection

---

## Project Structure

```
News-platefrom/
│
├── admin/                          # Legacy admin redirect shims
│
├── config/
│   └── database.php                # DB connection credentials
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
│       ├── Language.php            # EN/KM bilingual dictionary
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
- PHP **8.2+** (strict types, named arguments, union types)
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

**5. Login**

Your admin credentials are set up by the system automatically. Contact your system administrator for your staff login credentials.

> ⚠️ **Note:** Default credentials are configured during the initial database setup. Change your password via a database client (phpMyAdmin) after first login.

---

## Configuration

### `config/database.php`
```php
<?php
return [
    'host'     => 'localhost',
    'port'     => 3306,
    'dbname'   => 'news_platform',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];
```

### Upload Directory
Ensure the web server has write permissions on:
```
public/uploads/
```
On XAMPP, this is handled automatically. On production Linux:
```bash
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/
```

---

## Article Template Blueprints

The CMS supports **three distinct editorial layout blueprints**, selected per article by the editor:

### Blueprint 1 — Standard (`template_type = 'standard'`)
**File:** `templates/views/article-standard.php`

- Classic **2-column layout** with article body on the left and a sidebar on the right
- Sidebar includes Most Read Stories, Daily Digest subscription CTA, and Explore Topics
- Featured image displayed below the author metadata bar
- Suitable for general news, technology, economic reports

### Blueprint 2 — Investigative (`template_type = 'investigative'`)
**File:** `templates/views/article-investigative.php`

- **Single-column deep-read format** with a dark hero banner header section
- Article title and standfirst displayed in large white text over the dark hero
- Drop-cap styled article body for long-form reading
- Pull-quote callout box ("This investigation relies on verified primary source documents...")
- Reader feed subscription CTA at the bottom
- Suitable for special reports, investigations, data journalism

### Blueprint 3 — Opinion (`template_type = 'opinion'`)
**File:** `templates/views/article-opinion.php`

- **Columnist profile-focused layout** with warm sepia aesthetic
- Author spotlight card at the top showing avatar, bio, and columnist badge
- Article title and standfirst displayed in centered italic serif font
- Stylized pull-quote in distinctive opinion style
- Columnist footer profile callout with column feed subscription button
- Suitable for editorials, opinion columns, commentary pieces

All three blueprints support:
- 📹 **Video embed player** (YouTube/MP4)
- 🖼️ **Interactive photo gallery carousel** (Bootstrap 5, swipeable)
- 🎧 **Audio/Podcast player** (Spotify/SoundCloud iframe or native HTML5 audio)
- ✅ **Verified citation source card** at the bottom
- 🔗 **Related articles** from the same category

---

## Admin Panel (CMA)

### Dashboard (`/public/admin/dashboard.php`)

- **4 metric cards** — Total Articles, Published Live, Drafts Pending, Active Subscribers
- **Chart.js analytics** — Template blueprint pie chart ratio + article traffic bar chart
- **Article repository table** — Searchable, filterable by status tabs (All / Published / Draft / Archived)
- **Recent subscribers list** — Latest 6 feed registrations

### Article Editor (`/public/admin/article-create.php`, `/public/admin/article-edit.php`)

The article editor includes two panels:

**Left Panel (main content):**
- Article Title (large input)
- URL Slug (auto-generated from title, editable + regenerate button)
- Summary / Standfirst (textarea)
- **Quill WYSIWYG Rich Text Editor** (full formatting toolbar, synced to hidden `<textarea>` on submit)
- Multi-Media & Interactive Embeds card:
  - Video Embed URL (YouTube/MP4)
  - Audio Podcast Embed URL (Spotify/SoundCloud/MP3)
  - Photo Gallery URLs (one per line → renders carousel)
  - Verified Source Name + URL

**Right Panel (publishing controls):**
- Post Status (Draft / Published / Archived)
- Template Blueprint selector with live interactive preview box
- Category selector
- Author selector
- Breaking News toggle
- Featured Cover Image upload (JPG/PNG/WEBP, max 5MB)

### Staff Roles

| Role | Permissions |
|------|-------------|
| `admin` | Full access — create, edit, delete articles, view all dashboard |
| `editor` | Create and edit articles, no delete |
| `reporter` | Create articles only |

---

## Security Features

The `Auth` class (`src/Core/Auth.php`) implements a comprehensive security stack:

| Feature | Implementation |
|---------|----------------|
| **Password Hashing** | PHP `password_hash()` with `PASSWORD_BCRYPT` algorithm |
| **Session Security** | `HttpOnly`, `SameSite=Lax` session cookies |
| **Session Fingerprinting** | SHA-256 hash of IP + User-Agent, validated on every request |
| **Session Hijack Protection** | Automatic session destruction + regeneration on fingerprint mismatch |
| **Session ID Regeneration** | `session_regenerate_id(true)` on successful login (privilege elevation) |
| **CSRF Protection** | `bin2hex(random_bytes(32))` token stored in session, validated with `hash_equals()` on all POST forms |
| **Brute Force Rate Limiting** | Max 5 failed login attempts per IP per 15 minutes; recorded in `login_attempts` table |
| **Role-Based Access Control (RBAC)** | `requireAuth(array $roles)` guard on all admin controller methods |
| **Input Validation** | All POST data validated and sanitized before DB operations |
| **Parameterized Queries** | All DB queries use PDO prepared statements — zero raw SQL string interpolation |
| **File Upload Validation** | Extension allowlist + server-side MIME type check via PHP `finfo` |
| **XSS Output Escaping** | `e()` helper wraps all user-generated output with `htmlspecialchars()` |

---

## Bilingual Support (EN / KM)

The `Language` class (`src/Core/Language.php`) provides a full bilingual dictionary system.

### How It Works
1. Language is detected from `?lang=en` or `?lang=km` GET parameter → stored in `$_SESSION['lang']` and a 30-day cookie `app_lang`
2. All UI strings are accessed via the `__('key')` helper function
3. Category names are resolved via `cat_name($name)` which handles both EN→KM and KM→EN translation regardless of how names are stored in the database

### Switching Language
```
// Switch to Khmer:
http://localhost/News-platefrom/public/?lang=km

// Switch to English:
http://localhost/News-platefrom/public/?lang=en
```

The language switcher dropdown in the navigation header generates these URLs automatically using `lang_url('en')` / `lang_url('km')` which preserves all current query parameters.

### Adding New Translation Keys
In `src/Core/Language.php`, add the key to **both** language arrays:
```php
'en' => [
    'my_new_key' => 'My English text',
    // ...
],
'km' => [
    'my_new_key' => 'ខ្លឹមសារខ្មែររបស់ខ្ញុំ',
    // ...
],
```
Then use in templates: `<?= __('my_new_key') ?>`

---

## Media & Embeds

### Featured Image Upload
- Accepted formats: `JPG`, `JPEG`, `PNG`, `WEBP`
- Max size: **5MB**
- Server-side validation: file extension + MIME type via PHP `finfo`
- Stored in: `public/uploads/cover_YYYYMMDD_HHmmss_randomhex.ext`

### Video Embed
Paste any YouTube embed URL or direct MP4 link into the Video Embed URL field:
```
https://www.youtube.com/embed/VIDEO_ID
```
Renders as a responsive 16:9 iframe player in the article.

### Audio / Podcast Embed
The audio player intelligently detects the URL type:

| URL Type | Render Method |
|----------|---------------|
| `spotify.com`, `soundcloud.com`, `anchor.fm` | Platform-native embed `<iframe>` (152px height) |
| Any other URL (MP3, etc.) | HTML5 native `<audio controls>` player |

### Photo Gallery Carousel
Enter one image URL per line in the Photo Gallery textarea:
```
https://example.com/image1.jpg
https://example.com/image2.png
https://example.com/image3.webp
```
Renders as a Bootstrap 5 interactive carousel with indicators, prev/next controls, and graceful `onerror` fallback if an image fails to load.

---

## Core Classes Reference

### `Database` — `src/Core/Database.php`
PDO Singleton. Auto-creates the database, runs `schema.sql` on first boot, and handles migration of new columns.

```php
$db = Database::getInstance();
$articles = $db->fetchAll("SELECT * FROM articles WHERE status = :s", ['s' => 'published']);
$article  = $db->fetch("SELECT * FROM articles WHERE slug = :slug", ['slug' => $slug]);
$count    = $db->fetchColumn("SELECT COUNT(*) FROM articles");
$db->execute("UPDATE articles SET views_count = views_count + 1 WHERE id = :id", ['id' => 1]);
```

### `Auth` — `src/Core/Auth.php`
Session management, RBAC, CSRF, rate limiting.

```php
Auth::startSession();                    // Start secure session
Auth::login($username, $password);       // Authenticate staff
Auth::logout();                          // Destroy session
Auth::check();                           // Is logged in?
Auth::user();                            // Get current user array
Auth::requireAuth(['admin', 'editor']);  // Guard + redirect if unauthorized
Auth::generateCsrfToken();              // Generate CSRF token for forms
Auth::verifyCsrfToken($postToken);      // Verify submitted CSRF token
```

### `Language` — `src/Core/Language.php`
Bilingual string resolution.

```php
Language::init();          // Initialize language from session/cookie/GET
Language::current();       // Returns 'en' or 'km'
Language::get('key');      // Get translated string for current language
Language::catName($name);  // Translate category name (EN↔KM bidirectional)
```

### `TemplateEngine` — `src/Core/TemplateEngine.php`
Layout rendering engine.

```php
$engine = new TemplateEngine();
$engine->renderPage('views/home.php', ['articles' => $articles], 'public');
TemplateEngine::e($value);                        // HTML escape
TemplateEngine::formatDate($datetime);            // Format date for display
TemplateEngine::timeAgo($datetime);               // "2 hours ago" relative time
```

### Global Helper Functions — `src/Core/helpers.php`

```php
e($value)           // HTML escape output (XSS protection)
__('key')           // Translate UI string
cat_name($name)     // Translate category name (EN↔KM)
url('path/to/page') // Generate correct absolute URL regardless of server subdirectory
lang_url('km')      // Language switcher URL preserving current query params
```

---

## URL Routing

There is no framework router. Routes are handled by direct PHP file includes mapped to controller method calls:

| Public URL | File | Controller Method |
|------------|------|-------------------|
| `/public/` or `/public/index.php` | `public/index.php` | `PublicController::home()` |
| `/public/article.php?slug=...` | `public/article.php` | `PublicController::article()` |
| `/public/subscribe.php` | `public/subscribe.php` | `PublicController::subscribe()` |
| `/public/admin/login.php` | `public/admin/login.php` | `AdminController::login()` |
| `/public/admin/dashboard.php` | `public/admin/dashboard.php` | `AdminController::dashboard()` |
| `/public/admin/article-create.php` | `public/admin/article-create.php` | `AdminController::createArticleForm()` |
| `/public/admin/article-edit.php?id=...` | `public/admin/article-edit.php` | `AdminController::editArticleForm()` |
| `/public/admin/actions/save-article.php` | `public/admin/actions/save-article.php` | `AdminController::saveArticle()` |
| `/public/admin/actions/delete-article.php` | `public/admin/actions/delete-article.php` | `AdminController::deleteArticle()` |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend Language** | PHP 8.2+ (strict types, OOP, PDO) |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ |
| **CSS Framework** | Bootstrap 5.3.2 |
| **Icons** | Bootstrap Icons 1.11.1 |
| **Rich Text Editor** | Quill.js 2.0.2 (Snow theme) |
| **Charts** | Chart.js 4.x (Dashboard analytics) |
| **Fonts** | Google Fonts — Inter, Lora, Noto Sans Khmer, Noto Serif Khmer |
| **JavaScript** | Vanilla JS (ES6+) — no jQuery dependency |
| **Local Dev** | XAMPP / WAMP / Laragon |
| **Architecture Pattern** | Decoupled CMA/CDA, MVC-inspired, Singleton DB, Repository pattern |

---

## Contributing

This project follows strict PHP coding standards:

- All PHP files use `declare(strict_types=1)`
- Use standard curly brace `{ }` control flow syntax — **no** `endif;`/`endforeach;` alternative syntax
- All DB queries must use PDO prepared statements
- All output must be escaped with `e()` unless rendering trusted stored HTML (e.g. Quill content)
- All new UI strings must be added to **both** `en` and `km` language dictionaries in `Language.php`
- File uploads must validate both extension and MIME type server-side

---

## License

This project is built for educational and independent journalism purposes.  
© 2026 NewsPlatform CMS. All rights reserved.
#   N e w s _ P l a t e f o r m 1  
 