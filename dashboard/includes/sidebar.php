<?php
// includes/sidebar.php
if (!isset($activePage)) {
  $activePage = ''; // Prevent undefined variable warnings
}
?>
<aside class="sidebar" aria-label="Primary">
  <div class="sidebar__top">
    <div class="brand" title="Smart Campus">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="24" height="24">
        <path d="M3 10l9-7 9 7"/>
        <path d="M9 22V12h6v10"/>
        <path d="M21 22H3"/>
      </svg>
      <span>Student</span>
    </div>
    <button id="collapseBtn" class="icon-btn" aria-label="Collapse sidebar" title="Collapse sidebar">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M11 17l-5-5 5-5"/>
        <path d="M18 17l-5-5 5-5"/>
      </svg>
    </button>
  </div>

  <nav class="nav" role="navigation">
    <a href="/smartcampus/dashboard/students/dashboard.php" <?= $activePage === 'dashboard' ? 'aria-current="page"' : '' ?>>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <path d="M3 13h8V3H3z"/><path d="M13 21h8V8h-8z"/><path d="M3 21h8v-6H3z"/>
      </svg>
      <span class="label">Dashboard</span>
    </a>

    <a href="/smartcampus/dashboard/students/edit_profile.php" <?= $activePage === 'edit_profile' ? 'aria-current="page"' : '' ?>>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="12" cy="7" r="4"/>
      </svg>
      <span class="label">Edit Profile</span>
    </a>

    <a href="/smartcampus/dashboard/students/attendance.php" <?= $activePage === 'attendance' ? 'aria-current="page"' : '' ?>>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
        <path d="M16 2v4"/>
        <path d="M8 2v4"/>
        <path d="M3 10h18"/>
      </svg>
      <span class="label">Attendance</span>
    </a>

    <a href="/smartcampus/dashboard/students/notice_board.php" <?= $activePage === 'notices' ? 'aria-current="page"' : '' ?>>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <span class="label">Notice Board</span>
    </a>

    <a href="/smartcampus/dashboard/timetables/student_timetable.php" <?= $activePage === 'timetable' ? 'aria-current="page"' : '' ?>>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
        <path d="M16 2v4M8 2v4M3 10h18"/>
      </svg>
      <span class="label">Time Table</span>
    </a>

    <a href="/smartcampus/dashboard/students/result.php" <?= $activePage === 'result' ? 'aria-current="page"' : '' ?>>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <path d="M3 3v18l7-5 7 5V3z"/>
      </svg>
      <span class="label">Result</span>
    </a>

    <a href="/smartcampus/dashboard/students/leave.php" <?= $activePage === 'apply_leave' ? 'aria-current="page"' : '' ?>>
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>
      </svg>
      <span class="label">Apply Leave</span>
    </a>

    <!-- Study Materials submenu -->
    <div class="group">
      <button class="group__header" id="materialsBtn" aria-expanded="false" aria-controls="materialsMenu" type="button">
        <span style="display:flex; align-items:center; gap:12px">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 8v13H3V3h11"/><path d="M21 8H14V1"/>
          </svg>
          <span class="label">Study Materials</span>
        </span>
        <svg id="materialsChevron" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 9l6 6 6-6"/>
        </svg>
      </button>
      <div class="group__items" id="materialsMenu" hidden>
        <div class="group__inner">
          <a href="study_materials.php" <?= $activePage === 'materials_all' ? 'aria-current="page"' : '' ?>>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v15H6.5A2.5 2.5 0 0 0 4 19.5V4.5A2.5 2.5 0 0 1 6.5 2z"/>
            </svg>
            <span class="label">All</span>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Sidebar footer -->
  <div class="sidebar__footer">
    Logged in as <strong>Student</strong><br>
    <button id="logoutBtn" class="logout-btn">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
           stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21V9a3 3 0 0 1 3-3h6"/>
        <path d="M16 17l5-5-5-5"/>
      </svg>
      <span>Logout</span>
    </button>
  </div>
</aside>

<div class="overlay" id="overlay" role="presentation"></div>



<style>
  /* Example CSS for collapsed sidebar */
  .sidebar.collapsed {
    width: 60px; /* adjust as needed */
  }
  .sidebar.collapsed .label {
    display: none;
  }
  .overlay {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.3);
    display: none;
    z-index: 10;
  }
</style>
