<?php
// includes/footer.php
$userType = $_SESSION['user_type'] ?? 'student'; // student/faculty/admin
?>
<script>
// === Sidebar & Mobile Menu Script ===
const body = document.body;
const menuBtn = document.getElementById('menuBtn');
const overlay = document.getElementById('overlay');
const collapseBtn = document.getElementById('collapseBtn');

// Submenus
const materialsBtn = document.getElementById('materialsBtn');
const materialsMenu = document.getElementById('materialsMenu');
const materialsChevron = document.getElementById('materialsChevron');

const timetableBtn = document.getElementById('timetableBtn');
const timetableMenu = document.getElementById('timetableMenu');
const timetableChevron = document.getElementById('timetableChevron');

// --- Mobile menu toggle ---
if (menuBtn && overlay) {
  menuBtn.addEventListener('click', () => {
    const open = body.getAttribute('data-sidebar-open') === 'true';
    body.setAttribute('data-sidebar-open', open ? 'false' : 'true');
    overlay.style.display = open ? 'none' : 'block';
  });
  overlay.addEventListener('click', () => {
    body.setAttribute('data-sidebar-open', 'false');
    overlay.style.display = 'none';
  });
}

// --- Desktop sidebar collapse ---
if (collapseBtn) {
  collapseBtn.addEventListener('click', () => {
    const collapsed = body.getAttribute('data-sidebar-collapsed') === 'true';
    body.setAttribute('data-sidebar-collapsed', collapsed ? 'false' : 'true');
  });
}

// --- Submenu toggle ---
function setupSubmenu(button, menu, chevron) {
  if (!button || !menu || !chevron) return;
  button.addEventListener('click', () => {
    const isOpen = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', String(!isOpen));
    menu.hidden = isOpen;
    menu.classList.toggle('in', !isOpen);
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
  });
}
setupSubmenu(materialsBtn, materialsMenu, materialsChevron);
setupSubmenu(timetableBtn, timetableMenu, timetableChevron);

// --- Close sidebar on Escape ---
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    body.setAttribute('data-sidebar-open', 'false');
    if(overlay) overlay.style.display = 'none';
  }
});

// --- Sync sidebar state on resize ---
const mq = window.matchMedia('(max-width: 1023px)');
const syncState = () => {
  body.setAttribute('data-sidebar-open', 'false');
  if(overlay) overlay.style.display = 'none';
};
mq.addEventListener('change', syncState);
syncState();

// === Theme Toggle Script ===
const rootEl = document.documentElement;
const themeBtn = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');

const savedTheme = localStorage.getItem('theme');
rootEl.setAttribute('data-theme', savedTheme ? savedTheme : 'dark');

function updateThemeIcon() {
  if (!themeIcon) return;
  if (rootEl.getAttribute('data-theme') === 'dark') {
    themeIcon.innerHTML = '<circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2" fill="none" />'
      + '<line x1="12" y1="1" x2="12" y2="3" stroke="currentColor" stroke-width="2"/>'
      + '<line x1="12" y1="21" x2="12" y2="23" stroke="currentColor" stroke-width="2"/>'
      + '<line x1="4.22" y1="4.22" x2="5.64" y2="5.64" stroke="currentColor" stroke-width="2"/>'
      + '<line x1="18.36" y1="18.36" x2="19.78" y2="19.78" stroke="currentColor" stroke-width="2"/>'
      + '<line x1="1" y1="12" x2="3" y2="12" stroke="currentColor" stroke-width="2"/>'
      + '<line x1="21" y1="12" x2="23" y2="12" stroke="currentColor" stroke-width="2"/>'
      + '<line x1="4.22" y1="19.78" x2="5.64" y2="18.36" stroke="currentColor" stroke-width="2"/>'
      + '<line x1="18.36" y1="5.64" x2="19.78" y2="4.22" stroke="currentColor" stroke-width="2"/>';
  } else {
    themeIcon.innerHTML = '<path stroke="currentColor" stroke-width="2" fill="none" d="M21 12.79A9 9 0 1111.21 3a7 7 0 0010.24 9.79z" />';
  }
}
updateThemeIcon();

if (themeBtn) {
  themeBtn.addEventListener('click', () => {
    const newTheme = rootEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    rootEl.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon();
  });
}

// === Logout buttons (sidebar + topbar) ===
const logoutBtns = [document.getElementById('logoutBtn'), document.getElementById('logoutTopbarBtn')];
logoutBtns.forEach(btn => {
  if (btn) {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (confirm('Are you sure you want to log out?')) {
        fetch('/smartcampus/assets/logout.php', { method: 'POST' })
          .then(res => res.json())
          .then(data => {
            if (data.success) window.location.href = '/smartcampus/home.php';
            else alert('Logout failed');
          })
          .catch(() => alert('Error while logging out'));
      }
    });
  }
});

// === Search box toggle & live filter ===
const searchInput = document.querySelector('.search input');
const searchIcon  = document.querySelector('.search svg');

if (searchIcon && searchInput) {
  searchIcon.addEventListener('click', () => {
    searchInput.classList.toggle('active');
    searchInput.focus();
    searchIcon.classList.toggle('active');
  });

  searchInput.addEventListener('keyup', function () {
    const filter = this.value.toLowerCase();

    // Filter tables
    document.querySelectorAll("table[data-search]").forEach(table => {
      table.querySelectorAll("tbody tr").forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
      });
    });

    // Filter cards
    document.querySelectorAll("[data-search-card]").forEach(card => {
      const text = card.innerText.toLowerCase();
      card.style.display = text.includes(filter) ? "" : "none";
    });
  });
}

// === Topbar Dropdowns ===
function setupDropdown(btnId, dropdownId) {
  const btn = document.getElementById(btnId);
  const dropdown = document.getElementById(dropdownId);
  if (!btn || !dropdown) return;

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    dropdown.hidden = !dropdown.hidden;
  });

  document.addEventListener('click', () => {
    dropdown.hidden = true;
  });
}
setupDropdown('profileBtn', 'profileDropdown');

document.querySelectorAll(".view-reason").forEach(btn => {
  btn.addEventListener("click", () => {
    const reasonBox = btn.nextElementSibling;
    if (reasonBox.style.display === "none" || reasonBox.style.display === "") {
      reasonBox.textContent = btn.dataset.reason || "No reason provided";
      reasonBox.style.display = "block";
      btn.textContent = "Hide Reason";
    } else {
      reasonBox.style.display = "none";
      btn.textContent = "View Reason";
    }
  });
});
</script>
</body>
</html>
