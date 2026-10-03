# 🚀 Deployment Guide: NewsPlatform CMS on Render.com

This guide provides step-by-step instructions to deploy **NewsPlatform CMS** to **[Render.com](https://render.com/)** using **Docker**, **Managed MySQL**, **Persistent Storage Disks** for image uploads, and **Web Push VAPID** environment variables for background push notifications.

---

## 📋 Table of Contents
1. [Prerequisites](#1-prerequisites)
2. [Step 1: Push Code to GitHub](#step-1-push-code-to-github)
3. [Step 2: Create a MySQL Database on Render](#step-2-create-a-mysql-database-on-render)
4. [Step 3: Deploy the Web Service on Render](#step-3-deploy-the-web-service-on-render)
5. [Step 4: Configure Environment Variables](#step-4-configure-environment-variables)
6. [Step 5: Attach Persistent Disk for Media Uploads](#step-5-attach-persistent-disk-for-media-uploads)
7. [Step 6: Verify Database Initialization & Admin Login](#step-6-verify-database-initialization--admin-login)
8. [Step 7: Enable Web Push Notifications](#step-7-enable-web-push-notifications)
9. [Troubleshooting & FAQs](#-troubleshooting--faqs)

---

## 1. Prerequisites

- A **GitHub Account** ([github.com](https://github.com/))
- A **Render Account** ([render.com](https://render.com/))
- **Git** installed on your local computer

---

## Step 1: Push Code to GitHub

1. Initialize Git in your project folder (if not already done):
   ```bash
   cd c:\xampp\htdocs\News-platform-1
   git init
   ```
2. Stage and commit all files:
   ```bash
   git add .
   git commit -m "Prepare NewsPlatform CMS for Render deployment"
   ```
3. Create a new repository on [GitHub](https://github.com/new).
4. Link your local repo and push:
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/News-platform.git
   git branch -M main
   git push -u origin main
   ```

---

## Step 2: Create a MySQL Database on Render

1. Log into your **[Render Dashboard](https://dashboard.render.com/)**.
2. Click **New +** → Select **PostgreSQL / MySQL** (or **New Web Service / Database**).
   *(Note: You can use Render's MySQL database or a free external MySQL provider like [Aiven MySQL](https://aiven.io/) or [Railway MySQL](https://railway.app/)).*
3. Enter database details:
   - **Name**: `news-platform-db`
   - **Database**: `news_platform`
   - **User**: `news_user`
4. Copy your database connection credentials:
   - **Internal Database Host** / **Hostname**: `DB_HOST`
   - **Port**: `3306`
   - **Username**: `DB_USER`
   - **Password**: `DB_PASS`
   - **Database Name**: `news_platform`

---

## Step 3: Deploy the Web Service on Render

### Option A: Automatic Blueprint Deployment (Recommended)
1. On Render Dashboard, click **New +** → Select **Blueprints**.
2. Connect your GitHub repository `News-platform`.
3. Render will read `render.yaml` automatically and prompt you for missing environment variables.
4. Click **Apply**.

### Option B: Manual Web Service Setup
1. Click **New +** → Select **Web Service**.
2. Connect your GitHub repository `News-platform`.
3. Configure the service:
   - **Name**: `news-platform-cms`
   - **Region**: Choose closest to your readers (e.g. `Singapore`).
   - **Branch**: `main`
   - **Runtime**: **Docker**
   - **Dockerfile Path**: `./Dockerfile`

---

## Step 4: Configure Environment Variables

In your Web Service page on Render, navigate to **Environment** → **Add Environment Variable**:

| Key | Value | Description |
|---|---|---|
| `DB_HOST` | *(your Render DB hostname or IP)* | Database server host |
| `DB_PORT` | `3306` | MySQL port |
| `DB_NAME` | `news_platform` | Database name |
| `DB_USER` | *(your DB username)* | Database user |
| `DB_PASS` | *(your DB password)* | Database password |
| `APP_ENV` | `production` | Production environment mode |
| `VAPID_PUBLIC_KEY` | *(your VAPID public key)* | Web Push VAPID public key (base64url EC point) |
| `VAPID_PRIVATE_KEY` | *(your VAPID private key)* | Web Push VAPID private key (base64url scalar) |
| `VAPID_SUBJECT` | `mailto:your@email.com` | Contact email for push service identification |

> **Getting your VAPID keys:** After first running the app locally, keys are auto-generated and saved to `config/vapid.local.php`. Copy the `public_key`, `private_key`, and `subject` values from that file into Render's environment variables. Alternatively, the keys in `config/vapid.php` contain pre-configured defaults that work out of the box.

Click **Save Changes**. Render will automatically trigger a re-deploy.

---

## Step 5: Attach Persistent Disk for Media Uploads

To ensure uploaded article photos and media inside `/public/uploads/` are never lost when the server updates:

1. Go to your Render Web Service dashboard → Click **Disks** in the left menu.
2. Click **Add Disk**:
   - **Name**: `uploads-disk`
   - **Mount Path**: `/var/www/html/public/uploads`
   - **Size**: `1 GB` (or larger)
3. Click **Save Changes**.

---

## Step 6: Verify Database Initialization & Admin Login

Once deployment is complete (showing a green `Live` status):

1. **Visit your live URL**:
   `https://your-app.onrender.com/public/`

   > ℹ️ *The first visit automatically initializes all MySQL tables (`articles`, `users`, `categories`, `readers`, `comments`, `notifications`, `subscribers`, `push_subscriptions`, `login_attempts`) and seeds 12 full Khmer news posts!*

2. **Access Admin Editorial Control Panel**:
   `https://your-app.onrender.com/admin/login.php`

3. **Login with Default Credentials**:
   - **Username**: `admin`
   - **Password**: `admin123`

---

## Step 7: Enable Web Push Notifications

After deployment, set up background push notifications:

1. **Initialize the push subscriptions table** (if auto-init hasn't run):
   Visit `https://your-app.onrender.com/public/setup-push.php?secret=setup2024`
   This verifies VAPID keys are loaded and confirms the `push_subscriptions` table exists.

2. **Subscribe users**: Open the live site, click the 🔔 **Enable Notifications** banner, and allow notification permission. Each device's push endpoint is stored in the database.

3. **Test push delivery**: Add `&sendtest=1` to the setup URL:
   `https://your-app.onrender.com/public/setup-push.php?secret=setup2024&sendtest=1`

4. **Clean up**: Delete `public/setup-push.php` from your repo after testing.

5. **Push fires automatically** whenever an admin publishes or updates an article to `published` status.

> **HTTPS Requirement**: Web Push requires HTTPS. Render provides HTTPS automatically on `*.onrender.com` domains. iOS Safari background push (app fully closed) requires the PWA to be installed from an HTTPS domain.

---

## ❓ Troubleshooting & FAQs

### Q: Database Connection Error on first visit?
- Ensure `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` match your Render or external MySQL database settings.
- Check if your database allows connections from external IPs or internal Render web service hostnames.

### Q: Uploaded images disappear after a few days?
- Make sure you attached the **Persistent Disk** mounted at `/var/www/html/public/uploads` as shown in Step 5.

### Q: Layout looks broken or images appear huge on deployment?
- This happens when `style.css` fails to load due to `public/` path mismatch between local Apache (`htdocs`) and production Docker (`/public` root).
- Ensure asset references use `url('assets/css/style.css')` which works automatically in both subfolder and root domain setups via Apache mod_rewrite.

### Q: How do I change admin password for production?
- Log into admin dashboard, navigate to **Staff Users** (`/admin/users.php`), or update password hash directly in phpMyAdmin / database client.

### Q: Push notifications are not sending on production?
- Confirm `VAPID_PUBLIC_KEY`, `VAPID_PRIVATE_KEY`, and `VAPID_SUBJECT` are set in Render Environment Variables.
- Ensure at least one browser has subscribed (visited the site and clicked Enable Notifications).
- Check PHP error logs in the Render **Logs** tab for `[WebPush]` error entries.
- VAPID keys must match: the same keys used when browsers subscribed must be used when the server sends pushes. If you change VAPID keys, all existing subscriptions become invalid and users must re-subscribe.

### Q: Raw JSON appeared in the installed PWA when commenting or logging in?
- This was fixed in v3.0. Force the PWA to update: open the app, clear app data and reinstall, or wait for the new Service Worker (`newsplatform-v3`) to activate.

---

© 2026 **NewsPlatform CMS** - Render.com Deployment Package
