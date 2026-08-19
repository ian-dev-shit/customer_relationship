// Custom Styled SweetAlert Mixin para sa SwiftFreight Theme
const SwiftAlert = Swal.mixin({
  customClass: {
    popup: 'rounded-2xl border border-slate-200/80 shadow-2xl font-sans',
    confirmButton: 'px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl text-sm transition shadow-sm ml-2',
    cancelButton: 'px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition'
  },
  buttonsStyling: false
});

// Toast Notification (Lumalabas sa top-right corner)
function showToast(message, type = 'success') {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    background: '#1E293B', // Dark Slate
    color: '#F8FAFC',
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  });

  Toast.fire({
    icon: type,
    title: message
  });
}

// Full Modal Alert Success / Error / Info
function showAlert(title, text, icon = 'success') {
  return SwiftAlert.fire({
    title: `<span class="text-slate-800 text-xl font-bold">${title}</span>`,
    html: `<p class="text-slate-600 text-sm mt-1">${text}</p>`,
    icon: icon,
    confirmButtonText: 'Great, thanks!'
  });
}