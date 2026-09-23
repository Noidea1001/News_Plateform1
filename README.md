# NewsPlatform CMS

**A production-grade Decoupled Content Management System (CMS) & Content Delivery Application (CDA) for independent journalism, digital newsrooms, and editorial publishing.**

Built with **PHP 8.2+**, MySQL, a clean decoupled architecture, dynamic multi-template layout blueprints, MS Word-style media text wrapping, an interactive **Saved Reading List Engine** with instant dual-language switching, a **Quick Article Preview Modal**, and a CNA/BBC-style flat light editorial design system.

---

## 📋 Table of Contents

- [✨ Key System Features](#-key-system-features)
- [💻 Prerequisites & Technical Stack](#-prerequisites--technical-stack)
- [🚀 Quick Installation & Setup Guide](#-quick-installation--setup-guide)
- [🔑 Admin Login Credentials](#-admin-login-credentials)
- [⚙️ System Architecture & How It Works](#️-system-architecture--how-it-works)
  - [1. Decoupled Architecture (CMA vs CDA)](#1-decoupled-architecture-cma-vs-cda)
  - [2. Interactive Saved Reading List & Offcanvas Drawer](#2-interactive-saved-reading-list--offcanvas-drawer)
  - [3. Quick Article Preview Modal](#3-quick-article-preview-modal)
  - [4. Centralized Single-Source-of-Truth Language Engine](#4-centralized-single-source-of-truth-language-engine)
  - [5. CNA/BBC-Style Flat Light Aesthetic](#5-cnabbc-style-flat-light-aesthetic)
  - [6. Mobile Readability & High-Legibility Typography](#6-mobile-readability--high-legibility-typography)
  - [7. 3 Multi-Template Layout Blueprints](#7-3-multi-template-layout-blueprints)
  - [8. MS Word-Style Media Shortcodes & Text Wrapping](#8-ms-word-style-media-shortcodes--text-wrapping)
  - [9. Scroll Reading Progress Bar](#9-scroll-reading-progress-bar)
- [📁 Project Directory Structure](#-project-directory-structure)
- [🗄️ Database Schema & Auto-Migration](#-database-schema--auto-migration)
- [❓ Troubleshooting & FAQs](#-troubleshooting--faqs)

---

## ✨ Key System Features

- 📰 **CNA / BBC-Style Flat Editorial Aesthetic**: Clean, high-contrast light mode with `#c8102e` red accents, `#0f172a` navy header text, and flat sharp borders (`#e5e7eb`) with zero elevation shadows.
- 🔖 **Saved Reading List Offcanvas Modal**: Persisted via browser `localStorage` with a real-time header count badge `#savedCountBadge` and single-click removal.
- 👁️ **Quick Article Preview Modal**: Instant pop-up preview allowing readers to preview summaries, metadata, reading times, view counts, and bookmark articles without leaving the feed.
- 🌐 **Single Source of Truth Bilingual System (Khmer & English)**: Centralized translation maps in `src/Core/helpers.php` passed to JavaScript via `json_encode(get_translation_maps())`. Language switches immediately re-render saved articles in the newly active language without clearing `localStorage`.
- 📱 **Mobile Typography Overhaul**: Long-form mobile font size enhanced to `1.05rem` (16.8px) with `1.75` line height for comfortable mobile reading.
- 📐 **3 Article Layout Blueprints**:
  - `Standard`: 2-column layout with interactive sticky sidebar and author spotlight.
  - `Investigative`: 1-column deep-read layout with dark hero banner and verified primary citation cards.
  - `Opinion`: Columnist avatar spotlight header card with pull quotes and author commentary.
- 🖼️ **MS Word-Style Media Shortcodes & Text Wrapping**: Floating image and video alignment tags (`[image:1:left]`, `[image:1:right]`, `[video:1:full]`) with automated text wrapping.
- 📊 **Scroll Reading Progress Bar**: Real-time top progress bar `#readingProgressBar` that fills dynamically as the reader scrolls down an article page.

---

## 💻 Prerequisites & Technical Stack

| Technology | Minimum Requirement | Recommended |
|------------|---------------------|-------------|
| **Web Server** | Apache 2.4+ (`mod_rewrite` enabled) | XAMPP / WAMP / Laragon |
| **PHP Engine** | PHP 8.0+ | PHP 8.2 or 8.3 |
| **PHP Extensions** | `pdo`, `pdo_mysql`, `gd`, `json`, `session` | Included with XAMPP |
| **Database** | MySQL 5.7+ or MariaDB 10.3+ | MySQL 8.0+ |
| **Frontend Dependencies** | Bootstrap 5.3.2, Bootstrap Icons | CDN Loaded |

---

## 🚀 Quick Installation & Setup Guide

### Step 1: Clone / Copy to Local Web Server Root
Place the project directory inside your local Apache `htdocs` root:
- **XAMPP**: `C:\xampp\htdocs\News-platefrom-1\`
- **WAMP**: `C:\wamp64\www\News-platefrom-1\`

### Step 2: Configure Database Credentials
Edit `config/database.php` to set your local MySQL connection settings:

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

### Step 3: Automatic Database Schema Migration & Seeding
You **do not need to manually import SQL files!**  
On your first browser visit, `Database::autoInitializeSchema()` automatically:
1. Creates the `news_platform` database.
2. Creates all required database tables (`users`, `categories`, `articles`, `subscribers`, `login_attempts`).
3. Seeds sample news articles and staff user accounts.

*(Optional data re-seeding: Run `http://localhost/News-platefrom-1/seed_news.php` in your browser to re-seed 12 real informative news stories).*

### Step 4: Access the Application

- 🌐 **Public Reader Homepage (CDA)**:  
  `http://localhost/News-platefrom-1/public/` *(or `http://localhost/News-platefrom-1/`)*

- 🔐 **Admin Login Portal (CMA)**:  
  `http://localhost/News-platefrom-1/public/admin/login.php`

---

## 🔑 Admin Login Credentials

Use the pre-configured staff accounts to log into the backend Content Management Application (`/admin/login.php`):

| Role | Username | Password | Access Level |
|------|----------|----------|--------------|
| **Administrator** | `admin` | `admin123` | Full access (articles, categories, user management, subscribers) |
| **Senior Editor** | `eleanor_vane` | `admin123` | Article drafting, editing, and publishing across all blueprints |

---

## ⚙️ System Architecture & How It Works

### 1. Decoupled Architecture (CMA vs CDA)
- **CMA (Content Management Application)**: Backend admin suite (`/admin/*`) for journalists and editors to manage posts, select layout blueprints, upload media, and monitor traffic metrics.
- **CDA (Content Delivery Application)**: Public reader frontend (`/public/*`) optimized for speed, responsive legibility, accessibility, and high contrast.

---

### 2. Interactive Saved Reading List & Offcanvas Drawer
- Clicking the bookmark icon `[🔖]` on any article card toggles its saved state in `localStorage`.
- The navbar displays an icon-only bookmark button with a live badge counter `#savedCountBadge`.
- Opening the offcanvas drawer lists all bookmarked articles with thumbnail, category, title, reading time, direct read link (`អានអត្ថបទ →` / `Read Story →`), and individual remove buttons `[✖]`.

---

### 3. Quick Article Preview Modal
- Clicking the quick view icon `[👁️]` triggers a Bootstrap modal containing:
  - High-res cover image
  - Category pill & reading time
  - Article title & full summary
  - Author name, publication date, and total page views
  - One-click bookmark toggle button & direct link to full article page

---

### 4. Centralized Single-Source-of-Truth Language Engine
All translations are managed centrally in `src/Core/helpers.php` via `get_translation_maps()` and `languages/lang_kh.php` / `languages/lang_en.php`:

```php
// In src/Core/helpers.php
function get_translation_maps(): array {
    return [
        'categories' => ['Technology & AI' => 'បច្ចេកវិទ្យា & AI', ...],
        'titles'     => ['ការអភិវឌ្ឍប្រព័ន្ធ AI...' => 'Cambodia Digital Economy...', ...]
    ];
}
```

In `footer.php`, JavaScript inherits `get_translation_maps()` directly via `json_encode()`:
- **Zero hardcoded JS dictionaries**: When adding new articles or categories in CMS, you **never** need to touch `footer.php`!
- **Automatic Parenthetical Detection**: Titles formatted as `KhmerTitle (EnglishTitle)` are automatically parsed via Unicode regex `[\x{1780}-\x{17FF}]`.

---

### 5. CNA/BBC-Style Flat Light Aesthetic
- **Flat Borders**: Strict `border: 1px solid #e5e7eb` styling with **zero elevation box-shadows**.
- **Red Accent Lines**: Left border accents (`border-left: 3px solid #c8102e`).
- **Clean Corner Clipping**: All modals, cards, and offcanvas drawers use `overflow: hidden !important` and `border-top-left-radius: inherit !important` so red left borders never overflow modal corners.
- **Explicit Button Spacing**: Quick View `[👁️]` and Bookmark `[🔖]` buttons maintain explicit `margin-right: 0.35rem` / `margin-left: 0.35rem` spacing.

---

### 6. Mobile Readability & High-Legibility Typography
- **Mobile Body Text**: Increased to `1.05rem` (16.8px) with `1.75` line height.
- **Metadata Text**: High-contrast `0.8rem` (12.8px) text for mobile viewports.
- **Interactive Font Size Toolbar**: Readers can scale article text using `A-`, `100%`, and `A+` controls (saved across browser sessions).

---

### 7. 3 Multi-Template Layout Blueprints

1. **Standard Blueprint (`standard`)**:
   - Classic 2-column editorial layout with main content column, sticky right sidebar, author metadata bar, photo gallery carousel, video/audio embeds, citation box, and related articles grid.

2. **Investigative Blueprint (`investigative`)**:
   - Single-column immersive deep-read layout featuring a dark hero header banner, verified primary citation cards, audio report player, and pull quote callout boxes.

3. **Opinion Blueprint (`opinion`)**:
   - Columnist profile spotlight header card with columnist bio, avatar, stylized pull quotes with red top/bottom borders, audio commentary player, and author bio callout footer.

---

### 8. MS Word-Style Media Shortcodes & Text Wrapping
Authors can align images and videos between paragraphs using simple shortcode tags:

| Shortcode | Alignment / Formatting Effect |
|-----------|--------------------------------|
| `[image:1]` / `[image:1:center]` | Centered photo block |
| `[image:1:left]` | **Floated Left with text wrapping around right side** |
| `[image:1:right]` | **Floated Right with text wrapping around left side** |
| `[image:1:full]` | Full-width photo block |
| `[video:1:left]` | **Floated Left video player with text wrapping** |
| `[video:1:right]` | **Floated Right video player with text wrapping** |

---

### 9. Scroll Reading Progress Bar
A fixed top red progress bar `#readingProgressBar` tracks the reader's scroll position on article pages:

```javascript
window.addEventListener('scroll', function () {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    progressBar.style.width = scrolled + '%';
});
```

---

## 📁 Project Directory Structure

```
News-platefrom-1/
│
├── admin/                          # Backend CMA Entry Points
│   ├── dashboard.php               # Admin Editorial Control Panel
│   ├── article-create.php          # Draft New Article Form
│   ├── article-edit.php            # Edit Article Form
│   ├── users.php                   # Staff Account Management
│   ├── subscribers.php             # Reader Feed Subscribers List
│   └── actions/                    # POST Action Handlers (save, delete, upload)
│
├── config/
│   └── database.php                # Database Credentials Configuration
│
├── languages/                      # Translation Dictionaries
│   ├── common.php                  # Session Language Controller
│   ├── lang_en.php                 # English Translation Dictionary
│   └── lang_kh.php                 # Khmer Translation Dictionary
│
├── public/                         # Public CDA Reader Root
│   ├── index.php                   # Homepage Feed Stream
│   ├── article.php                 # Single Article View Controller
│   ├── subscribe.php               # AJAX Subscriber Endpoint
│   └── assets/
│       └── css/style.css           # CNA Light Editorial CSS System
│
├── src/
│   ├── Controllers/                # Admin & Public Business Controllers
│   └── Core/
│       ├── Auth.php                # Authentication, CSRF, Rate Limiting
│       ├── Database.php            # PDO Singleton & Auto Schema Migration
│       ├── Sanitizer.php           # XSS Cleaner & Media Shortcode Parser
│       ├── TemplateEngine.php      # View Layout & Date Engine
│       └── helpers.php             # Centralized Helper Functions & Translation Maps
│
├── templates/                      # Views & Reusable Components
│   ├── layouts/
│   │   ├── header-public.php       # Public Header & Sticky Navbar
│   │   ├── header-admin.php        # Admin Navigation Header
│   │   └── footer.php              # Footer & JS Interactivity Engine
│   ├── views/
│   │   ├── home.php                # Homepage Feed Layout
│   │   ├── article-standard.php    # Standard Layout Blueprint
│   │   ├── article-investigative.php # Investigative Layout Blueprint
│   │   └── article-opinion.php     # Opinion Layout Blueprint
│   └── components/
│       ├── quick-view-modal.php    # Quick Preview Modal Component
│       ├── saved-articles-modal.php# Saved Reading List Offcanvas Drawer
│       ├── reading-toolbar.php     # Reader Font Size Scale Widget (A-/100%/A+)
│       ├── citation-box.php        # Verified Primary Source Citation Card
│       └── sidebar.php             # Public Sidebar (Most Read, Digest CTA)
│
├── seed_news.php                   # Real Data Seeder Script (12 Articles)
├── schema.sql                      # Production MySQL Database Schema
└── README.md                       # Comprehensive Project Documentation
```

---

## 🗄️ Database Schema & Auto-Migration

The application manages 5 core relational tables:

1. **`users`**: Staff accounts (`admin`, `editor`, `reporter`) with bcrypt password hashes.
2. **`categories`**: Article categories (`technology-ai`, `global-politics`, `climate-science`, `economy-markets`, etc.).
3. **`articles`**: Article repository storing headlines, HTML content, template type (`standard`, `investigative`, `opinion`), featured images, video/audio embeds, drop-cap toggle, and views counter.
4. **`subscribers`**: Reader email subscription records with category preferences.
5. **`login_attempts`**: Security logging for brute-force rate-limiting on login.

---

## ❓ Troubleshooting & FAQs

### Q: Why do saved articles switch language instantly when toggling Khmer / English?
- **A**: `localStorage` saves the article ID along with pre-calculated `title_kh`, `title_en`, `category_kh`, and `category_en` fields. JavaScript directly renders the field matching `$_SESSION['lang']`, ensuring instant client-side language switching without page reloads or clearing data.

### Q: How do I float an image or video to the left/right of text?
- **A**: In the editor, click the **Left** or **Right** button under the media reference section (e.g. `[image:1:left]` or `[video:1:right]`). Text paragraphs placed after the tag will wrap cleanly around the media element.

### Q: Why were modal corners leaking white background pixels previously?
- **A**: Fixed by enforcing `overflow: hidden !important` on `.modal-content`, `.offcanvas`, and `.card` containers, while setting `border-top-left-radius: inherit !important` on header elements.

---

## 📄 License

Built for professional editorial newsrooms and independent journalism.  
© 2026 **NewsPlatform CMS**. All rights reserved.