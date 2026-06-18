/* ================================================================
   RENTAL MOBIL KU — script.js
   Fitur:
     1. Navbar scroll effect
     2. Mobile hamburger menu
     3. Fleet filter tabs
     4. Booking modal (open/close, focus trap)
     5. Booking form validation
     6. WA preview generator
     7. WhatsApp redirect with formatted message
     8. FAQ accordion (accessible)
   GANTI NOMOR WA DI BAWAH INI:
================================================================ */

const WA_NUMBER = '6287758388616'; // Format: 62 + nomor tanpa angka 0 di awal

/* ── 1. Navbar scroll effect ─────────────────────────────────── */
const navbar = document.getElementById('navbar');

const handleScroll = () => {
  if (window.scrollY > 40) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
};

window.addEventListener('scroll', handleScroll, { passive: true });
handleScroll(); // run on load


/* ── 2. Mobile hamburger menu ────────────────────────────────── */
const hamburger = document.querySelector('.nav-hamburger');
const mobileMenu = document.getElementById('mobile-menu');

hamburger?.addEventListener('click', () => {
  const isOpen = mobileMenu.classList.toggle('open');
  hamburger.setAttribute('aria-expanded', isOpen);
  mobileMenu.setAttribute('aria-hidden', !isOpen);
});

// Close on mobile link click
mobileMenu?.querySelectorAll('a').forEach(link => {
  link.addEventListener('click', () => {
    mobileMenu.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
    mobileMenu.setAttribute('aria-hidden', 'true');
  });
});


/* ── 3. Fleet filter tabs ────────────────────────────────────── */
const filterTabs = document.querySelectorAll('.filter-tab');
const carCards   = document.querySelectorAll('.car-card');

filterTabs.forEach(tab => {
  tab.addEventListener('click', () => {
    const filter = tab.dataset.filter;

    // Update tab states
    filterTabs.forEach(t => {
      t.classList.remove('active');
      t.setAttribute('aria-selected', 'false');
    });
    tab.classList.add('active');
    tab.setAttribute('aria-selected', 'true');

    // Show/hide cards
    carCards.forEach(card => {
      if (filter === 'all' || card.dataset.category === filter) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  });
});


/* ── 4. Booking modal ────────────────────────────────────────── */
const overlay    = document.getElementById('modal-overlay');
const btnClose   = document.getElementById('modal-close');
const step1      = document.getElementById('step-1');
const step2      = document.getElementById('step-2');
const modalCarName = document.getElementById('modal-car-name');

let selectedCar   = { name: '', price: 0 };
let lastFocusEl   = null; // for focus restore on close

// Open modal
document.querySelectorAll('.book-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    selectedCar.name  = btn.dataset.name;
    selectedCar.price = parseInt(btn.dataset.price, 10);
    modalCarName.textContent = `✦ ${selectedCar.name}`;
    openModal();
  });
});

function openModal() {
  lastFocusEl = document.activeElement;
  showStep(1);
  overlay.classList.add('open');
  overlay.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';

  // Set focus to first input after animation
  setTimeout(() => {
    const firstInput = step1.querySelector('input');
    firstInput?.focus();
  }, 320);
}

function closeModal() {
  overlay.classList.remove('open');
  overlay.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
  lastFocusEl?.focus();
  clearError();
}

btnClose?.addEventListener('click', closeModal);

overlay?.addEventListener('click', e => {
  if (e.target === overlay) closeModal();
});

document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
});

// Focus trap inside modal
overlay?.addEventListener('keydown', e => {
  if (e.key !== 'Tab' || !overlay.classList.contains('open')) return;
  const focusable = overlay.querySelectorAll(
    'button:not([disabled]), input, select, textarea, [href], [tabindex]:not([tabindex="-1"])'
  );
  const first = focusable[0];
  const last  = focusable[focusable.length - 1];

  if (e.shiftKey ? document.activeElement === first : document.activeElement === last) {
    e.preventDefault();
    (e.shiftKey ? last : first).focus();
  }
});

function showStep(n) {
  if (n === 1) {
    step1.hidden = false;
    step2.hidden = true;
  } else {
    step1.hidden = true;
    step2.hidden = false;
  }
}


/* ── 5. Form validation ──────────────────────────────────────── */
const formError = document.getElementById('form-error');

function showError(msg) {
  formError.textContent = msg;
  formError.setAttribute('role', 'alert');
}

function clearError() {
  formError.textContent = '';
}

function getFormData() {
  return {
    nama      : document.getElementById('nama')?.value.trim(),
    noHp      : document.getElementById('no-hp')?.value.trim(),
    tglMulai  : document.getElementById('tgl-mulai')?.value,
    jamMulai  : document.getElementById('jam-mulai')?.value,
    tglSelesai: document.getElementById('tgl-selesai')?.value,
    jamSelesai: document.getElementById('jam-selesai')?.value,
    opsi      : document.querySelector('input[name="opsi"]:checked')?.value,
    lokasi    : document.getElementById('lokasi')?.value.trim(),
    catatan   : document.getElementById('catatan')?.value.trim(),
  };
}

function validateForm(data) {
  if (!data.nama)       return 'Nama lengkap harus diisi.';
  if (!data.noHp)       return 'Nomor WhatsApp harus diisi.';
  if (!data.tglMulai)   return 'Tanggal mulai sewa harus diisi.';
  if (!data.jamMulai)   return 'Jam penjemputan harus diisi.';
  if (!data.tglSelesai) return 'Tanggal selesai sewa harus diisi.';
  if (!data.jamSelesai) return 'Jam pengembalian harus diisi.';

  const start = new Date(`${data.tglMulai}T${data.jamMulai}`);
  const end   = new Date(`${data.tglSelesai}T${data.jamSelesai}`);
  if (isNaN(start) || isNaN(end)) return 'Format tanggal/jam tidak valid.';
  if (end <= start) return 'Tanggal/jam selesai harus setelah tanggal/jam mulai.';

  return null;
}


/* ── 6. WA message builder ───────────────────────────────────── */
function formatDate(dateStr) {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}

function formatTime(timeStr) {
  if (!timeStr) return '-';
  return timeStr + ' WIB';
}

function calcDuration(tglMulai, jamMulai, tglSelesai, jamSelesai) {
  const start = new Date(`${tglMulai}T${jamMulai}`);
  const end   = new Date(`${tglSelesai}T${jamSelesai}`);
  const diffMs = end - start;
  const diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
  const days  = Math.floor(diffHours / 24);
  const hours = diffHours % 24;
  let str = '';
  if (days  > 0) str += `${days} hari `;
  if (hours > 0) str += `${hours} jam`;
  return str.trim() || '< 1 jam';
}

function formatRupiah(num) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);
}

function buildWAMessage(data) {
  const durStr = calcDuration(data.tglMulai, data.jamMulai, data.tglSelesai, data.jamSelesai);

  // Estimasi harga (asumsi per hari 24 jam)
  const start = new Date(`${data.tglMulai}T${data.jamMulai}`);
  const end   = new Date(`${data.tglSelesai}T${data.jamSelesai}`);
  const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
  const estimasi = diffDays > 0 ? formatRupiah(selectedCar.price * diffDays) : '(hubungi kami)';

  const lines = [
    '🚗 *BOOKING RENTAL MOBIL KU*',
    '━━━━━━━━━━━━━━━━━━━━',
    '',
    `📋 *Detail Pemesan*`,
    `• Nama       : ${data.nama}`,
    `• No. WA     : ${data.noHp}`,
    '',
    `🚙 *Detail Kendaraan*`,
    `• Kendaraan  : ${selectedCar.name}`,
    `• Harga/hari : ${formatRupiah(selectedCar.price)}`,
    `• Opsi Sewa  : ${data.opsi}`,
    '',
    `📅 *Jadwal Sewa*`,
    `• Mulai      : ${formatDate(data.tglMulai)}, ${formatTime(data.jamMulai)}`,
    `• Selesai    : ${formatDate(data.tglSelesai)}, ${formatTime(data.jamSelesai)}`,
    `• Durasi     : ${durStr}`,
    '',
    `📍 *Lokasi Penjemputan*`,
    `• ${data.lokasi || 'Belum diisi (akan dikonfirmasi)'}`,
    '',
    ...(data.catatan ? [
      `📝 *Catatan Tambahan*`,
      `• ${data.catatan}`,
      '',
    ] : []),
    `💰 *Estimasi Biaya*`,
    `• ${formatRupiah(selectedCar.price)} × ${diffDays > 0 ? diffDays + ' hari' : 'durasi'} = ${estimasi}`,
    `  _(belum termasuk biaya sopir & BBM jika ada)_`,
    '',
    '━━━━━━━━━━━━━━━━━━━━',
    'Mohon konfirmasi ketersediaan kendaraan.',
    'Terima kasih! 🙏',
  ];

  return lines.join('\n');
}


/* ── 7. Preview & WA redirect ────────────────────────────────── */
const btnPreview = document.getElementById('btn-preview');
const btnBack    = document.getElementById('btn-back');
const btnSendWA  = document.getElementById('btn-send-wa');
const waPreview  = document.getElementById('wa-preview-text');

btnPreview?.addEventListener('click', () => {
  clearError();
  const data = getFormData();
  const err  = validateForm(data);

  if (err) {
    showError(err);
    return;
  }

  const message = buildWAMessage(data);
  waPreview.textContent = message;

  const encoded = encodeURIComponent(message);
  btnSendWA.href = `https://api.whatsapp.com/send?phone=${WA_NUMBER}&text=${encoded}`;

  showStep(2);

  // Scroll preview into view, focus send button
  setTimeout(() => btnSendWA?.focus(), 80);
});

btnBack?.addEventListener('click', () => {
  showStep(1);
  setTimeout(() => document.getElementById('nama')?.focus(), 80);
});


/* ── 8. FAQ accordion ────────────────────────────────────────── */
document.querySelectorAll('.faq-question').forEach(btn => {
  btn.addEventListener('click', () => {
    const answer    = document.getElementById(btn.getAttribute('aria-controls'));
    const isExpanded = btn.getAttribute('aria-expanded') === 'true';

    // Close all others (optional: comment out for multi-open behavior)
    document.querySelectorAll('.faq-question').forEach(other => {
      if (other !== btn) {
        other.setAttribute('aria-expanded', 'false');
        const otherAnswer = document.getElementById(other.getAttribute('aria-controls'));
        if (otherAnswer) otherAnswer.hidden = true;
      }
    });

    // Toggle current
    btn.setAttribute('aria-expanded', !isExpanded);
    answer.hidden = isExpanded;
  });
});


/* ── 9. Set min dates for booking inputs ────────────────────── */
(function setMinDates() {
  const today = new Date().toISOString().split('T')[0];
  const tglMulai   = document.getElementById('tgl-mulai');
  const tglSelesai = document.getElementById('tgl-selesai');

  if (tglMulai) tglMulai.min = today;
  if (tglSelesai) tglSelesai.min = today;

  tglMulai?.addEventListener('change', () => {
    if (tglSelesai) tglSelesai.min = tglMulai.value;
    if (tglSelesai?.value && tglSelesai.value < tglMulai.value) {
      tglSelesai.value = tglMulai.value;
    }
  });
})();
