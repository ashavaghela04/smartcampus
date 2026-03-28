<?php
// includes/sidebar_faculty.php
?>

<aside class="sidebar">
  <!-- Sidebar top -->
  <div class="sidebar__top">
    <div class="brand">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
           width="28" height="28" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 12l2-2m0 0l7-7 7 7m-9 2v8m4-8l2 2m0 0l7-7-7 7m-2 2v8"/>
      </svg>
      <span>Faculty</span>
    </div>
    <button id="collapseBtn" class="icon-btn" title="Collapse menu">
      <svg viewBox="0 0 24 24" width="20" height="20"
           fill="none" stroke="currentColor" stroke-width="2">
        <path d="M15 19l-7-7 7-7"/>
      </svg>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="nav">
    <!-- Dashboard -->
    <a href="/smartcampus/dashboard/faculty/faculty_dashboard.php" 
       <?= ($activePage ?? '') === "faculty_dashboard" ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20"
           fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 12l2-2 7-7 7 7M9 10v10m6-10v10"/>
      </svg>
      <span class="label">Dashboard</span>
    </a>

    <!-- Edit Profile -->
    <a href="/smartcampus/dashboard/faculty/faculty_profile.php"
       <?= ($activePage ?? '') === 'edit_profile' ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20"
           fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="7" r="4"/>
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
      </svg>
      <span class="label">Edit Profile</span>
    </a>

    <!-- Attendance -->
    <a href="/smartcampus/dashboard/faculty/faculty_attendance.php"
       <?= ($activePage ?? '') === 'faculty_attendance' ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20"
           fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 0 1-2 2H5l-4 4V5a2 2 0 0 1 2-2h11"/>
      </svg>
      <span class="label">Attendance</span>
    </a>

    <a href="/smartcampus/dashboard/faculty/faculty_leave.php"
       <?= ($activePage ?? '') === 'faculty_leave' ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20"
           fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 0 1-2 2H5l-4 4V5a2 2 0 0 1 2-2h11"/>
      </svg>
      <span class="label">Leave Approvel</span>
    </a>
    <!-- Results -->
    <a href="/smartcampus/dashboard/faculty/faculty_results.php"
       <?= ($activePage ?? '') === 'faculty_results' ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20"
           fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 17v-4h6v4M8 9h8M5 6h14v14H5V6z"/>
      </svg>
      <span class="label">Results</span>
    </a>

    <!-- TimeTable Dropdown -->
    <button id="timetableBtn" class="group__header" aria-expanded="false" aria-controls="timetableMenu">
      <span class="kpi">
        <svg viewBox="0 0 24 24" width="20" height="20"
             fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span class="label">TimeTable</span>
      </span>
      <svg id="timetableChevron" viewBox="0 0 24 24" width="18" height="18"
           fill="none" stroke="currentColor" stroke-width="2" style="transition:transform .2s;">
        <path d="M6 9l6 6 6-6"/>
      </svg>
    </button>
    <div id="timetableMenu" class="group__items" hidden>
      <div class="group__inner">
        <a href="/smartcampus/dashboard/timetables/upload_timetable.php"
           <?= ($activePage ?? '') === 'upload_timetable' ? 'aria-current="page"' : '' ?>>
          <svg viewBox="0 0 24 24" width="20" height="20"
               fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
          </svg>
          <span class="label">Upload</span>
        </a>
        <a href="/smartcampus/dashboard/timetables/view_timetable.php"
           <?= ($activePage ?? '') === 'view_timetable' ? 'aria-current="page"' : '' ?>>
          <svg viewBox="0 0 24 24" width="20" height="20"
               fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82 ..."/>
          </svg>
          <span class="label">Manage</span>
        </a>
      </div>
    </div>

    <!-- Materials Dropdown -->
    <button id="materialsBtn" class="group__header" aria-expanded="false" aria-controls="materialsMenu">
      <span class="kpi">
        <svg viewBox="0 0 24 24" width="20" height="20"
             fill="none" stroke="currentColor" stroke-width="2">
          <path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20"/>
          <path d="M6 2l14 14"/>
          <path d="M14 2h6v6"/>
          <path d="M16 16l-4 4H4a2 2 0 0 1-2-2v-8l4-4"/>
        </svg>
        <span class="label">Materials</span>
      </span>
      <svg id="materialsChevron" viewBox="0 0 24 24" width="18" height="18"
           fill="none" stroke="currentColor" stroke-width="2" style="transition:transform .2s;">
        <path d="M6 9l6 6 6-6"/>
      </svg>
    </button>
    <div id="materialsMenu" class="group__items" hidden>
      <div class="group__inner">
        <a href="/smartcampus/dashboard/faculty/faculty_materials_upload.php"
           <?= ($activePage ?? '') === 'faculty_materials_upload' ? 'aria-current="page"' : '' ?>>
          <svg viewBox="0 0 24 24" width="20" height="20"
               fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
          </svg>
          <span class="label">Upload</span>
        </a>
        <a href="/smartcampus/dashboard/faculty/faculty_materials_manage.php"
           <?= ($activePage ?? '') === 'faculty_materials_manage' ? 'aria-current="page"' : '' ?>>
          <svg viewBox="0 0 24 24" width="20" height="20"
               fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 ..."/>
          </svg>
          <span class="label">Manage</span>
        </a>
      </div>
    </div>

    <!-- Announcements -->
    <a href="/smartcampus/dashboard/announcements/announcements.php"
       <?= ($activePage ?? '') === 'faculty_announcements' ? 'aria-current="page"' : '' ?>>
      <svg viewBox="0 0 24 24" width="20" height="20"
           fill="none" stroke="currentColor" stroke-width="2">
        <path d="M13 16h-1v-4h-1m1-4h.01"/>
        <circle cx="12" cy="12" r="9"/>
      </svg>
      <span class="label">Announcements</span>
    </a>
  </nav>

  <!-- Sidebar footer -->
  <div class="sidebar__footer">
    Logged in as <strong>Faculty</strong>
    <br>
    <button id="logoutBtn" class="logout-btn">Logout</button>
  </div>
</aside>

<!-- Mobile overlay -->
<div id="overlay" class="overlay"></div>
