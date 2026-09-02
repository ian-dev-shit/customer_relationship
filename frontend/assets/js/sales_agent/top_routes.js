let map;

// Pagination Limit: 5 items per page
const ITEMS_PER_PAGE = 5;

let routesData = [];
let currentRoutesPage = 1;

let customersData = [];
let currentCustomersPage = 1;

document.addEventListener("DOMContentLoaded", () => {
  initMap();
  fetchLeadsAndRoutes();
  fetchTopCustomers();
});

function initMap() {
  map = L.map('routes-map', {
    zoomControl: false,
    attributionControl: false
  }).setView([13.0000, 122.0000], 3);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
  }).addTo(map);

  L.control.zoom({ position: 'bottomleft' }).addTo(map);
}

const hubCoords = {
  "manila": [14.5995, 120.9842],
  "china": [31.2304, 121.4737],
  "australia": [-33.8688, 151.2093],
  "malaysia": [3.1390, 101.6869],
  "indonesia": [-6.2088, 106.8456],
  "singapore": [1.3521, 103.8198],
  "usa": [40.7128, -74.0060],
  "japan": [35.6762, 139.6503]
};

async function fetchLeadsAndRoutes() {
  const statusContainer = document.getElementById('leads-status-container');
  const routesContainer = document.getElementById('top-routes-container');

  try {
    const response = await fetch('http://127.0.0.1:8000/api/sales/leads-and-routes');
    if (!response.ok) throw new Error("HTTP error " + response.status);
    const res = await response.json();

    // 1. Direct Render sa Leads Management (Walang Pagination)
    if (res.lead_statuses && res.lead_statuses.length > 0 && statusContainer) {
      let statusHTML = '';
      res.lead_statuses.forEach(item => {
        statusHTML += `
          <div class="flex items-center justify-between text-xs">
            <span class="w-28 font-medium text-gray-600 truncate">${item.label}</span>
            <div class="flex-1 mx-3 bg-gray-100 h-2.5 rounded-full overflow-hidden">
              <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: ${item.percentage}%"></div>
            </div>
            <span class="w-8 text-right font-bold text-gray-800">${item.percentage}%</span>
          </div>
        `;
      });
      statusContainer.innerHTML = statusHTML;
    }

    // 2. Paginated Top Routes Render
    routesData = res.top_routes || [];
    currentRoutesPage = 1;
    renderRoutesPage();

    // Map Markers
    if (routesData.length > 0) {
      routesData.forEach(item => {
        const routeLower = item.route.toLowerCase();
        for (const [key, coords] of Object.entries(hubCoords)) {
          if (routeLower.includes(key)) {
            L.circleMarker(coords, {
              color: '#6366f1',
              fillColor: '#818cf8',
              fillOpacity: 0.8,
              radius: 6
            }).addTo(map).bindTooltip(item.route);
          }
        }
      });
    }

  } catch (err) {
    console.error("Fetch Error:", err);
    if (statusContainer) statusContainer.innerHTML = `<div class="text-xs text-rose-500 text-center py-4">Failed to load status data</div>`;
    if (routesContainer) routesContainer.innerHTML = `<div class="text-xs text-rose-500 text-center py-4">Failed to load routes data</div>`;
  }
}

// TOP ROUTES PAGINATION FUNCTIONS
function renderRoutesPage() {
  const container = document.getElementById('top-routes-container');
  if (!container) return;

  if (routesData.length === 0) {
    container.innerHTML = `<div class="text-xs text-gray-400 text-center py-4">No routes data</div>`;
    updatePaginationUI('routes', 0, 1);
    return;
  }

  const totalPages = Math.ceil(routesData.length / ITEMS_PER_PAGE);
  const startIndex = (currentRoutesPage - 1) * ITEMS_PER_PAGE;
  const pageItems = routesData.slice(startIndex, startIndex + ITEMS_PER_PAGE);

  let routeHTML = '';
  pageItems.forEach(item => {
    routeHTML += `
      <div class="flex items-center justify-between text-xs p-1 hover:bg-gray-50 rounded-lg transition-colors">
        <div class="flex items-center gap-2.5">
          <span class="font-bold text-gray-400 w-3">${item.rank}</span>
          <span class="p-1 bg-indigo-50 text-indigo-600 rounded">🚢</span>
          <span class="font-semibold text-gray-800 truncate max-w-[110px]">${item.route}</span>
        </div>
        <span class="font-bold text-gray-900">${item.percentage}</span>
      </div>
    `;
  });

  container.innerHTML = routeHTML;
  updatePaginationUI('routes', totalPages, currentRoutesPage);
}

function changeRoutesPage(dir) {
  const totalPages = Math.ceil(routesData.length / ITEMS_PER_PAGE);
  if (currentRoutesPage + dir >= 1 && currentRoutesPage + dir <= totalPages) {
    currentRoutesPage += dir;
    renderRoutesPage();
  }
}

// TOP CUSTOMERS FETCH & PAGINATION FUNCTIONS
async function fetchTopCustomers() {
  const container = document.getElementById('top-customers-list');
  if (!container) return;

  try {
    const response = await fetch('http://127.0.0.1:8000/api/sales/top-customers');
    if (!response.ok) throw new Error("HTTP error " + response.status);
    const data = await response.json();

    customersData = data.customers || [];
    currentCustomersPage = 1;
    renderCustomersPage();

  } catch (err) {
    console.error("Top Customers Error:", err);
    container.innerHTML = `<div class="p-4 text-center text-xs text-rose-500">Failed to load top customers</div>`;
    updatePaginationUI('customers', 0, 1);
  }
}

function renderCustomersPage() {
  const container = document.getElementById('top-customers-list');
  if (!container) return;

  if (customersData.length === 0) {
    container.innerHTML = `<div class="p-4 text-center text-xs text-gray-400">No customers found</div>`;
    updatePaginationUI('customers', 0, 1);
    return;
  }

  const totalPages = Math.ceil(customersData.length / ITEMS_PER_PAGE);
  const startIndex = (currentCustomersPage - 1) * ITEMS_PER_PAGE;
  const pageItems = customersData.slice(startIndex, startIndex + ITEMS_PER_PAGE);

  let html = '';
  pageItems.forEach((cust) => {
    html += `
      <div class="flex items-center justify-between p-2.5 rounded-xl hover:bg-gray-50 transition-colors">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center shrink-0">
            ${cust.initials}
          </div>
          <div>
            <p class="text-xs font-bold text-gray-900 line-clamp-1">${cust.company_name}</p>
            <p class="text-[11px] text-gray-400 font-medium">${cust.contact_person}</p>
          </div>
        </div>
        <div class="text-right">
          <p class="text-xs font-bold text-gray-800">${cust.total_bookings} <span class="text-[10px] font-normal text-gray-400">Bookings</span></p>
          <span class="inline-block px-2 py-0.5 text-[9px] font-bold rounded-md bg-amber-50 text-amber-600 border border-amber-200/60 uppercase">
            ${cust.tier}
          </span>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
  updatePaginationUI('customers', totalPages, currentCustomersPage);
}

function changeCustomersPage(dir) {
  const totalPages = Math.ceil(customersData.length / ITEMS_PER_PAGE);
  if (currentCustomersPage + dir >= 1 && currentCustomersPage + dir <= totalPages) {
    currentCustomersPage += dir;
    renderCustomersPage();
  }
}

// HELPER FOR PAGINATION UI CONTROL
function updatePaginationUI(type, totalPages, currentPage) {
  const pagWrapper = document.getElementById(`${type}-pagination`);
  const indicator = document.getElementById(`${type}PageIndicator`);
  const prevBtn = document.getElementById(`prev${type.charAt(0).toUpperCase() + type.slice(1)}Btn`);
  const nextBtn = document.getElementById(`next${type.charAt(0).toUpperCase() + type.slice(1)}Btn`);

  if (!pagWrapper) return;

  // Tanggalin ang 'hidden' para laging nakikita ang pagination bar sa ilalim
  pagWrapper.classList.remove('hidden');

  const actualPages = totalPages > 0 ? totalPages : 1;
  if (indicator) indicator.innerText = `Page ${currentPage} of ${actualPages}`;
  
  // Disable lang ang buttons kung walang pwedeng lipatan
  if (prevBtn) prevBtn.disabled = (currentPage === 1);
  if (nextBtn) nextBtn.disabled = (currentPage === actualPages || totalPages === 0);
}