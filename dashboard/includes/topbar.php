<?php
// includes/topbar.php
?>
<header class="topbar">
  <!-- Mobile menu button -->
  <button id="menuBtn" class="icon-btn" aria-label="Open menu" title="Open menu">
    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="3" y1="12" x2="21" y2="12"/>
      <line x1="3" y1="6" x2="21" y2="6"/>
      <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>

  <!-- Search box -->
  <div class="search" role="search">
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="11" cy="11" r="8"/>
      <path d="M21 21l-4.3-4.3"/>
    </svg>
    <input type="search" placeholder="Search courses, materials, faculty..." aria-label="Search" />
  </div>

  <!-- Theme toggle -->
  <button id="themeToggle" class="icon-btn" aria-label="Toggle theme" title="Toggle theme">
    <svg id="themeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
      viewBox="0 0 24 24" width="20" height="20">
      <!-- Icon is dynamically set in footer.js -->
    </svg>
  </button>

  <!-- Notifications -->
  <div class="topbar-item dropdown" style="position: relative;">
    <button id="notifBtn" class="icon-btn" aria-label="Notifications" title="Notifications">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
      </svg>
      <span id="notifCount" class="badge">3</span>
    </button>
    <div id="notifDropdown" class="dropdown-menu" hidden>
      <ul>
        <li><a href="#">New assignment uploaded</a></li>
        <li><a href="#">Exam schedule released</a></li>
        <li><a href="#">Leave approved</a></li>
      </ul>
    </div>
  </div>

  <!-- Profile -->
  <div class="topbar-item dropdown" style="position: relative;">
    <button id="profileBtn" class="icon-btn" aria-label="Profile" title="Profile">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="7" r="4"/>
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
      </svg>
    </button>
    <div id="profileDropdown" class="dropdown-menu" hidden>
      <ul>
        <li><a href="/smartcampus/dashboard/students/edit_profile.php">Edit Profile</a></li>
        <li><a href="/smartcampus/dashboard/students/leave.php">Apply Leave</a></li>
        <li><a href="#" id="logoutTopbarBtn">Logout</a></li>
      </ul>
    </div>
  </div>
</header>

<style>
/* Simple dropdown styling */
.dropdown-menu {
  position: absolute;
  top: 110%;
  right: 0;
  background: #fff;
  border: 1px solid #ccc;
  border-radius: 6px;
  min-width: 200px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  z-index: 1000;
}
.dropdown-menu ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
.dropdown-menu li a {
  display: block;
  padding: 10px 15px;
  text-decoration: none;
  color: #333;
}
.dropdown-menu li a:hover {
  background: #f0f0f0;
}
.badge {
  background: red;
  color: #fff;
  font-size: 12px;
  padding: 2px 6px;
  border-radius: 50%;
  position: absolute;
  top: 0;
  right: 0;
}
</style>

