<?php
declare(strict_types=1);
require_once __DIR__ . '/content.php';
$pricing = require __DIR__ . '/pricing-config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['httponly'=>true, 'secure'=>!empty($_SERVER['HTTPS']), 'samesite'=>'Lax']);
    session_start();
}
if (empty($_SESSION['contact_csrf'])) $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));

function h(string $value): string { return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function wa(string $message = WHATSAPP_MESSAGE): string { return 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode($message); }
function current_url(string $path = ''): string {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = preg_replace('/[^a-zA-Z0-9.:-]/', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
    return $scheme . '://' . $host . $path;
}
function page_link(string $route): string { return $route === 'home' ? '/' : '/' . ltrim($route, '/'); }

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = '/' . trim($requestPath, '/');
if ($path === '//') $path = '/';
$articleList = articles();
$article = null;
if (str_starts_with($path, '/blog/')) {
    $slug = substr($path, 6);
    foreach ($articleList as $item) if ($item[0] === $slug) { $article = $item; break; }
}
$routes = ['/', '/services', '/genres', '/blog', '/calculator', '/faq', '/contact', '/privacy', '/terms'];
$isArticle = $article !== null;
$is404 = !in_array($path, $routes, true) && !$isArticle;
if ($is404) http_response_code(404);

$meta = [
    '/' => ['The Beacon Publishers | Book Publishing & Author Services', 'Editing, design, publishing and marketing support for independent authors. A guiding light from manuscript to bookshelf.'],
    '/services' => ['Publishing Services | The Beacon Publishers', 'Explore editing, design, publishing, distribution, author branding and book marketing services.'],
    '/genres' => ['Book Genres | The Beacon Publishers', 'Publishing support for fiction, memoir, children’s books, poetry, business, wellness and more.'],
    '/blog' => ['Author Insights & Publishing Blog | The Beacon Publishers', 'Practical articles on writing, editing, publishing and growing your readership.'],
    '/calculator' => ['Publishing Cost Calculator | The Beacon Publishers', 'Plan your publishing project with our itemised cost calculator. Request a personal quote.'],
    '/faq' => ['Frequently Asked Questions | The Beacon Publishers', 'Clear answers about editing, publishing, author rights, formats, costs and timelines.'],
    '/contact' => ['Contact The Beacon Publishers', 'Tell us about your book and get a clear next step from The Beacon Publishers.'],
    '/privacy' => ['Privacy Policy | The Beacon Publishers', 'How The Beacon Publishers handles enquiry and project information.'],
    '/terms' => ['Terms & Conditions | The Beacon Publishers', 'Project scope, timelines, estimates and author ownership information.'],
];
$title = $isArticle ? $article[2] . ' | The Beacon Publishers' : ($is404 ? 'Page Not Found | The Beacon Publishers' : $meta[$path][0]);
$description = $isArticle ? $article[3] : ($is404 ? 'The requested page could not be found.' : $meta[$path][1]);
$canonical = current_url($path === '/' ? '/' : $path);

$knowledge = array_merge(
    array_map(fn($item) => ['q'=>$item[0], 'a'=>$item[1], 'url'=>'/faq'], faqs()),
    array_map(fn($item) => ['q'=>$item[0], 'a'=>$item[1], 'url'=>'/services'], services()),
    array_map(fn($item) => ['q'=>$item[2] . ' ' . $item[3], 'a'=>implode(' ', $item[4]), 'url'=>'/blog/' . $item[0]], $articleList),
    [
        ['q'=>'What genres do you work with?', 'a'=>'We support ' . implode(', ', genres()) . '.', 'url'=>'/genres'],
        ['q'=>'How do I contact the team?', 'a'=>'Use the contact form, email ' . CONTACT_EMAIL . ', or message us on WhatsApp at +1 (771) 218-2277.', 'url'=>'/contact'],
        ['q'=>'How does the publishing process work?', 'a'=>'The four steps are Consult & Edit, Design, Publish, and Promote. The team reviews your manuscript and goals, prepares your cover and interior, sets up publishing platforms, then plans promotion.', 'url'=>'/services'],
        ['q'=>'What is included in the Essential package?', 'a'=>'Proofreading, eBook formatting, cover design and KDP setup.', 'url'=>'/'],
        ['q'=>'What is included in the Professional package?', 'a'=>'Copy editing, print and eBook interiors, cover design, ISBN assistance, KDP and IngramSpark.', 'url'=>'/'],
        ['q'=>'What is included in the Signature package?', 'a'=>'Full editing, all formats, author website, metadata optimization and a launch campaign.', 'url'=>'/'],
        ['q'=>'What does the privacy policy say?', 'a'=>'The site collects information authors choose to share to respond to enquiries and deliver services. Personal information is not sold; manuscripts are treated as confidential and used only for requested work.', 'url'=>'/privacy'],
        ['q'=>'What are the terms and conditions?', 'a'=>'Authors retain full ownership and copyright of their work. Project scope, timelines and fees are agreed in writing before work begins. Estimates from the cost calculator are indicative only; a final quotation is provided after reviewing the manuscript. Questions can be sent to ' . CONTACT_EMAIL . '.', 'url'=>'/terms'],
        ['q'=>'What information does the privacy policy cover?', 'a'=>'The privacy policy says the publisher collects only information voluntarily shared, such as name, email, phone and project details, to respond to enquiries and deliver services. Personal information is not sold. Manuscripts are treated as confidential and used only for the requested work. Requests about or to delete data can be emailed to ' . CONTACT_EMAIL . '.', 'url'=>'/privacy'],
        ['q'=>'What is The Beacon Publishers?', 'a'=>'The Beacon Publishers helps independent authors turn finished and in-progress manuscripts into professionally published books, bringing together editing, design, publishing and marketing support.', 'url'=>'/'],
        ['q'=>'What are the reasons to choose The Beacon Publishers?', 'a'=>'The site highlights editorial care, author ownership and control, clear guidance, and itemised pricing with no hidden fees.', 'url'=>'/'],
        ['q'=>'How much does publishing cost?', 'a'=>'Pricing has not yet been configured. The calculator interface is available, but current prices require confirmation. Request a personalised quote through the contact page.', 'url'=>'/calculator'],
    ]
);

$formStatus = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/contact') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $country = trim((string)($_POST['country'] ?? ''));
    $genre = trim((string)($_POST['genre'] ?? ''));
    $manuscript = trim((string)($_POST['manuscript'] ?? ''));
    $project = trim((string)($_POST['project'] ?? ''));
    $selectedServices = array_map('trim', (array)($_POST['services'] ?? []));
    $trap = trim((string)($_POST['website'] ?? ''));
    if (!hash_equals($_SESSION['contact_csrf'] ?? '', (string)($_POST['csrf'] ?? ''))) {
        $formStatus = 'invalid';
    } elseif ($trap !== '') {
        $formStatus = 'sent';
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $project === '') {
        $formStatus = 'invalid';
    } else {
        $allowed = array_column(services(), 0);
        $selectedServices = array_values(array_intersect($selectedServices, $allowed));
        $subject = 'New publishing enquiry from ' . preg_replace('/[\r\n]+/', ' ', $name);
        $body = "New enquiry from The Beacon Publishers website\n\n" .
            "Name: {$name}\nEmail: {$email}\nPhone / WhatsApp: {$phone}\nCountry: {$country}\n" .
            "Genre: {$genre}\nManuscript status: {$manuscript}\n" .
            'Services: ' . ($selectedServices ? implode(', ', $selectedServices) : 'Not specified') . "\n\nProject description:\n{$project}\n";
        $headers = "From: " . CONTACT_EMAIL . "\r\nReply-To: " . str_replace(["\r", "\n"], '', $email) . "\r\nContent-Type: text/plain; charset=UTF-8";
        $formStatus = function_exists('mail') && @mail(CONTACT_EMAIL, $subject, $body, $headers) ? 'sent' : 'error';
    }
}

function header_view(string $active, string $title, string $description, string $canonical): void { ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($title) ?></title><meta name="description" content="<?= h($description) ?>">
  <meta name="robots" content="<?= isset($_SERVER['REQUEST_URI']) && !in_array(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ['/', '/services', '/genres', '/blog', '/calculator', '/faq', '/contact', '/privacy', '/terms'], true) && !str_starts_with((string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/blog/') ? 'noindex,follow' : 'index,follow' ?>"><link rel="canonical" href="<?= h($canonical) ?>">
  <meta property="og:type" content="website"><meta property="og:title" content="<?= h($title) ?>"><meta property="og:description" content="<?= h($description) ?>"><meta property="og:url" content="<?= h($canonical) ?>"><meta property="og:image" content="<?= h(current_url('/assets/hero-books.jpg')) ?>">
  <meta name="twitter:card" content="summary_large_image"><meta name="twitter:title" content="<?= h($title) ?>"><meta name="twitter:description" content="<?= h($description) ?>">
  <link rel="icon" type="image/png" href="/assets/logo-beacon.png"><link rel="stylesheet" href="/assets/site.css">
  <script type="application/ld+json"><?= json_encode(['@context'=>'https://schema.org','@type'=>'WebSite','name'=>BRAND,'url'=>current_url('/')], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header"><div class="shell header-inner">
  <a class="brand" href="/" aria-label="The Beacon Publishers home"><img src="/assets/logo-beacon.png" alt="The Beacon Publishers" width="66" height="66"><span class="brand-name"><span>The</span><strong>Beacon Publishers</strong></span></a>
  <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav"><span></span><span></span><span></span><b class="sr-only">Toggle menu</b></button>
  <nav class="primary-nav" id="primary-nav" aria-label="Primary navigation">
  <?php foreach (['Home'=>'/','Services'=>'/services','Genres'=>'/genres','Blogs'=>'/blog','Cost Calculator'=>'/calculator','FAQ'=>'/faq','Contact Us'=>'/contact'] as $label=>$url): ?><a href="<?= h($url) ?>"<?= ($active === $url || ($url === '/blog' && str_starts_with($active, '/blog/'))) ? ' aria-current="page"' : '' ?>><?= h($label) ?></a><?php endforeach; ?>
  <a class="button button-small nav-cta" href="/contact">Get a Free Consultation</a></nav>
</div></header>
<?php }

function footer_view(array $knowledge, array $pricing): void { ?>
<footer class="site-footer"><div class="shell footer-grid">
  <div class="footer-brand"><a class="brand brand-footer" href="/"><img src="/assets/logo-beacon.png" alt="The Beacon Publishers" width="66" height="66"><span class="brand-name"><span>The</span><strong>Beacon Publishers</strong></span></a><p>Editing, design, publishing and marketing support for independent authors.</p><div class="social-links"><a href="https://www.instagram.com/thebeaconpublishers/" aria-label="Instagram">◎</a></div></div>
  <div><h2>Quick Links</h2><ul><li><a href="/">Home</a></li><li><a href="/services">Services</a></li><li><a href="/genres">Genres</a></li><li><a href="/blog">Blogs</a></li><li><a href="/calculator">Cost Calculator</a></li><li><a href="/faq">FAQ</a></li><li><a href="/contact">Contact Us</a></li></ul></div>
  <div><h2>Services</h2><ul><?php foreach (array_slice(services(), 0, 6) as $service): ?><li><a href="/services"><?= h($service[0]) ?></a></li><?php endforeach; ?></ul></div>
  <div><h2>Contact</h2><ul><li><a href="mailto:<?= h(CONTACT_EMAIL) ?>"><?= h(CONTACT_EMAIL) ?></a></li><li><a href="<?= h(wa()) ?>">+1 (771) 218-2277</a></li></ul><a class="text-link" href="<?= h(wa()) ?>">Chat on WhatsApp <span aria-hidden="true">↗</span></a></div>
</div><div class="shell footer-bottom"><span>© <?= date('Y') ?> The Beacon Publishers. All rights reserved.</span><div><a href="/privacy">Privacy Policy</a><a href="/terms">Terms &amp; Conditions</a></div></div></footer>
<a class="whatsapp-float" href="<?= h(wa()) ?>" target="_blank" rel="noopener" aria-label="WhatsApp consultation"><span aria-hidden="true">WA</span></a>
<section class="quill" aria-label="Ask Beacon's Assistant"><div class="quill-panel" id="quill-panel" hidden><div class="quill-head"><div><strong>Beacon’s Assistant</strong><span>Author guide · The Beacon Publishers</span></div><button type="button" class="quill-close" aria-label="Close chat">×</button></div><div class="quill-log" id="quill-log" aria-live="polite"><div class="chat-message bot">Hi, I’m Beacon’s Assistant. Ask me about our services, publishing process, genres, formats, FAQs or getting started. I answer from the information on this website.</div><div class="chat-suggestions"><button type="button">What services do you offer?</button><button type="button">Do I keep my rights?</button><button type="button">How do I get started?</button></div></div><form id="quill-form" class="quill-form"><label class="sr-only" for="quill-input">Ask about your book</label><input id="quill-input" name="question" placeholder="Ask about your book…" autocomplete="off" required><button type="submit" aria-label="Send question">↑</button></form></div><button class="quill-launch" type="button" aria-expanded="false" aria-controls="quill-panel"><span class="quill-orbit">✦</span><span>Ask Quill</span></button></section>
<script>window.beaconKnowledge = <?= json_encode($knowledge, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>; window.beaconPricing = <?= json_encode($pricing, JSON_HEX_TAG | JSON_HEX_AMP) ?>;</script>
<script src="/assets/site.js" defer></script>
</body></html>
<?php }

header_view($path, $title, $description, $canonical);
?>
<main id="main">
<?php if ($path === '/'): ?>
  <section class="home-hero"><div class="shell hero-layout"><div class="hero-copy"><p class="eyebrow">THE BEACON PUBLISHERS</p><h1>Build a brand that <em>speaks</em> before you do.</h1><p class="hero-lede">Transform your ideas into a recognizable professional identity.</p><div class="button-row"><a class="button" href="/contact">Start Your Publishing Journey <span>→</span></a><a class="button button-outline" href="/services">Explore Our Services</a></div><div class="hero-note"><span class="gold-dot"></span> Thoughtful support from manuscript to bookshelf</div></div><div class="hero-image"><img src="/assets/hero-books.jpg" alt="A carefully arranged stack of books in a warm, literary setting" fetchpriority="high"><div class="image-caption"><span>YOUR STORY</span><strong>Ready for its next chapter</strong></div></div></div><div class="hero-mark" aria-hidden="true">B</div></section>
  <section class="section about-section"><div class="shell about-grid"><div class="about-image"><img src="/assets/writing-desk.jpg" alt="A writer’s desk with a manuscript and books" loading="lazy"><span class="image-index">01 / THE BEGINNING</span></div><div class="about-copy"><p class="eyebrow">ABOUT US</p><h2>A guiding light from manuscript to bookshelf.</h2><p>The Beacon Publishers helps independent authors turn finished and in-progress manuscripts into professionally published books. We bring together editing, design, publishing and marketing so you can work with one team instead of many.</p><p>Whether this is your first book or your fifth, we tailor our support to your goals, your genre and your budget.</p><a class="text-link" href="/services">See what we do <span>→</span></a></div></div></section>
  <section class="section reasons-section"><div class="shell"><div class="section-intro"><p class="eyebrow">WHY CHOOSE US</p><h2>Publishing, done thoughtfully.</h2></div><div class="reason-grid"><?php foreach ([['Editorial care','Every manuscript is handled with attention to voice, clarity and craft.','✧'],['You keep your rights','Your book, your royalties and your creative control stay with you.','◇'],['Clear guidance','Plain-language advice at each step, so you always know what comes next.','⌖'],['Transparent pricing','Itemised estimates with no hidden fees — see our cost calculator.','✦']] as $i=>$r): ?><article class="reason-card"><span class="reason-icon"><?= $r[2] ?></span><span class="card-number">0<?= $i+1 ?></span><h3><?= h($r[0]) ?></h3><p><?= h($r[1]) ?></p></article><?php endforeach; ?></div></div></section>
  <section class="section process-section"><div class="shell"><div class="section-intro"><p class="eyebrow">OUR PROCESS</p><h2>Four steps to your published book.</h2></div><div class="process-grid"><?php foreach ([['Consult & Edit','We review your manuscript and goals, then refine the writing.'],['Design','Cover and interior layouts crafted for your genre and formats.'],['Publish','Set up on Amazon KDP, IngramSpark and other platforms.'],['Promote','Launch planning and marketing to help readers find your book.']] as $i=>$p): ?><article class="process-step"><span>0<?= $i+1 ?></span><h3><?= h($p[0]) ?></h3><p><?= h($p[1]) ?></p></article><?php endforeach; ?></div></div></section>
  <section class="section genres-feature"><div class="shell"><div class="section-heading"><div><p class="eyebrow">FEATURED GENRES</p><h2>Stories of every shape.</h2></div><a class="text-link" href="/genres">All genres <span>→</span></a></div><div class="genre-feature-grid"><?php foreach ([['Fiction and Romance','hero-books.jpg'],['Thriller and Mystery','bookshelf.jpg'],['Fantasy and Science Fiction','open-book.jpg'],['Memoir and Autobiography','writing-desk.jpg']] as $g): ?><a class="genre-feature-card" href="/genres"><img src="/assets/<?= h($g[1]) ?>" alt="" loading="lazy"><span><?= h($g[0]) ?></span><b aria-hidden="true">↗</b></a><?php endforeach; ?></div></div></section>
  <section class="author-note"><div class="shell author-note-inner"><p class="eyebrow">AUTHOR STORIES</p><h2>Your success story belongs here.</h2><p>We share author testimonials only with their permission, in their own words. As our authors publish with us, their stories will appear in this space.</p><a class="button button-light" href="/contact">Become our next author <span>→</span></a><blockquote>“Every author deserves a book they are proud to hold.” <cite>— Our guiding principle</cite></blockquote></div></section>
  <section class="section packages-section"><div class="shell"><div class="section-intro"><p class="eyebrow">PUBLISHING PACKAGES</p><h2>Choose a starting point.</h2><p>Every package can be customised. Use the calculator to prepare your project details for a quote.</p></div><div class="package-grid"><?php foreach ([['Essential','Proofreading, formatting, cover design and eBook publishing.',['Proofreading','eBook formatting','Cover design','KDP setup']],['Professional','Everything an author needs for print and digital release.',['Copy editing','Print + eBook interiors','Cover design','ISBN assistance','KDP & IngramSpark']],['Signature','A complete publishing and author-brand launch.',['Full editing','All formats','Author website','Metadata optimization','Launch campaign']]] as $i=>$pack): ?><article class="package-card<?= $i===1?' package-featured':'' ?>"><?php if ($i===1): ?><span class="package-ribbon">MOST COMPLETE FOR PRINT</span><?php endif; ?><h3><?= h($pack[0]) ?></h3><p><?= h($pack[1]) ?></p><ul><?php foreach ($pack[2] as $v): ?><li><span>✦</span><?= h($v) ?></li><?php endforeach; ?></ul><a class="text-link" href="/calculator">Estimate project <span>→</span></a></article><?php endforeach; ?></div></div></section>
  <?php consultation_cta(); ?>
<?php elseif ($path === '/services'): ?>
  <section class="page-hero"><div class="shell"><p class="eyebrow">OUR SERVICES</p><h1>Complete publishing and<br><em>marketing support.</em></h1><p>Pick a single service or combine them into a full publishing journey.</p></div></section><section class="section services-list"><div class="shell service-grid"><?php foreach (services() as $i=>$s): ?><article class="service-card"><span class="service-number"><?= sprintf('%02d',$i+1) ?></span><div><h2><?= h($s[0]) ?></h2><p><?= h($s[1]) ?></p></div><span class="service-arrow" aria-hidden="true">↗</span></article><?php endforeach; ?></div><div class="shell center-cta"><a class="button" href="/calculator">Estimate your project <span>→</span></a></div></section><?php consultation_cta(); ?>
<?php elseif ($path === '/genres'): ?>
  <section class="page-hero"><div class="shell"><p class="eyebrow">GENRES</p><h1>Every genre, published<br><em>with care.</em></h1><p>Readers in each genre expect different things. We design and position your book to meet them.</p></div></section><section class="section"><div class="shell genres-grid"><?php $genreImages=['hero-books.jpg','bookshelf.jpg','open-book.jpg','writing-desk.jpg']; foreach (genres() as $i=>$g): ?><a class="genre-card" href="/contact"><div class="genre-image"><img src="/assets/<?= h($genreImages[$i%4]) ?>" alt="<?= h($g) ?> books" loading="lazy"><span><?= sprintf('%02d',$i+1) ?></span></div><div class="genre-card-title"><h2><?= h($g) ?></h2><span>Discuss your book →</span></div></a><?php endforeach; ?></div></section><?php consultation_cta(); ?>
<?php elseif ($path === '/blog'): ?>
  <section class="page-hero"><div class="shell"><p class="eyebrow">KNOWLEDGE CENTER</p><h1>Insights for authors.</h1><p>Practical articles on writing, publishing and growing your readership.</p></div></section><section class="section blog-section"><div class="shell"><div class="featured-articles"><?php foreach (array_slice($articleList,0,2) as $i=>$a): ?><a class="featured-article" href="/blog/<?= h($a[0]) ?>"><span class="eyebrow"><?= h($i===0?'FEATURED · '.$a[1]:$a[1]) ?></span><h2><?= h($a[2]) ?></h2><p><?= h($a[3]) ?></p><span class="text-link">Read article →</span></a><?php endforeach; ?></div><div class="blog-tools"><div class="filter-list" aria-label="Filter articles"><button class="filter-chip active" data-filter="All">All</button><?php foreach (array_values(array_unique(array_column($articleList,1))) as $category): ?><button class="filter-chip" data-filter="<?= h($category) ?>"><?= h($category) ?></button><?php endforeach; ?></div><label class="search-box"><span aria-hidden="true">⌕</span><input type="search" id="blog-search" placeholder="Search articles" aria-label="Search articles"></label></div><div class="blog-grid" id="blog-grid"><?php foreach ($articleList as $i=>$a): ?><article class="blog-card" data-category="<?= h($a[1]) ?>" data-search="<?= h(strtolower($a[1].' '.$a[2].' '.$a[3])) ?>"><a href="/blog/<?= h($a[0]) ?>"><div class="blog-image blog-image-<?= $i%4 ?>"><img src="/assets/<?= h(['hero-books.jpg','bookshelf.jpg','open-book.jpg','writing-desk.jpg'][$i%4]) ?>" alt="" loading="lazy"><span><?= h($a[1]) ?></span></div><h2><?= h($a[2]) ?></h2><p><?= h($a[3]) ?></p><span class="text-link">Read article <b>→</b></span></a></article><?php endforeach; ?></div><p class="empty-state" id="blog-empty" hidden>No articles match your search.</p></div></section>
<?php elseif ($isArticle): ?>
  <article class="article-page"><div class="article-shell"><a class="back-link" href="/blog">← All articles</a><p class="eyebrow"><?= h(strtoupper($article[1])) ?></p><h1><?= h($article[2]) ?></h1><p class="article-deck"><?= h($article[3]) ?></p><div class="article-rule"></div><?php foreach ($article[4] as $paragraph): ?><p><?= h($paragraph) ?></p><?php endforeach; ?><div class="article-footer"><a href="/contact">Talk through your project <span>→</span></a><a href="/calculator">Visit the cost calculator <span>→</span></a></div></div><div class="shell keep-reading"><h2>Keep reading</h2><div><?php foreach (array_slice(array_values(array_filter($articleList,fn($item)=>$item[0]!==$article[0])),0,3) as $other): ?><a href="/blog/<?= h($other[0]) ?>"><span><?= h(strtoupper($other[1])) ?></span><strong><?= h($other[2]) ?></strong></a><?php endforeach; ?></div></div></article><?php consultation_cta(); ?>
<?php elseif ($path === '/calculator'): ?>
  <section class="page-hero"><div class="shell"><p class="eyebrow">COST CALCULATOR</p><h1>Estimate your publishing<br><em>investment.</em></h1><p>Adjust the options below to prepare an itemised project brief. Final quotes are confirmed after reviewing your manuscript.</p></div></section><section class="section calculator-section"><div class="shell calculator-layout"><form class="calculator-form" id="calculator-form"><div class="calc-step"><label class="step-title" for="calc-genre">1. Book genre</label><select id="calc-genre" name="genre"><?php foreach (genres() as $i=>$g): ?><option<?= $i===0?' selected':'' ?>><?= h($g) ?></option><?php endforeach; ?></select></div><div class="calc-step"><label class="step-title" for="calc-pages">2. Number of pages: <output id="page-count">200</output></label><input id="calc-pages" type="range" min="<?= (int)$pricing['page_min'] ?>" max="<?= (int)$pricing['page_max'] ?>" step="<?= (int)$pricing['page_step'] ?>" value="200"><div class="range-labels"><span><?= (int)$pricing['page_min'] ?> pages</span><span><?= (int)$pricing['page_max'] ?> pages</span></div></div><fieldset class="calc-step"><legend class="step-title">3. Manuscript status</legend><div class="choice-grid"><?php foreach (['Final draft (proofread only)','Needs light editing','Needs heavy editing','Still writing'] as $i=>$v): ?><button type="button" class="choice-button<?= $i===1?' selected':'' ?>" data-choice="status" aria-pressed="<?= $i===1?'true':'false' ?>"><?= h($v) ?></button><?php endforeach; ?></div></fieldset><fieldset class="calc-step"><legend class="step-title">4. Publishing services</legend><div class="choice-grid"><?php foreach (['Professional Cover Design','ISBN & Copyright Assistance','Amazon KDP Publishing','IngramSpark Distribution','Metadata Optimization','Author Website'] as $v): ?><button type="button" class="choice-button" data-choice="service" aria-pressed="false"><?= h($v) ?></button><?php endforeach; ?></div></fieldset><fieldset class="calc-step"><legend class="step-title">5. Publishing formats</legend><div class="choice-grid choice-grid-short"><?php foreach (['eBook','Paperback','Hardcover'] as $v): ?><button type="button" class="choice-button" data-choice="format" aria-pressed="false"><?= h($v) ?></button><?php endforeach; ?></div></fieldset><fieldset class="calc-step"><legend class="step-title">6. Marketing</legend><div class="choice-grid"><?php foreach (['None','Launch essentials','Full launch campaign','Ongoing (3 months)'] as $i=>$v): ?><button type="button" class="choice-button<?= $i===0?' selected':'' ?>" data-choice="marketing" aria-pressed="<?= $i===0?'true':'false' ?>"><?= h($v) ?></button><?php endforeach; ?></div></fieldset></form><aside class="estimate-card"><p class="eyebrow">YOUR PROJECT BRIEF</p><h2 id="estimate-heading">Pricing setup pending</h2><p class="estimate-explainer" id="estimate-explainer">Your selections are saved in this summary. The estimate will appear once approved pricing rules are added.</p><ul class="estimate-breakdown" id="estimate-breakdown" hidden></ul><dl><div><dt>Genre</dt><dd id="summary-genre">Fiction and Romance</dd></div><div><dt>Page count</dt><dd id="summary-pages">200 pages</dd></div><div><dt>Manuscript</dt><dd id="summary-status">Needs light editing</dd></div><div><dt>Services</dt><dd id="summary-services">None selected</dd></div><div><dt>Formats</dt><dd id="summary-formats">None selected</dd></div><div><dt>Marketing</dt><dd id="summary-marketing">None</dd></div></dl><a class="button button-full" href="/contact">Request a personalised quote <span>→</span></a><p class="fine-print">The original example figures are not approved current rates and are not used here.</p></aside></div></section>
<?php elseif ($path === '/faq'): ?>
  <section class="page-hero"><div class="shell"><p class="eyebrow">FAQ</p><h1>Questions authors<br><em>ask us.</em></h1><p>Clear answers about editing, publishing, rights and costs.</p></div></section><section class="section faq-section"><div class="faq-shell"><?php foreach (faqs() as $f): ?><details class="faq-item"><summary><?= h($f[0]) ?><span aria-hidden="true">+</span></summary><div class="faq-answer"><p><?= h($f[1]) ?></p><?php if (str_contains($f[0],'cost')): ?><a href="/calculator">Open the cost calculator →</a><?php endif; ?></div></details><?php endforeach; ?><p class="faq-contact">Still have a question? Ask Quill in the chat, or <a href="/contact">contact us</a>.</p></div></section>
<?php elseif ($path === '/contact'): ?>
  <section class="page-hero"><div class="shell"><p class="eyebrow">CONTACT US</p><h1>Tell us about<br><em>your book.</em></h1><p>We’ll reply with recommendations and a clear next step.</p></div></section><section class="section contact-section"><div class="shell contact-layout"><form class="contact-form" method="post" action="/contact" novalidate><input type="hidden" name="csrf" value="<?= h($_SESSION['contact_csrf']) ?>"><div class="form-grid"><label>Full Name<input type="text" name="name" autocomplete="name" required></label><label>Email<input type="email" name="email" autocomplete="email" required></label><label>Phone / WhatsApp<input type="tel" name="phone" autocomplete="tel"></label><label>Country<input type="text" name="country" autocomplete="country-name"></label><label>Book Genre<select name="genre"><?php foreach (genres() as $g): ?><option><?= h($g) ?></option><?php endforeach; ?></select></label><label>Manuscript Status<select name="manuscript"><option>Final draft (proofread only)</option><option>Needs light editing</option><option>Needs heavy editing</option><option>Still writing</option></select></label></div><fieldset class="service-picker"><legend>Required Services</legend><div class="service-checks"><?php foreach (services() as $s): ?><label><input type="checkbox" name="services[]" value="<?= h($s[0]) ?>"><span><?= h($s[0]) ?></span></label><?php endforeach; ?></div></fieldset><label class="project-field">Project Description<textarea name="project" rows="5" required placeholder="Tell us a little about your book and what you need."></textarea></label><label class="honeypot" aria-hidden="true">Leave this field empty<input name="website" tabindex="-1" autocomplete="off"></label><button class="button" type="submit">Send Message <span>→</span></button><?php if ($formStatus==='sent'): ?><p class="form-message success" role="status">Thank you. Your message has been sent. We’ll be in touch soon.</p><?php elseif ($formStatus==='invalid'): ?><p class="form-message error" role="alert">Please add your name, a valid email address and a project description.</p><?php elseif ($formStatus==='error'): ?><p class="form-message error" role="alert">We couldn’t send your message right now. Please email us directly or contact us on WhatsApp.</p><?php endif; ?></form><aside class="contact-aside"><span class="eyebrow">PREFER TO CHAT?</span><h2>Let’s find your next step.</h2><p>Start a free consultation on WhatsApp or write to our support team.</p><a class="button" href="<?= h(wa()) ?>" target="_blank" rel="noopener">WhatsApp Consultation <span>↗</span></a><a class="contact-email" href="mailto:<?= h(CONTACT_EMAIL) ?>"><?= h(CONTACT_EMAIL) ?></a><a class="contact-email" href="<?= h(wa()) ?>">+1 (771) 218-2277</a></aside></div></section>
<?php elseif ($path === '/privacy'): ?>
  <section class="legal-page"><div class="article-shell"><p class="eyebrow">THE BEACON PUBLISHERS</p><h1>Privacy Policy</h1><p>We collect only the information you choose to share with us — such as your name, email, phone number and details about your project — in order to respond to your enquiry and deliver our services.</p><p>We do not sell your personal information. Manuscripts you send us are treated as confidential and used only for the work you request.</p><p>To ask about or delete your data, email <a href="mailto:<?= h(CONTACT_EMAIL) ?>"><?= h(CONTACT_EMAIL) ?></a>.</p></div></section>
<?php elseif ($path === '/terms'): ?>
  <section class="legal-page"><div class="article-shell"><p class="eyebrow">THE BEACON PUBLISHERS</p><h1>Terms &amp; Conditions</h1><p>Authors retain full ownership and copyright of their work. Project scope, timelines and fees are agreed in writing before work begins.</p><p>Estimates from our cost calculator are indicative only; a final quotation is provided after reviewing your manuscript.</p><p>Questions? Contact <a href="mailto:<?= h(CONTACT_EMAIL) ?>"><?= h(CONTACT_EMAIL) ?></a>.</p></div></section>
<?php else: ?>
  <section class="not-found"><div class="shell"><p class="eyebrow">404 · PAGE NOT FOUND</p><h1>This page seems to have<br><em>lost its place.</em></h1><p>The page may have moved. Let’s get you back on your way.</p><a class="button" href="/">Return home <span>→</span></a></div></section>
<?php endif; ?>
</main>
<?php footer_view($knowledge, $pricing); ?>
<?php
function consultation_cta(): void { ?>
<section class="consultation-cta"><div class="shell cta-inner"><div><p class="eyebrow">FREE CONSULTATION</p><h2>Let’s talk about your book.</h2><p>Share where you are in the process and we’ll suggest a clear next step.</p></div><div class="button-row"><a class="button button-light" href="/contact">Get a Free Consultation <span>→</span></a><a class="button button-clear" href="<?= h(wa()) ?>" target="_blank" rel="noopener">WhatsApp Us <span>↗</span></a></div></div></section>
<?php }
?>


