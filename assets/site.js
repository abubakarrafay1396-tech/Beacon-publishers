(() => {
  const menuButton = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.primary-nav');
  if (menuButton && nav) {
    menuButton.addEventListener('click', () => {
      const open = menuButton.getAttribute('aria-expanded') !== 'true';
      menuButton.setAttribute('aria-expanded', String(open));
      nav.classList.toggle('open', open);
    });
    nav.addEventListener('click', event => {
      if (event.target.closest('a')) { nav.classList.remove('open'); menuButton.setAttribute('aria-expanded', 'false'); }
    });
  }

  const blogGrid = document.querySelector('#blog-grid');
  if (blogGrid) {
    let category = 'All';
    const search = document.querySelector('#blog-search');
    const empty = document.querySelector('#blog-empty');
    const update = () => {
      let shown = 0;
      const query = (search?.value || '').trim().toLowerCase();
      blogGrid.querySelectorAll('.blog-card').forEach(card => {
        const visible = (category === 'All' || card.dataset.category === category) && (!query || card.dataset.search.includes(query));
        card.hidden = !visible;
        if (visible) shown++;
      });
      if (empty) empty.hidden = shown !== 0;
    };
    document.querySelectorAll('.filter-chip').forEach(button => button.addEventListener('click', () => {
      category = button.dataset.filter;
      document.querySelectorAll('.filter-chip').forEach(item => item.classList.toggle('active', item === button));
      update();
    }));
    search?.addEventListener('input', update);
  }

  const calculator = document.querySelector('#calculator-form');
  if (calculator) {
    const selected = { status: 'Needs light editing', service: [], format: [], marketing: 'None' };
    const genre = document.querySelector('#calc-genre');
    const pages = document.querySelector('#calc-pages');
    const pageCount = document.querySelector('#page-count');
    const text = id => document.querySelector(id);
    const set = (id, value) => { const el = text(id); if (el) el.textContent = value; };
    const render = () => {
      set('#summary-genre', genre.value);
      set('#summary-pages', `${pages.value} pages`);
      set('#summary-status', selected.status);
      set('#summary-services', selected.service.length ? selected.service.join(', ') : 'None selected');
      set('#summary-formats', selected.format.length ? selected.format.join(', ') : 'None selected');
      set('#summary-marketing', selected.marketing);
      pageCount.value = pages.value;
      pageCount.textContent = pages.value;
      updateEstimate();
    };
    const statusKeys = { 'Final draft (proofread only)': 'proofread', 'Needs light editing': 'light', 'Needs heavy editing': 'heavy', 'Still writing': 'writing' };
    const serviceKeys = {
      'Professional Cover Design': 'cover', 'ISBN & Copyright Assistance': 'isbn',
      'Amazon KDP Publishing': 'kdp', 'IngramSpark Distribution': 'ingramspark',
      'Metadata Optimization': 'metadata', 'Author Website': 'author_website'
    };
    const formatKeys = { 'eBook': 'ebook', 'Paperback': 'paperback', 'Hardcover': 'hardcover' };
    const marketingKeys = { 'None': 'none', 'Launch essentials': 'launch', 'Full launch campaign': 'full', 'Ongoing (3 months)': 'ongoing' };
    const updateEstimate = () => {
      const config = window.beaconPricing || {};
      const heading = text('#estimate-heading');
      const explainer = text('#estimate-explainer');
      const breakdown = text('#estimate-breakdown');
      const note = text('#estimate-note');
      if (!config.enabled || !config.rates || !heading || !breakdown) return;
      const rate = config.rates;
      const required = [rate.editing?.[statusKeys[selected.status]], rate.formatting_per_page];
      selected.service.forEach(label => required.push(rate.services?.[serviceKeys[label]]));
      selected.format.forEach(label => required.push(rate.formats?.[formatKeys[label]]));
      const marketingKey = marketingKeys[selected.marketing];
      if (marketingKey !== 'none') required.push(rate.marketing?.[marketingKey]);
      if (required.some(value => typeof value !== 'number' || !Number.isFinite(value))) {
        heading.textContent = 'Pricing incomplete';
        explainer.textContent = 'Some selected services do not have an approved rate yet. Please request a personalised quote.';
        breakdown.hidden = true;
        if (note) note.textContent = 'Only client-approved prices can be shown here.';
        return;
      }
      const lines = [];
      const add = (label, value) => { if (value > 0) lines.push([label, value]); };
      add(`Editing (${pages.value} pages)`, rate.editing[statusKeys[selected.status]] * Number(pages.value));
      add(`Formatting & interior (${pages.value} pages)`, rate.formatting_per_page * Number(pages.value));
      selected.service.forEach(label => add(label, rate.services[serviceKeys[label]]));
      selected.format.forEach(label => add(`${label} publishing`, rate.formats[formatKeys[label]]));
      if (marketingKey !== 'none') add(selected.marketing, rate.marketing[marketingKey]);
      const currency = config.currency || 'USD';
      const money = amount => new Intl.NumberFormat(undefined, { style: 'currency', currency, maximumFractionDigits: 0 }).format(amount);
      breakdown.replaceChildren(...lines.map(([label, amount]) => {
        const item = document.createElement('li'); const name = document.createElement('span'); const value = document.createElement('strong');
        name.textContent = label; value.textContent = money(amount); item.append(name, value); return item;
      }));
      breakdown.hidden = false;
      heading.textContent = money(lines.reduce((sum, line) => sum + line[1], 0));
      explainer.textContent = 'Itemised estimate based on the selected services and approved rates. Final quotes are confirmed after manuscript review.';
      if (note) note.textContent = 'This is an estimate; the final quotation follows a manuscript review.';
    };
    genre.addEventListener('change', render);
    pages.addEventListener('input', render);
    calculator.querySelectorAll('[data-choice]').forEach(button => button.addEventListener('click', () => {
      const key = button.dataset.choice;
      const value = button.textContent.trim();
      if (key === 'service' || key === 'format') {
        const list = selected[key];
        const index = list.indexOf(value);
        if (index >= 0) list.splice(index, 1); else list.push(value);
        button.classList.toggle('selected', index < 0);
        button.setAttribute('aria-pressed', String(index < 0));
      } else {
        selected[key] = value;
        calculator.querySelectorAll(`[data-choice="${key}"]`).forEach(item => {
          const active = item === button;
          item.classList.toggle('selected', active);
          item.setAttribute('aria-pressed', String(active));
        });
      }
      render();
    }));
    render();
    const quoteLink = document.querySelector('.estimate-card .button');
    quoteLink?.addEventListener('click', () => {
      sessionStorage.setItem('beaconProjectBrief', [
        `Genre: ${genre.value}`, `Pages: ${pages.value}`, `Manuscript: ${selected.status}`,
        `Services: ${selected.service.join(', ') || 'None selected'}`,
        `Formats: ${selected.format.join(', ') || 'None selected'}`, `Marketing: ${selected.marketing}`
      ].join('\n'));
    });
  }

  const contactForm = document.querySelector('.contact-form');
  if (contactForm) {
    const project = contactForm.querySelector('[name="project"]');
    const savedBrief = sessionStorage.getItem('beaconProjectBrief');
    if (savedBrief && project && !project.value) {
      project.value = `Publishing project details\n${savedBrief}\n\nTell us anything else about your book:`;
      sessionStorage.removeItem('beaconProjectBrief');
    }
    contactForm.addEventListener('submit', event => {
      const required = [...contactForm.querySelectorAll('[required]')];
      const valid = required.every(field => field.checkValidity());
      if (!valid) {
        event.preventDefault();
        contactForm.reportValidity();
      }
    });
  }

  const launcher = document.querySelector('.quill-launch');
  const panel = document.querySelector('#quill-panel');
  const log = document.querySelector('#quill-log');
  const chatForm = document.querySelector('#quill-form');
  const input = document.querySelector('#quill-input');
  const close = document.querySelector('.quill-close');
  if (launcher && panel && log && chatForm && input) {
    const toggle = open => {
      panel.hidden = !open;
      launcher.setAttribute('aria-expanded', String(open));
      if (open) input.focus();
    };
    launcher.addEventListener('click', () => toggle(panel.hidden));
    close?.addEventListener('click', () => toggle(false));
    const addMessage = (content, type, url = '') => {
      const bubble = document.createElement('div');
      bubble.className = `chat-message ${type}`;
      if (url && type === 'bot') {
        bubble.append(document.createTextNode(content + ' '));
        const link = document.createElement('a'); link.href = url; link.textContent = 'Learn more →'; bubble.append(link);
      } else bubble.textContent = content;
      log.append(bubble); log.scrollTop = log.scrollHeight;
    };
    const stopWords = new Set(['what','when','where','which','who','does','do','can','the','and','for','are','you','your','about','with','from','how','much','our','have','has','into','this','that','book','please','tell','me']);
    const answer = question => {
      const synonyms = { price: 'cost', prices: 'cost', pricing: 'cost', rates: 'cost', quote: 'cost', quotation: 'cost', formats: 'format', rights: 'ownership', refund: 'cost' };
      const words = question.toLowerCase().match(/[a-z0-9]+/g)?.filter(word => word.length > 2 && !stopWords.has(word)).map(word => synonyms[word] || word) || [];
      const ranked = (window.beaconKnowledge || []).map(entry => {
        const source = `${entry.q} ${entry.a}`.toLowerCase();
        let score = 0;
        words.forEach(word => { if (source.includes(word)) score += entry.q.toLowerCase().includes(word) ? 3 : 1; });
        return { ...entry, score };
      }).sort((a,b) => b.score - a.score);
      if (!ranked.length || ranked[0].score === 0) return {
        answer: 'I couldn’t find that in the information on this website. You can browse the FAQ, visit the relevant page, or contact the team for a personal answer.', url: '/faq'
      };
      return { answer: ranked[0].a, url: ranked[0].url };
    };
    const submit = question => {
      const clean = question.trim(); if (!clean) return;
      log.querySelector('.chat-suggestions')?.remove();
      addMessage(clean, 'user'); input.value = '';
      const result = answer(clean); addMessage(result.answer, 'bot', result.url);
    };
    chatForm.addEventListener('submit', event => { event.preventDefault(); submit(input.value); });
    log.addEventListener('click', event => { const button = event.target.closest('.chat-suggestions button'); if (button) submit(button.textContent); });
  }
})();
