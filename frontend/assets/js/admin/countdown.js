function updateStatusCountdowns() {
  const timers = document.querySelectorAll('.pickup-timer');

  timers.forEach(badge => {
    const targetIso = badge.getAttribute('data-pickup');
    if (!targetIso) return;

    const targetDate = new Date(targetIso).getTime();
    const now = new Date().getTime();
    const diff = targetDate - now;

    // 24 Hours in Milliseconds Threshold
    const TWENTY_FOUR_HOURS = 24 * 60 * 60 * 1000;

    // OVERDUE State
    if (diff <= 0) {
      badge.className = "pickup-timer inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500 text-white animate-pulse shadow-sm";
      badge.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-[9px]"></i> OVERDUE`;
      return;
    }

    // Calculations
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    let displayTime = '';
    if (days > 0) {
      displayTime = `${days}d ${hours}h left`;
    } else {
      displayTime = `${hours}h ${minutes}m ${seconds}s left`;
    }

    // Green (malayo pa) vs Red (24 hrs or less na lang)
    if (diff <= TWENTY_FOUR_HOURS) {
      badge.className = "pickup-timer inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300 animate-pulse shadow-sm";
      badge.innerHTML = `<i class="fa-solid fa-clock text-[9px]"></i> ${displayTime}`;
    } else {
      badge.className = "pickup-timer inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-300 shadow-sm";
      badge.innerHTML = `<i class="fa-solid fa-clock text-[9px]"></i> ${displayTime}`;
    }
  });
}

// Run immediately and update every second
updateStatusCountdowns();
setInterval(updateStatusCountdowns, 1000);