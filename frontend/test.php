<!--admin dashboard-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CargoNet — Customer Dashboard</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter', 'sans-serif'] },
          colors: {
            freight: { 50: '#f0f9ff', 100: '#e0f2fe', 500: '#0ea5e9', 600: '#0284c7', 900: '#0c4a6e', 950: '#082f49' },
            cargo: { amber: '#f59e0b', slate: '#1e293b', dark: '#0f172a', panel: '#111827' }
          }
        }
      }
    }
  </script>

  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-slate-950 text-slate-100 antialiased">

<div id="dashboard-view" data-role="customer" class="min-h-screen bg-slate-950">
  <!-- Sidebar -->
  <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 border-r border-slate-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
    <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800">
      <div class="w-8 h-8 rounded bg-gradient-to-br from-sky-500 to-cyan-600 flex items-center justify-center">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-19.5-3h19.5m-19.5-3h19.5m-19.5-3h19.5M4.5 3h15a2.25 2.25 0 012.25 2.25v.75a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25v-.75A2.25 2.25 0 014.5 3z"/></svg>
      </div>
      <span class="font-bold text-white tracking-tight">CargoNet</span>
    </div>
    <nav class="p-4 space-y-1" id="sidebar-nav">
      <!-- Will be built by JS -->
    </nav>
    <div class="absolute bottom-0 w-full p-4 border-t border-slate-800">
      <div class="flex items-center gap-3 px-3">
        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center text-xs font-bold text-white">JD</div>
        <div class="text-left">
          <div class="text-sm font-medium text-white">John Doe</div>
          <div class="text-xs text-slate-400">Customer</div>
        </div>
        <button onclick="App.logout()" class="ml-auto text-slate-400 hover:text-rose-400" title="Logout">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 12h-6m6 0l-3-3m3 3l-3 3"/></svg>
        </button>
      </div>
    </div>
  </aside>

  <!-- Main -->
  <div id="main-wrap" class="lg:ml-64 min-h-screen flex flex-col transition-all">
    <header class="h-16 bg-slate-900/80 backdrop-blur border-b border-slate-800 flex items-center justify-between px-6 sticky top-0 z-30">
      <div class="flex items-center gap-4">
        <button onclick="App.toggleSidebar()" class="lg:hidden p-2 rounded-md text-slate-300 hover:bg-slate-800">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        </button>
        <h2 id="page-title" class="text-lg font-semibold text-white">Dashboard Overview</h2>
      </div>
      <div class="flex items-center gap-4">
        <div class="relative hidden md:block">
          <span class="absolute left-3 top-2 text-slate-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 101.5 8.438a7.5 7.5 0 0012.306 5.238z"/></svg></span>
          <input type="text" class="pl-9 pr-4 py-1.5 bg-slate-800 border border-slate-700 rounded-md text-sm text-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 w-64" placeholder="Search shipments...">
        </div>
        <button class="relative p-2 text-slate-300 hover:bg-slate-800 rounded-lg">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 5.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a33 0 11-5.714 0"/></svg>
          <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500"></span>
        </button>
      </div>
    </header>
    <main id="main-content" class="flex-1 p-6 overflow-y-auto"></main>
  </div>
</div>

<!-- Modal -->
<div id="modal-overlay" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-start justify-center p-4 pt-20">
  <div id="modal-card" class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden"></div>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>