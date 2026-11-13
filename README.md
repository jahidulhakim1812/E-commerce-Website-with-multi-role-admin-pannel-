# DryNutri E-commerce Suite (PHP + MySQL)

Healthy & natural themed online store with Admin, Manager and Customer panels.

## Install (XAMPP / any PHP 8+ hosting)
1. Copy the `drynutri` folder to `htdocs` (or your hosting `public_html`).
2. Run `composer install` from the project folder to install PHP dependencies (required for browser push notifications).
3. Open `http://localhost/drynutri/install.php`, enter MySQL details + your admin email/password. Tables and demo data are created automatically
   (alternative: import `database.sql` in phpMyAdmin and create `config.local.php` with DB_HOST, DB_NAME, DB_USER, DB_PASS defines).
4. **Delete `install.php`** afterwards. Make sure the `uploads/` folder is writable.
5. Log in at `/login.php` (admin goes to `/admin/`). Default demo admin before installer change: admin@drynutri.com.

## What the admin can change without touching code
Products (EN/BN, gallery, flags, cost price), categories & sub-categories, header menu (pick any category/sub-category, dropdowns), footer links,
homepage sections (order, titles, product sources), offer banner image/button in the middle of the home page, hero slides, pages, testimonials, coupons, reviews,
orders (status → customer tracking + email), customers, managers with per-section permissions, expenses / profit & loss, reports (CSV),
visitor analytics (IP, pages, device, date/IP filters), and Settings: logo, colours, contact, floating contact icon, shipping/payment,
SMTP setup presets for Gmail and hosting webmail, test-mail button, and editable order-email template.

The Admin → Sales & Insight → Export Center downloads customer, product, subscriber, order, sales, finance, and visitor data as CSV or Word-compatible `.doc` files. Exports follow the signed-in manager's section permissions and include a selectable date range. Customer, subscriber, and visitor exports can contain personal information; handle downloaded files securely.

## Notes
- Language: English default, Bengali toggle (top bar / side drawer). Light/Dark toggle on website and all panels.
- Auto emails need SMTP enabled in Admin → Settings → Email. WhatsApp auto-messages need a paid WhatsApp Business API (not included); the floating WhatsApp button and "Order on WhatsApp" are included.
- SSL Wireless order-confirmation SMS can be enabled and configured in Admin → Settings → SMS. Enter the endpoint, account username/password, sender ID, then send a test SMS before enabling live checkout messages.
- Anonymous browser push: in Admin → Settings → Browser Push, generate VAPID keys and enable push. Visitors can opt in from the 🔔 bell without creating an account or sharing contact details. Browsers require an explicit permission choice, and production sites must use HTTPS. New products, offers, and published admin notifications are sent to opted-in browsers.
- PHP/Apache OpenSSL must have a valid `OPENSSL_CONF` at server startup for VAPID key generation and push signing. After correcting the OpenSSL config path, restart Apache.
- Customers can reset forgotten passwords from the sign-in page. Configure the canonical website URL in Admin → Settings → General and SMTP in Admin → Settings → Email. Reset links expire after one hour and can be used once.
- Visitor IP: uses CF-Connecting-IP / X-Forwarded-For / REMOTE_ADDR. Staff visits and bots are not counted.
- Revenue = confirmed/processing/shipped/delivered orders. Profit uses each product's cost price at the time of order.
- Online card/bKash/Nagad gateways are manual (customer pays and writes the transaction ID); integrate a gateway SDK in checkout.php if needed.

## What's new (dried-fruit edition)
- **Theme:** warm mango / orange / banana colours, dried-fruit demo products & categories. Change colours any time in Admin → Settings → General (primary + accent).
- **Notifications:** every visitor gets a 🔔 bell (new products, offers, your custom messages) plus optional browser alerts. Subscribers & registered customers can also get e-mails. New products and new coupons are announced automatically; send your own from Admin → Notifications.
- **Home page:** 8 Fan Favorite cards (rest via "View All"), auto-sliding Shop-by-Category, Best Selling slider, Recent Uploads, All Products, and an offer banner managed from Admin → Offer Banners.
- **Buttons:** clean, shadow-free.
- Menu opens as a left drawer; floating cart opens a right-side cart drawer.

## Database files (inside /database)
- `drynutri_full.sql` - import in phpMyAdmin for a ready database with demo data (creates DB `drynutri`).
- `database_starter.sql` - clean starter data only (same as root `database.sql`, used by install.php).
- `update_to_latest.sql` - only for upgrading an older database (MariaDB/XAMPP).

## Notes
- E-mail needs SMTP enabled in Admin → Settings → Email. Use a Gmail App Password or your hosting mailbox credentials and SMTP hostname; presets and a test-mail button are provided. Bulk e-mails go to max 1000 unique recipients per send.
- Visitor IPs are tracked for analytics (Admin → Visitor Analytics). Staff visits and bots are not counted.
- Revenue = confirmed/processing/shipped/delivered orders; profit uses each product's cost price at order time.
- Online card/bKash/Nagad gateways are manual (customer pays and writes the transaction ID).

## Branding, icons & settings (latest)
- Your DryNutri logo is built in (assets/img). It is used in the header, footer, side menu, admin sidebar, login pages, invoices, emails and as favicon. Replace it any time in Admin > Settings > Appearance (also upload a white version for dark areas).
- Natural green colour theme (colours editable with one-click presets in Settings > Appearance).
- All interface icons are premium SVG icons (no emoji). Product/category tiles show a fruit emoji only until you upload real photos.
- Settings page: General, Appearance, Contact & Footer, Shipping & Payment, Floating Contact, Notifications, Email (SMTP), SSL Wireless SMS, and Browser Push - all validated and saved per tab.
- Security: CSRF tokens, hashed passwords, login lock after 5 wrong attempts (15 min), security headers, upload checks.
- SEO: Open Graph tags, product structured data, sitemap at /sitemap.php, robots.txt.

## Latest additions
- **Weight options (200 g / 100 g / 50 g / 25 g ...)**: add them on each product (Admin > Products > Weight / size options) with their own price, old price (shows the % OFF badge), cost and stock. Customers pick one on the product page; if they do not, the LOWEST quantity is selected automatically. The chosen weight appears in the cart, checkout, orders, invoice and emails.
- **Delivery charge is chosen at checkout** (Inside Dhaka / Outside Dhaka, amounts in Settings > Shipping & Payment). It is never added automatically in the cart. Free delivery above a set amount is supported.
- **Product image zoom**: hover-zoom on desktop and a full-screen gallery (tap/click) with zoom, arrows and swipe.
- **Subscribers page** (Admin > Subscribers): list, search, add, pause, delete, CSV export and send a notification/email to all subscribers (with unsubscribe link in every email).
- **Checkout Visited** (Admin > Checkout Visited): everyone who opened checkout - visitor/account ID, IP, device, cart, and any name/phone/email typed - even if they never ordered, with WhatsApp / Call buttons and CSV export.
- **Customers**: every guest order automatically creates a customer record (marked "from order"); if that person registers later with the same email they get their order history.
- **Profile settings**: click your name at the top right of the admin panel to edit your profile, upload a photo and change your password. Customers can also add a profile photo in My Account.
- **Home slider**: pictures are always shown in full (never cropped). Choose "picture beside text" or "picture only" per slide. The banner section also accepts a full picture.
- **Fixed**: every Save button in the admin panel now returns to the correct admin page (previously showed "Not Found").
- Upgrading an older install? Import `database/update_to_latest.sql` first.
