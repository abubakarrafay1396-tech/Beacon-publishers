# The Beacon Publishers website

Independent PHP website recreation based on the supplied Lovable reference. It does not load Lovable code or require a Lovable subscription. No database or build step is required.

## Project files

- `index.php` — clean URL router, shared header/footer, SEO tags, pages and contact form handler.
- `content.php` — service, genre, FAQ and blog content.
- `pricing-config.php` — disabled-by-default calculator pricing configuration.
- `assets/site.css`, `assets/site.js` — responsive design and browser interactions.
- `assets/` — original logo and reference book photography.
- `sitemap.php`, `robots.php` — generated sitemap and crawler rules.
- `.htaccess` — clean routes and security response headers.

## Hostinger deployment

Deployment is intentionally not performed. The exact domain and replacement target have not been confirmed.

1. In Hostinger, select a PHP 8.x version for the domain and make sure Apache rewrite support is enabled.
2. Back up any existing site before replacing files.
3. Upload the *contents* of this folder into the domain’s `public_html` directory. Keep `.htaccess` and the `assets` folder.
4. Enable SSL/HTTPS in Hostinger and redirect HTTP to HTTPS using the hosting panel.
5. Visit each route, including `/sitemap.xml` and `/robots.txt`, after deployment.
6. Submit a real contact form after deployment and confirm that the message arrives at `support@thebeaconpublishers.com`. The form uses PHP `mail()`. If Hostinger does not deliver it, configure SMTP using the mailbox’s Hostinger SMTP settings; do not put mailbox passwords in this project or a public repository.

No DNS changes are included. Point the confirmed domain to the Hostinger hosting plan using the values shown in that account.

## Run locally from VS Code

1. Unzip the project and open the `site` folder in VS Code.
2. Install PHP 8.x if it is not already available in your terminal. No Node.js packages are required.
3. Open **Terminal → New Terminal** in VS Code. In PowerShell, run:

   ```powershell
   php -S 127.0.0.1:8000 -t .
   ```

4. Open `http://127.0.0.1:8000/` in your browser. Clean routes such as `/services` and `/faq` are handled by `index.php`.
5. Stop the local server with `Ctrl+C` in the terminal.

PHP’s built-in server is for local preview only. Contact form email delivery depends on a mail setup and may not work from a local machine.

## Contact form

The form validates the name, email and project description, includes CSRF and honeypot checks, and sends the enquiry to the approved support address. It does not save submissions to a database. Deliverability depends on the live hosting/mail configuration, which was not available to verify here.

## Cost calculator

The reference exposes a client-side calculator but no pricing API or backend table. Its example total and rates are not used. Until approved rates are supplied, the interface gathers genre, page count, manuscript status, services, formats and marketing choices and transfers that project brief into the contact form. It clearly labels estimates as pending.

After receiving approved rules, edit `pricing-config.php`:

- Set `enabled` to `true`.
- Enter per-page editing rates under `rates.editing` for `proofread`, `light`, `heavy` and `writing`.
- Enter the interior rate per page under `rates.formatting_per_page`.
- Enter fixed service rates for `cover`, `isbn`, `kdp`, `ingramspark`, `metadata` and `author_website`.
- Enter format rates for `ebook`, `paperback` and `hardcover`.
- Enter campaign rates for `launch`, `full` and `ongoing`. `none` defaults to zero.
- Confirm currency and page-count limits/step.

If any rate needed for a selected option is still missing, the calculator shows that pricing is incomplete and keeps the enquiry CTA available.

## Ask Quill recommendation

The current Ask Quill is a lightweight, private, on-page answer finder. It searches the website’s approved FAQs, service descriptions, genres, packages, process, legal-page summaries and all seven reference articles. It does not send visitor questions to an AI provider, store chat transcripts, or invent an answer when its content does not match; it links the visitor to the closest page or contact route.

For a more conversational assistant, the recommended next step is a server-side retrieval-augmented assistant using only this site’s approved content as its knowledge source. Keep the AI API key on the server, cite the relevant page in each answer, direct pricing questions to the approved calculator/quote flow, provide a human contact fallback, and publish a short notice about AI processing and retention. That version needs an API account/key and the client’s approval of chat data handling. The current offline answer finder works without those credentials.

## Legal copy and content maintenance

Privacy Policy and Terms & Conditions currently reproduce the short reference wording. The client asked to supply final legal copy later; replace the relevant page text in `index.php` before public launch. To edit FAQs, services or articles, update `content.php`; the assistant’s site knowledge uses those data arrays.

## Limits

- The site was not deployed to Hostinger, because the exact domain and live replacement target are not yet confirmed.
- The PHP mail path was not run against a live mailbox. Verify delivery on Hostinger before launch.
- Calculator prices remain disabled pending the client’s approved calculation rules.
- No live AI service is connected to Ask Quill.
- Responsive design and reference content have been recreated in source, but browser QA at target breakpoints and a page-by-page visual comparison remain to be done before delivery sign-off.

