<?php
declare(strict_types=1);

const BRAND = 'The Beacon Publishers';
const CONTACT_EMAIL = 'support@thebeaconpublishers.com';
const WHATSAPP_NUMBER = '17712182277';
const WHATSAPP_MESSAGE = "Hello Beacon Publishers, I'd like a free publishing consultation.";

function genres(): array {
    return [
        'Fiction and Romance', 'Thriller and Mystery', 'Fantasy and Science Fiction',
        'Memoir and Autobiography', 'Self-Help and Personal Development',
        'Business and Entrepreneurship', 'Health and Wellness', "Children's Books",
        'Poetry', 'Journals and Workbooks', 'Educational Books'
    ];
}

function services(): array {
    return [
        ['Manuscript Editing & Proofreading', 'Developmental, line and copy editing plus a final proofread so your writing reads clean and confident.'],
        ['Book Formatting & Interior Design', 'Print-ready and reflowable layouts with considered typography, chapter openers and consistent styling.'],
        ['Professional Cover Design', 'Genre-aware covers designed to look right on a shelf and as a thumbnail online.'],
        ['ISBN & Copyright Registration Assistance', 'Guidance through ISBN acquisition and copyright registration so your rights are documented.'],
        ['Amazon KDP Publishing', 'Account setup, file upload, pricing and listing configuration on Kindle Direct Publishing.'],
        ['Paperback, Hardcover & eBook Publishing', "One manuscript, every format — prepared to each platform's technical specifications."],
        [
            'IngramSpark & Global Distribution', "Reach bookstores, libraries and retailers through IngramSpark's distribution network."
        ],
        ['Metadata Optimization', 'Titles, subtitles, categories, keywords and descriptions written to help readers find your book.'],
        ['Author Branding & Website Development', 'A cohesive author identity and a website that gives readers a home for your work.'],
        ['Book Marketing & Promotion', 'Launch planning, reader outreach and promotional assets tailored to your audience and budget.'],
    ];
}

function faqs(): array {
    return [
        ['Do I keep the rights to my book?', 'Yes. You keep full ownership of your work and your royalties. We help you publish under your own name and accounts.'],
        ["My manuscript isn't finished yet. Can you still help?", 'Absolutely. We can advise on structure and planning while you write, then step in with editing once your draft is ready.'],
        ['What kinds of editing do you offer?', 'Developmental editing (story and structure), line editing (style and flow), copy editing (grammar and consistency) and a final proofread.'],
        ['How much does publishing cost?', 'It depends on your page count, the level of editing and the services you choose. Use our Cost Calculator for an estimate; we confirm a final quote after a free consultation.'],
        ['How long does the process take?', "Timelines depend on your manuscript's length and the services involved. We agree a schedule with you before work begins."],
        ['Which platforms do you publish on?', 'We prepare and publish on Amazon KDP and IngramSpark, which together reach online retailers, bookstores and libraries worldwide.'],
        ['Can you publish paperback, hardcover and eBook versions?', 'Yes. We format one manuscript for every format, meeting each platform’s technical requirements.'],
        ['Do you help with ISBNs and copyright?', 'We guide you through getting an ISBN and registering copyright so your rights are properly documented.'],
        ['Will you design my book cover?', 'Yes. Our designers create genre-aware covers that look great on a shelf and as a small online thumbnail.'],
        ['Do you help market my book after launch?', 'We offer launch planning, reader outreach, promotional graphics and ongoing marketing support tailored to your budget.'],
        ['How do I get started?', 'Book a free consultation through the contact form, chat with Beacon’s Assistant, our author guide, or message us on WhatsApp.'],
    ];
}

function articles(): array {
    return [
        ['preparing-your-manuscript-for-editing', 'Editing', 'Preparing Your Manuscript for Professional Editing', 'Simple steps that help your editor focus on what matters most in your book.', [
            'Before sending your manuscript to an editor, take time to read it through in full. Note recurring issues you already know about so your editor can address them directly.',
            'Use consistent formatting: one font, standard spacing, and clear chapter headings. This keeps attention on your writing rather than layout.',
            'Finally, decide which kind of edit you need. Developmental editing looks at structure and story; copy editing focuses on sentences, grammar and consistency.'
        ]],
        ['understanding-amazon-kdp', 'Amazon KDP', 'Understanding Amazon KDP: A Plain-Language Guide', 'How Kindle Direct Publishing works, from uploading files to setting your price.', [
            'Kindle Direct Publishing lets authors publish eBooks and paperbacks that sell on Amazon. You keep control of your rights and pricing.',
            'You’ll need a formatted interior file, a cover sized to your trim and page count, and your book’s metadata — title, description, categories and keywords.',
            'Royalty options and pricing affect each other, so review Amazon’s current terms before you choose.'
        ]],
        ['what-makes-a-cover-work', 'Book Design', 'What Makes a Book Cover Work', 'Genre signals, legibility and the thumbnail test.', [
            'Readers judge genre within a second. A good cover uses the visual language of its category so the right readers recognise it.',
            'Check your cover at thumbnail size. If the title is unreadable at the size shown in online stores, simplify.',
            'Typography matters as much as imagery — choose type that feels native to your genre.'
        ]],
        ['building-an-author-brand', 'Author Branding', 'Building an Author Brand Readers Remember', 'Consistency across your covers, website and voice builds recognition over time.', [
            'An author brand is the promise readers associate with your name. It comes from consistent themes, tone and visual identity.',
            'Start with a simple website, a professional photo and a short bio written for your readers.',
            'Keep your visual style consistent across books in a series so readers can spot them instantly.'
        ]],
        ['planning-a-book-launch', 'Book Marketing', 'Planning a Book Launch on a Realistic Budget', 'Where to focus your time and money in the weeks around release day.', [
            'Begin planning two to three months before release. Gather early readers who can leave honest reviews.',
            'Prepare promotional graphics, a short description and an excerpt you can share.',
            'Measure what works and adjust — a launch is the beginning of a book’s life, not the end.'
        ]],
        ['writing-habits-that-finish-books', 'Writing', 'Writing Habits That Help You Finish the Book', 'Small, repeatable routines matter more than occasional bursts.', [
            'Set a modest daily or weekly target you can actually keep.',
            'Separate drafting from editing. Let the first draft be imperfect.',
            'Track your progress — seeing word counts grow is a powerful motivator.'
        ]],
        ['paperback-vs-hardcover-vs-ebook', 'Publishing', 'Paperback, Hardcover or eBook? Choosing Your Formats', 'Each format reaches readers differently. Here’s how to decide.', [
            'eBooks are inexpensive to produce and easy to distribute worldwide.',
            'Paperbacks are the most popular print format for many genres and work well for print-on-demand.',
            'Hardcovers suit gift books, children’s picture books and titles aimed at libraries.'
        ]],
    ];
}
