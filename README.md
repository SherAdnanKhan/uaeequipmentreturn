# UAE Equipment Rental WordPress Site

Local WordPress website for CAT 226B bobcat / skid steer loader rental with operator.

## Run After Laptop Restart

Open Terminal and run:

```bash
cd "/Users/sheradnan.khan/Documents/flutterDev/projects/practice/testapp"
brew services start mysql
/Applications/ServBay/package/php/8.1/8.1.34/bin/php -d memory_limit=512M /opt/homebrew/bin/wp server --host=localhost --port=8080 --path=wordpress
```

Then open:

```text
http://localhost:8080
```

## WordPress Admin

Admin URL:

```text
http://localhost:8080/wp-admin
```

Login:

```text
Username: admin
Password: Admin@123456
```

## If Port 8080 Is Busy

Use another port:

```bash
/Applications/ServBay/package/php/8.1/8.1.34/bin/php -d memory_limit=512M /opt/homebrew/bin/wp server --host=localhost --port=8081 --path=wordpress
```

Then open:

```text
http://localhost:8081
```

## Useful Setup Scripts

Rebuild bilingual English/Arabic pages, blog posts, menus, Polylang links, and SEO metadata:

```bash
/Applications/ServBay/package/php/8.1/8.1.34/bin/php -d memory_limit=512M /opt/homebrew/bin/wp eval-file setup-wordpress-content.php --path=wordpress
```

Recreate and embed the English and Arabic Fluent Forms quote forms:

```bash
/Applications/ServBay/package/php/8.1/8.1.34/bin/php -d memory_limit=512M /opt/homebrew/bin/wp eval-file setup-fluent-form.php --path=wordpress
```

## Notes

- Database name: `testapp_wp`
- WordPress folder: `wordpress/`
- Active theme: `dubai-bobcat-rental`
- Languages: English default plus Arabic at `/ar/` through Polylang.
- Business name: `UAE Equipment Rental`
- Phone / WhatsApp: `+971 54 738 8695`
- Headquarters: `Dibba, Fujairah, United Arab Emirates`
- Hours: `24/7`
- Service area: UAE-wide by delivery discussion; delivery can be free near Fujairah and quoted elsewhere.
- Real machine media from `/Users/sheradnan.khan/Downloads/WhatsApp Unknown 2026-06-20 at 22.06.41.zip` has been optimized into the theme `assets/` folder.
- Machine document source: `/Users/sheradnan.khan/Downloads/38324 BOBCAT MALKIA.pdf`. Public page copy uses machine type/model details only, not licence dates.
- LiteSpeed Cache and Wordfence are installed but inactive for local preview speed. Activate and configure them on proper hosting.
- Add only verified customer reviews and the real Google Business Profile link once available.
# uaeequipmentreturn
