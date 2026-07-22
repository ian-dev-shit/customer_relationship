/* ============================================================
   APPLICATION LOGIC – Role‑aware
   ============================================================ */

const Icons = {
  user: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
  check: '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>',
  alert: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.949 3.374h14.71c1.73 0 2.813-1.874 1.949-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>',
  doc: '<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>',
};

const Data = {
  customers: [
    { id:'C-8921', company:'Pacific Freight Ltd', contact:'James Chen', region:'APAC', status:'Active', email:'james@pacificfreight.com', lastActivity:'2 hrs ago', revenue:'$1.2M' },
    { id:'C-9022', company:'EuroLogi GmbH', contact:'Anna Schmidt', region:'EMEA', status:'Active', email:'anna@eurologi.de', lastActivity:'5 hrs ago', revenue:'$890K' },
    { id:'C-9103', company:'Atlantic Cargo Co', contact:'Marcus Johnson', region:'AMER', status:'Prospect', email:'marcus@atlanticcargo.com', lastActivity:'1 day ago', revenue:'$0' },
    { id:'C-9144', company:'Nordic Shipping AS', contact:'Erik Solberg', region:'EMEA', status:'Active', email:'erik@nordic.no', lastActivity:'30 mins ago', revenue:'$2.4M' },
    { id:'C-9205', company:'Gulf Waves Transport', contact:'Fatima Al-Rashid', region:'MENA', status:'Inactive', email:'fatima@gulfwaves.ae', lastActivity:'2 weeks ago', revenue:'$430K' },
    { id:'C-9316', company:'Saigon Logistics', contact:'Minh Tran', region:'APAC', status:'Active', email:'minh@sglogistics.vn', lastActivity:'4 hrs ago', revenue:'$670K' },
    { id:'C-9407', company:'Panama Route Inc', contact:'Carlos Mendez', region:'LATAM', status:'Prospect', email:'carlos@panamaroute.com', lastActivity:'3 days ago', revenue:'$0' },
    { id:'C-9518', company:'Toronto Supply Chain', contact:'Sarah O\'Connor', region:'AMER', status:'Active', email:'sarah@torontosc.ca', lastActivity:'6 hrs ago', revenue:'$1.5M' },
  ],
  contracts: [
    { id:'SLA-2026-001', customer:'Pacific Freight Ltd', type:'Ocean Freight', start:'2026-01-01', end:'2026-12-31', health:99.2, status:'Healthy' },
    { id:'SLA-2026-045', customer:'EuroLogi GmbH', type:'Air Express', start:'2026-03-01', end:'2027-02-28', health:97.5, status:'Warning' },
    { id:'SLA-2026-112', customer:'Nordic Shipping AS', type:'Multimodal', start:'2025-11-01', end:'2026-10-31', health:99.8, status:'Healthy' },
    { id:'SLA-2025-089', customer:'Gulf Waves Transport', type:'Road Haulage', start:'2025-06-15', end:'2026-06-14', health:94.1, status:'Critical' },
    { id:'SLA-2026-203', customer:'Saigon Logistics', type:'Ocean Freight', start:'2026-04-01', end:'2027-03-31', health:98.9, status:'Healthy' },
    { id:'SLA-2026-301', customer:'Toronto Supply Chain', type:'Rail & Intermodal', start:'2026-05-01', end:'2027-04-30', health:96.4, status:'Warning' },
  ],
  documents: [
    { name:'BOL-2026-7782.pdf', type:'Bill of Lading', status:'Verified', date:'2026-07-14', size:'1.2 MB' },
    { name:'INV-44921-A.pdf', type:'Commercial Invoice', status:'Verified', date:'2026-07-14', size:'0.8 MB' },
    { name:'COO-APAC-112.pdf', type:'Certificate of Origin', status:'Pending', date:'2026-07-15', size:'1.5 MB' },
    { name:'CUS-DECL-0092.pdf', type:'Customs Declaration', status:'Verified', date:'2026-07-12', size:'2.1 MB' },
    { name:'SLA-EURO-045.pdf', type:'Contract Annex', status:'Expired', date:'2025-12-01', size:'0.4 MB' },
    { name:'PACK-LIST-991.pdf', type:'Packing List', status:'Verified', date:'2026-07-15', size:'0.9 MB' },
    { name:'INS-HV-556.pdf', type:'Insurance Certificate', status:'Pending', date:'2026-07-16', size:'1.1 MB' },
  ],
  notifications: [
    { id:1, title:'SLA Breach Alert', message:'Gulf Waves Transport Road Haulage is below 95% threshold.', type:'alert', time:'10 min ago', read:false },
    { id:2, title:'Document Verified', message:'Bill of Lading BOL-2026-7782 fully verified by customs API.', type:'success', time:'32 min ago', read:false },
    { id:3, title:'Contract Renewal', message:'EuroLogi GmbH Air Express expires in 45 days.', type:'warning', time:'2 hrs ago', read:true },
    { id:4, title:'New Portal Message', message:'Nordic Shipping AS uploaded3 new compliance docs.', type:'info', time:'4 hrs ago', read:true },
    { id:5, title:'System Maintenance', message:'Scheduled BI analytics downtime at 02:00 UTC.', type:'system', time:'6 hrs ago', read:true },
  ],
  chartRefs: []
};

const App = {
  state: {
    view: 'home',
    sidebarOpen: false,
    modalHtml: ''
  },

  // Determine user role from data-attribute
  getRole() {
    const el = document.getElementById('dashboard-view');
    return el ? el.dataset.role : 'customer';
  },

  // Build sidebar links based on role
  buildSidebar() {
    const nav = document.getElementById('sidebar-nav');
    if (!nav) return;

    const role = this.getRole();
    const isAdmin = role === 'admin';

    // Define link configurations
    const links = [
      { id: 'home', label: 'Dashboard', icon: '<path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>' },
      { id: 'crm', label: 'CRM', icon: '<path d="M15 19.128a9.38 9.38 0 002.625.168 9.373 9.373 0 002.625-.168m-15.75 3.75a9.375 9.375 0 018.25-16.485V6a2.25 2.25 0 012.25 2.25v.894a9.375 9.375 0 018.25 16.485m-13.5-9h13.5m-13.5 3h13.5m-13.5 3h13.5m-13.5 3h13.5"/>' },
      { id: 'contracts', label: 'Contract & SLA', icon: '<path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0a1.5 1.5 0 00-1.0612.306A8.97 8.97 0 0112 11.25c.9920 1.953-.138 2.864-.395m-5.8 0a8.965 8.965 0 01-2.864-1.395M6.75 15.75v.75a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25v-.75"/>' },
      { id: 'docs', label: 'E-Docs & Compliance', icon: '<path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a99 0 00-9-9z"/>' },
      { id: 'analytics', label: 'BI & Analytics', icon: '<path d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0120.25 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>' },
      { id: 'portal', label: 'Portal & Hub', icon: '<path d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.748 0-5.287.82-7.414 2.238"/>' },
    ];

    // For customers, keep only 'home'
    const filtered = isAdmin ? links : links.filter(l => l.id === 'home');

    let html = '';
    filtered.forEach(link => {
      const active = link.id === 'home' ? 'active' : '';
      html += `
        <button onclick="App.nav('${link.id}')" data-nav="${link.id}" class="sidebar-link ${active} w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800/50 transition">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">${link.icon}</svg>
          ${link.label}
        </button>
      `;
    });
    nav.innerHTML = html;
  },

  init() {
    this.buildSidebar();
    this.nav('home');
  },

  logout() {
    if (confirm('End session and return to secure login?')) {
      window.location.href = 'login.php';
    }
  },

  toggleSidebar() {
    const sb = document.getElementById('sidebar');
    sb.classList.toggle('-translate-x-full');
  },

  nav(view) {
    this.state.view = view;
    // Update sidebar active state
    document.querySelectorAll('.sidebar-link').forEach(el => {
      if (el.dataset.nav === view) { el.classList.add('active'); }
      else { el.classList.remove('active'); }
    });

    const titles = {
      home: 'Dashboard Overview',
      crm: 'Customer Relationship Management',
      contracts: 'Contract & SLA Monitoring',
      docs: 'E-Documentation & Compliance',
      analytics: 'Business Intelligence & Freight Analytics',
      portal: 'Customer Portal & Notification Hub'
    };
    document.getElementById('page-title').innerText = titles[view] || 'Dashboard';
    this.destroyCharts();
    this.render(view);
  },

  destroyCharts() {
    Data.chartRefs.forEach(c => c.destroy());
    Data.chartRefs = [];
  },

  render(view) {
    const main = document.getElementById('main-content');
    main.innerHTML = '';
    const wrap = document.createElement('div');
    wrap.className = 'fade-in';
    if (view === 'home') wrap.innerHTML = Views.home();
    else if (view === 'crm') wrap.innerHTML = Views.crm();
    else if (view === 'contracts') wrap.innerHTML = Views.contracts();
    else if (view === 'docs') wrap.innerHTML = Views.docs();
    else if (view === 'analytics') wrap.innerHTML = Views.analytics();
    else if (view === 'portal') wrap.innerHTML = Views.portal();
    main.appendChild(wrap);
    if (view === 'analytics') setTimeout(() => Charts.init(), 50);
  },

  openModal(html) {
    document.getElementById('modal-card').innerHTML = `<div class="p-6 relative">
      <button onclick="App.closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
      ${html}
    </div>`;
    document.getElementById('modal-overlay').classList.remove('hidden');
  },
  closeModal() {
    document.getElementById('modal-overlay').classList.add('hidden');
  },

  openCustomerModal(customerId) {
    const customer = Data.customers.find(c => c.id === customerId);
    if (customer) {
      this.openModal(Views.customerModal(customer));
    }
  },

  badge(status) {
    const map = {
      Active: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
      Prospect: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
      Inactive: 'bg-slate-500/10 text-slate-400 border-slate-500/20',
      Healthy: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
      Warning: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
      Critical: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
      Verified: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
      Pending: 'bg-amber-500/10 text-amber-400 border-amber-500/20',
      Expired: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
      alert: 'bg-rose-500/10 text-rose-400 border-rose-500/20',
      success: 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
      info: 'bg-sky-500/10 text-sky-400 border-sky-500/20',
      system: 'bg-slate-500/10 text-slate-300 border-slate-500/20',
    };
    return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${map[status] || map.info}">${status}</span>`;
  }
};

/* ============================================================
   VIEW TEMPLATES (same as before – keep all Views)
   ============================================================ */
const Views = {
  home() {
    return `
      <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-sky-900/40 to-slate-900 border border-sky-500/20">
        <h1 class="text-2xl font-bold text-white mb-1">Welcome back, ${App.getRole() === 'admin' ? 'Alex' : 'John'}</h1>
        <p class="text-slate-300 text-sm">Today is ${new Date().toLocaleDateString('en-US',{weekday:'long', year:'numeric', month:'long', day:'numeric'})}. ${App.getRole() === 'admin' ? 'You have <span class="text-amber-400 font-semibold">2 SLA alerts</span> requiring attention.' : 'Your shipments are on track.'}</p>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        ${[
          {t:'Total Active Clients', v:'486', c:'text-emerald-400', icon:'<path d="M15 19.128a9.38 9.38 0 002.625.168 9.373 9.373 0 002.625-.168m-15.75 3.75a9.375 9.375 0 018.25-16.485V6a2.25 2.25 0 012.25 2.25v.894a9.375 9.375 0 018.25 16.485m-13.5-9h13.5m-13.5 3h13.5m-13.5 3h13.5m-13.5 3h13.5"/>'},
          {t:'Pending Renewals', v:'12', c:'text-amber-400', icon:'<path d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.25 2.25 0 0113.35 1.5h1.5a2.25 2.25 0 012.25 2.25V6a2.25 2.25 0 01-2.25 2.25h-1.5a2.25 2.25 0 01-2.25-2.25V2.25m-5.8 0A2.25 2.25 0 004.05 1.5h-1.5a2.25 2.25 0 00-2.25 2.25V6a2.25 2.25 0 002.25 2.25h1.5a2.25 2.25 0 002.25-2.25V2.25z"/>'},
          {t:'Weekly Shipments', v:'1,284', c:'text-sky-400', icon:'<path d="M2.25 21h19.5m-19.5-3h19.5m-19.5-3h19.5m-19.5-3h19.5M4.5 3h15a2.25 2.25 0 012.25 2.25v.75a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25v-.75A2.25 2.25 0 014.5 3z"/>'}
        ].map(k => `
          <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-start justify-between hover:border-slate-700 transition">
            <div><p class="text-slate-400 text-sm mb-1">${k.t}</p><p class="text-3xl font-bold ${k.c}">${k.v}</p></div>
            <div class="p-2.5 rounded-lg bg-slate-800 text-slate-300"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">${k.icon}</svg></div>
          </div>
        `).join('')}
      </div>
      <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-xl p-5">
          <h3 class="font-semibold text-white mb-4">Recent Activity</h3>
          <div class="space-y-3">
            ${[
              {a:'BOL-2026-7782 verified for Pacific Freight', t:'32 min ago', c:'text-emerald-400'},
              {a:'SLA threshold alert: Gulf Waves Transport', t:'2 hrs ago', c:'text-rose-400'},
              {a:'Contract renewal draft sent to EuroLogi', t:'4 hrs ago', c:'text-sky-400'},
              {a:'New customer portal access: Saigon Logistics', t:'5 hrs ago', c:'text-amber-400'},
            ].map(i => `
              <div class="flex items-center justify-between p-3 rounded-lg bg-slate-800/50 border border-slate-700/50">
                <span class="text-sm text-slate-200">${i.a}</span>
                <span class="text-xs ${i.c}">${i.t}</span>
              </div>
            `).join('')}
          </div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
          <h3 class="font-semibold text-white mb-4">Quick Actions</h3>
          <div class="space-y-2">
            <button class="w-full text-left px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm text-slate-200 transition flex items-center gap-3">
              <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M124.5v15m7.5-7.5h-15"/></svg> Create Shipment
            </button>
            <button class="w-full text-left px-4 py-3 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 text-sm text-slate-200 transition flex items-center gap-3">
              <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg> Upload Document
            </button>
          </div>
        </div>
      </div>
    `;
  },

  crm() {
    const statusFilter = `<div class="flex gap-2 mb-4">
      <button class="px-3 py-1.5 rounded-md bg-sky-600 text-white text-xs font-medium">All</button>
      <button class="px-3 py-1.5 rounded-md bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-medium">Active</button>
      <button class="px-3 py-1.5 rounded-md bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-medium">Prospects</button>
    </div>`;
    const rows = Data.customers.map(c => `
      <tr class="hover:bg-slate-800/50 transition border-b border-slate-800 last:border-0">
        <td class="px-4 py-3 text-sm font-medium text-white">${c.company}</td>
        <td class="px-4 py-3 text-sm text-slate-300"><div>${c.contact}</div><div class="text-xs text-slate-500">${c.email}</div></td>
        <td class="px-4 py-3 text-sm text-slate-300">${c.region}</td>
        <td class="px-4 py-3">${App.badge(c.status)}</td>
        <td class="px-4 py-3 text-sm text-slate-400">${c.lastActivity}</td>
        <td class="px-4 py-3 text-sm text-slate-300 font-mono">${c.revenue}</td>
        <td class="px-4 py-3 text-right">
          <button data-customer-id="${c.id}" class="view-customer-btn text-sky-400 hover:text-sky-300 text-sm font-medium">View</button>
        </td>
      </tr>
    `).join('');
    return `
      <div class="flex items-center justify-between mb-6">
        <div><h2 class="text-xl font-bold text-white">Customer Directory</h2><p class="text-sm text-slate-400 mt-1">Manage accounts, contacts, and pipeline status.</p></div>
        <button class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium transition">+ Add Customer</button>
      </div>
      ${statusFilter}
      <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
          <thead><tr class="bg-slate-800/60 text-slate-400 text-xs uppercase tracking-wider">
            <th class="px-4 py-3 font-medium">Company</th><th class="px-4 py-3 font-medium">Primary Contact</th>
            <th class="px-4 py-3 font-medium">Region</th><th class="px-4 py-3 font-medium">Status</th>
            <th class="px-4 py-3 font-medium">Last Activity</th><th class="px-4 py-3 font-medium">LTV</th>
            <th class="px-4 py-3 font-medium text-right">Action</th>
          </tr></thead>
          <tbody class="divide-y divide-slate-800">${rows}</tbody>
        </table>
      </div>
    `;
  },

  customerModal(c) {
    return `
      <div>
        <h3 class="text-lg font-bold text-white mb-1">${c.company}</h3>
        <p class="text-sm text-slate-400 mb-5">ID: ${c.id} · Region: ${c.region}</p>
        <div class="grid sm:grid-cols-2 gap-4 mb-5">
          <div class="p-3 rounded-lg bg-slate-800 border border-slate-700">
            <div class="text-xs text-slate-400 mb-1">Primary Contact</div>
            <div class="text-sm text-white font-medium">${c.contact}</div>
            <div class="text-xs text-slate-400">${c.email}</div>
          </div>
          <div class="p-3 rounded-lg bg-slate-800 border border-slate-700">
            <div class="text-xs text-slate-400 mb-1">Relationship Value</div>
            <div class="text-sm text-white font-medium">${c.revenue}</div>
            <div class="text-xs text-slate-400">Annual contribution</div>
          </div>
        </div>
        <h4 class="text-sm font-semibold text-white mb-2">Contact History</h4>
        <div class="space-y-2 mb-4">
          <div class="flex items-start gap-3 p-3 rounded bg-slate-800/50 border border-slate-700">
            <div class="mt-0.5 text-sky-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H6A2.25 2.25 0 003.75 6v10.5A2.25 2.25 0 006 18.75z"/></svg></div>
            <div><div class="text-xs text-slate-300">Sent rate quote for Q3 ocean freight</div><div class="text-xs text-slate-500">2 hours ago</div></div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded bg-slate-800/50 border border-slate-700">
            <div class="mt-0.5 text-emerald-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 3.75v4.5m0-4.5h-4.5m4.5 0l-6 6m3 12c-8.284 0-15-6.716-15-15V4.5A2.25 2.25 0 014.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 2.573m0 0l6.012-1.506a2.25 2.25 0 011.095.073l2.527.758m-9.684 4.528l.6181.527c.193.474.64.79 1.144.79h2.967c.47 0 .898.266 1.1.686l.966 1.932m-6.584 2.675l5.01-1.253"/></svg></div>
            <div><div class="text-xs text-slate-300">Voice call — logistics alignment</div><div class="text-xs text-slate-500">Yesterday</div></div>
          </div>
        </div>
        <div class="flex justify-end gap-2">
          <button onclick="App.closeModal()" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm">Close</button>
          <button class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm">View Full Profile</button>
        </div>
      </div>
    `;
  },

  contracts() {
    const cards = Data.contracts.map(c => {
      let color = c.health > 98 ? 'stroke-emerald-400' : c.health > 95 ? 'stroke-amber-400' : 'stroke-rose-500';
      return `
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 hover:border-slate-700 transition">
        <div class="flex items-start justify-between mb-4">
          <div><div class="text-xs text-slate-500 font-mono mb-1">${c.id}</div><h4 class="text-white font-semibold">${c.customer}</h4><div class="text-sm text-slate-400 mt-0.5">${c.type}</div></div>
          <div class="w-14 h-14 shrink-0">
            <svg class="w-14 h-14" viewBox="0 0 100 100">
              <circle cx="50" cy="50" r="40" fill="none" stroke="#1e293b" stroke-width="8"/>
              <circle cx="50" cy="50" r="40" fill="none" class="${color} progress-ring__circle" stroke-width="8" stroke-dasharray="251.2" stroke-dashoffset="${251.2 - (251.2 * c.health / 100)}" stroke-linecap="round"/>
              <text x="50" y="55" text-anchor="middle" fill="white" font-size="22" font-weight="bold">${Math.round(c.health)}%</text>
            </svg>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-4">
          <div class="text-xs text-slate-400">Start: <span class="text-slate-200">${c.start}</span></div>
          <div class="text-xs text-slate-400">End: <span class="text-slate-200">${c.end}</span></div>
        </div>
        <div class="flex items-center justify-between">
          ${App.badge(c.status)}
          <span class="text-xs text-slate-500 bg-slate-800 px-2 py-1 rounded">${c.health > 98 ? 'Within SLA' : c.health > 95 ? 'Review Advised' : 'Breach Risk'}</span>
        </div>
      </div>
      `;
    }).join('');
    return `
      <div class="flex items-center justify-between mb-6">
        <div><h2 class="text-xl font-bold text-white">Contract & SLA Monitoring</h2><p class="text-sm text-slate-400 mt-1">Real-time service level health and renewal pipeline.</p></div>
        <div class="flex gap-2"><button class="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm border border-slate-700">Export CSV</button><button class="px-3 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm">+ New Contract</button></div>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-center gap-4">
          <div class="p-3 rounded-lg bg-sky-500/10 text-sky-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0a1.5 1.5 0 00-1.061 2.306A8.97 8.97 0 0112 11.25c.992 0 1.953-.138 2.864-.395m-5.8 0a8.965 8.965 0 01-2.864-1.395M6.75 15.75v.75a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25v-.75"/></svg></div>
          <div><div class="text-sm text-slate-400">Active Contracts</div><div class="text-2xl font-bold text-white">42</div></div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-center gap-4">
          <div class="p-3 rounded-lg bg-emerald-500/10 text-emerald-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg></div>
          <div><div class="text-sm text-slate-400">Avg SLA Health</div><div class="text-2xl font-bold text-emerald-400">97.7%</div></div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-center gap-4">
          <div class="p-3 rounded-lg bg-amber-500/10 text-amber-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
          <div><div class="text-sm text-slate-400">Renewals (30d)</div><div class="text-2xl font-bold text-amber-400">3</div></div>
        </div>
      </div>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">${cards}</div>
    `;
  },

  docs() {
    const complianceScore = 94;
    const checklist = [
      { label: 'Customs Declarations Filed', ok: true },
      { label: 'BOL Digital Verification', ok: true },
      { label: 'Certificate of Origin Uploaded', ok: false },
      { label: 'Insurance Active for All Open Cargo', ok: true },
      { label: 'GDPR Data Retention Policy', ok: true },
      { label: 'IATA Dangerous Goods Cert', ok: false },
    ];
    const docs = Data.documents.map(d => `
      <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 hover:border-slate-700 transition flex items-start gap-3">
        <div class="p-2 rounded-lg bg-slate-800 text-slate-300 shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg></div>
        <div class="flex-1 min-w-0"><div class="text-sm font-medium text-white truncate">${d.name}</div><div class="text-xs text-slate-400 mt-0.5">${d.type} · ${d.size}</div><div class="flex items-center gap-2 mt-2">${App.badge(d.status)}<span class="text-xs text-slate-500">${d.date}</span></div></div>
      </div>
    `).join('');
    return `
      <div class="flex items-center justify-between mb-6"><h2 class="text-xl font-bold text-white">E-Documentation & Compliance</h2><button class="px-3 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm">+ Request Document</button></div>
      <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
          <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex flex-col items-center">
            <h3 class="text-sm font-semibold text-white mb-3 w-full text-left">Compliance Score</h3>
            <div class="relative w-32 h-32 mb-3">
              <svg class="w-full h-full" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="42" fill="none" stroke="#1e293b" stroke-width="10"/>
                <circle cx="50" cy="50" r="42" fill="none" stroke="${complianceScore>=90?'#10b981':complianceScore>=75?'#f59e0b':'#f43f5e'}" stroke-width="10" stroke-dasharray="263.9" stroke-dashoffset="${263.9-(263.9*complianceScore/100)}" stroke-linecap="round" class="progress-ring__circle"/>
                <text x="50" y="55" text-anchor="middle" fill="white" font-size="24" font-weight="bold">${complianceScore}%</text>
              </svg>
            </div>
            <p class="text-xs text-slate-400 text-center leading-relaxed">Score based on document recency, verification status, and regulatory filing compliance.</p>
          </div>
          <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-white mb-3">Compliance Checklist</h3>
            <div class="space-y-2">${checklist.map(ch => `<label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-800/50 transition cursor-pointer"><input type="checkbox" ${ch.ok?'checked':''} disabled class="w-4 h-4 rounded border-slate-600 ${ch.ok?'text-emerald-500':'text-slate-600'}"><span class="text-sm ${ch.ok?'text-slate-300':'text-slate-500'}">${ch.label}</span></label>`).join('')}</div>
          </div>
        </div>
        <div class="lg:col-span-2">
          <div class="bg-slate-900 border border-dashed border-slate-700 rounded-xl p-8 text-center mb-6 hover:bg-slate-800/30 transition cursor-pointer">
            <svg class="w-8 h-8 text-slate-500 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M316.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            <p class="text-sm text-slate-300 font-medium">Drag files here or click to upload</p><p class="text-xs text-slate-500 mt-1">Supports PDF, DOCX, PNG scans up to 25MB</p>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">${docs}</div>
        </div>
      </div>
    `;
  },

  analytics() {
    return `
      <div class="flex items-center justify-between mb-6">
        <div><h2 class="text-xl font-bold text-white">Business Intelligence & Freight Analytics</h2><p class="text-sm text-slate-400 mt-1">Operational metrics, trends, and regional performance.</p></div>
        <select class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2 focus:ring-sky-500"><option>Last 30 Days</option><option>Last 90 Days</option><option>YTD 2026</option></select>
      </div>
      <div class="grid md:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4"><div class="text-xs text-slate-400 mb-1">Total Shipment Volume</div><div class="text-2xl font-bold text-white">4,291</div><div class="text-xs text-emerald-400 mt-1">+12.4% vs last period</div></div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4"><div class="text-xs text-slate-400 mb-1">Avg Transit Time</div><div class="text-2xl font-bold text-white">4.2 days</div><div class="text-xs text-emerald-400 mt-1">-0.3 days improvement</div></div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4"><div class="text-xs text-slate-400 mb-1">Revenue / KM</div><div class="text-2xl font-bold text-white">$0.82</div><div class="text-xs text-amber-400 mt-1">+2.1% fuel adjusted</div></div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4"><div class="text-xs text-slate-400 mb-1">Customer NPS</div><div class="text-2xl font-bold text-white">72</div><div class="text-xs text-emerald-400 mt-1">+4 pts this quarter</div></div>
      </div>
      <div class="grid lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5"><h4 class="text-sm font-semibold text-white mb-3">Shipment Volume Trend</h4><div class="chart-container"><canvas id="chart-volume"></canvas></div></div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5"><h4 class="text-sm font-semibold text-white mb-3">Revenue by Transport Mode</h4><div class="chart-container"><canvas id="chart-mode"></canvas></div></div>
      </div>
      <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5"><h4 class="text-sm font-semibold text-white mb-3">On-Time Performance</h4><div class="chart-container"><canvas id="chart-otp"></canvas></div></div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5"><h4 class="text-sm font-semibold text-white mb-3">Regional Distribution</h4><div class="chart-container"><canvas id="chart-region"></canvas></div></div>
      </div>
    `;
  },

  portal() {
    const notifs = Data.notifications.map(n => `
      <div class="flex items-start gap-3 p-3 rounded-lg ${n.read?'bg-slate-800/30':'bg-slate-800 border border-slate-700'} transition">
        <div class="mt-0.5 ${n.type==='alert'?'text-rose-400':n.type==='success'?'text-emerald-400':n.type==='warning'?'text-amber-400':'text-sky-400'}">
          ${n.type==='alert'?Icons.alert:(n.type==='success'?'<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>':n.type==='warning'?'<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.949 3.374h14.71c1.73 0 2.813-1.874 1.949-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>':'<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>')}
        </div>
        <div class="flex-1"><div class="flex items-center justify-between"><span class="text-sm font-medium text-white">${n.title}</span><span class="text-xs text-slate-500">${n.time}</span></div><p class="text-xs text-slate-300 mt-0.5">${n.message}</p></div>
        ${!n.read?'<span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-1.5"></span>':''}
      </div>
    `).join('');
    return `
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-white">Customer Portal & Notification Hub</h2>
        <button onclick="App.openModal(\`${Views.announceModal()}\`)" class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm font-medium">+ Post Announcement</button>
      </div>
      <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-4">
          <div class="bg-slate-900 border border-slate-800 rounded-xl p-5"><h3 class="text-sm font-semibold text-white mb-3">Notification Center</h3><div class="space-y-2">${notifs}</div></div>
        </div>
        <div class="space-y-6">
          <div class="bg-slate-900 border border-slate-800 rounded-xl p-5"><h3 class="text-sm font-semibold text-white mb-3">Customer Activity</h3><div class="space-y-3">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-sky-500/20 text-sky-300 flex items-center justify-center text-xs font-bold">NS</div><div><div class="text-sm text-white">Nordic Shipping AS</div><div class="text-xs text-slate-400">Downloaded 3 invoices</div></div></div>
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-xs font-bold">PF</div><div><div class="text-sm text-white">Pacific Freight Ltd</div><div class="text-xs text-slate-400">Signed digital POD</div></div></div>
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-amber-500/20 text-amber-300 flex items-center justify-center text-xs font-bold">EL</div><div><div class="text-sm text-white">EuroLogi GmbH</div><div class="text-xs text-slate-400">Viewed SLA dashboard</div></div></div>
          </div></div>
          <div class="bg-slate-900 border border-slate-800 rounded-xl p-5"><h3 class="text-sm font-semibold text-white mb-3">System Status</h3><div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-slate-400">API Gateway</span><span class="text-emerald-400 font-medium">Operational</span></div>
            <div class="flex justify-between"><span class="text-slate-400">Document OCR</span><span class="text-emerald-400 font-medium">Operational</span></div>
            <div class="flex justify-between"><span class="text-slate-400">EDI Partners</span><span class="text-amber-400 font-medium">Degraded</span></div>
            <div class="flex justify-between"><span class="text-slate-400">BI Warehouse</span><span class="text-emerald-400 font-medium">Operational</span></div>
          </div></div>
        </div>
      </div>
    `;
  },

  announceModal() {
    return `
      <h3 class="text-lg font-bold text-white mb-1">Post Portal Announcement</h3>
      <p class="text-sm text-slate-400 mb-4">Broadcast a message to all customer portal users.</p>
      <div class="space-y-3 mb-5">
        <div><label class="block text-xs font-medium text-slate-300 mb-1">Audience</label><select class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200"><option>All Portal Users</option><option>Active Freight Clients</option><option>Partner Forwarders</option></select></div>
        <div><label class="block text-xs font-medium text-slate-300 mb-1">Subject</label><input type="text" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200" placeholder="e.g. Q3 Service Update"></div>
        <div><label class="block text-xs font-medium text-slate-300 mb-1">Message</label><textarea rows="4" class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-200" placeholder="Type announcement..."></textarea></div>
      </div>
      <div class="flex justify-end gap-2">
        <button onclick="App.closeModal()" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm">Cancel</button>
        <button onclick="App.closeModal(); setTimeout(()=>alert('Announcement published to Customer Portal.'),200)" class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-sm">Publish</button>
      </div>
    `;
  }
};

/* ============================================================
   CHARTS
   ============================================================ */
const Charts = {
  init() {
    const cfg = (type, labels, data, colors) => ({
      type,
      data: { labels, datasets: [{ data, backgroundColor: colors, borderColor: colors.map(c=>c), borderWidth: 1 }] },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color:'#94a3b8' } } }, scales: type!=='doughnut' && type!=='polarArea' ? { y: { grid:{color:'#1e293b'}, ticks:{color:'#94a3b8'} }, x: { grid:{display:false}, ticks:{color:'#94a3b8'} } } : {} }
    });

    const c1 = new Chart(document.getElementById('chart-volume'), cfg('line', ['Jun 16','Jun 20','Jun 24','Jun 28','Jul 2','Jul 6','Jul 10','Jul 14'], [120,132,128,145,160,155,170,182], ['#0ea5e9']));
    c1.data.datasets[0].fill = true;
    c1.data.datasets[0].backgroundColor = 'rgba(14,165,233,0.15)';
    c1.data.datasets[0].tension = 0.4;
    c1.update();

    const c2 = new Chart(document.getElementById('chart-mode'), cfg('bar', ['Ocean','Air','Road','Rail','Intermodal'], [420,310,280,95,150], ['#0ea5e9','#06b6d4','#10b981','#f59e0b','#6366f1']));
    const c3 = new Chart(document.getElementById('chart-otp'), cfg('doughnut', ['On-Time','Minor Delay','Significant Delay'], [82,12,6], ['#10b981','#f59e0b','#f43f5e']));
    const c4 = new Chart(document.getElementById('chart-region'), cfg('polarArea', ['APAC','EMEA','AMER','MENA','LATAM'], [35,28,22,9,6], ['#0ea5e9','#06b6d4','#10b981','#f59e0b','#8b5cf6']));

    Data.chartRefs.push(c1,c2,c3,c4);
  }
};

/* ============================================================
   INITIALIZATION
   ============================================================ */
document.addEventListener('DOMContentLoaded', function() {
  App.init();

  // Event delegation for CRM "View" buttons
  document.addEventListener('click', function(e) {
    const target = e.target.closest('.view-customer-btn');
    if (target) {
      const customerId = target.getAttribute('data-customer-id');
      if (customerId) {
        App.openCustomerModal(customerId);
      }
    }
  });
});

// Escape key to close modal
document.addEventListener('keydown', e => { if(e.key==='Escape') App.closeModal(); });