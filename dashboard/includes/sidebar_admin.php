<?php
// includes/sidebar_admin.php

$activePage = $activePage ?? '';
?>
<aside class="sidebar">
  <div class="sidebar__top">
    <div class="brand">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
           viewBox="0 0 24 24" width="28" height="28">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2 7-7 7 7"/>
      </svg>
      <span>Admin Panel</span>
    </div>
    <button id="collapseBtn" class="icon-btn">
      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M15 19l-7-7 7-7"/>
      </svg>
    </button>
  </div>

  <nav class="nav">
    <?php
    $navItems = [
        'admin_dashboard' => ['url' => '/smartcampus/dashboard/admin/admin_dashboard.php', 'label' => 'Dashboard', 'icon' => '<path d="M3 12h18M3 6h18M3 18h18"/>'],
        'admin_faculty_manage' => ['url' => '/smartcampus/dashboard/admin/admin_faculty_manage.php', 'label' => 'Faculty', 'icon' => '<path d="M20 21v-2a4 4 0 0 0-3-3.87"/><path d="M4 21v-2a4 4 0 0 1 3-3.87"/><circle cx="12" cy="7" r="4"/>'],
        'admin_students_manage' => ['url' => '/smartcampus/dashboard/admin/admin_students_manage.php', 'label' => 'Students', 'icon' => '<circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>'],
        'admin_results_manage' => ['url' => '/smartcampus/dashboard/admin/admin_results_manage.php', 'label' => 'Results', 'icon' => '<path d="M9 17v-6h6v6"/><path d="M8 21h8"/><path d="M12 3v4"/>'],
        'admin_attendance_view' => ['url' => '/smartcampus/dashboard/admin/admin_attendance_view.php', 'label' => 'Attendance', 'icon' => '<path d="M16 2v4"/><path d="M8 2v4"/><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 10h18"/>'],
        'admin_announcements' => ['url' => '/smartcampus/dashboard/announcements/announcements.php', 'label' => 'Announcements', 'icon' => '<path d="M3 11l18-5v12L3 13v-2z"/><path d="M11 19H7a4 4 0 0 1-4-4V9"/>'],
        'approve_user' => ['url' => '/smartcampus/dashboard/admin/approve_user.php', 'label' => 'Approvals', 'icon' => '<path d="M20 6L9 17l-5-5"/>'],
        'admin_profile' => ['url' => '/smartcampus/dashboard/admin/admin_profile.php', 'label' => 'Profile', 'icon' => '<circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>'],
'admin_leave_approvel' => ['url' => '/smartcampus/dashboard/admin/leave_approval.php', 'label' => 'Leave Approval', 'icon' => '<circle cx="12" cy="7" r="4"/><path d="M5.5 21a8.38 8.38 0 0 1 13 0"/>']

      ];

    foreach ($navItems as $key => $item):
    ?>
      <div class="nav-item <?= $activePage === $key ? 'active' : '' ?>">
        <a href="<?= $item['url'] ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <?= $item['icon'] ?>
          </svg>
          <span><?= $item['label'] ?></span>
        </a>
      </div>
    <?php endforeach; ?>

    <!-- TimeTable Dropdown -->
    <button id="timetableBtn" class="group__header" aria-expanded="false" aria-controls="timetableMenu">
      <span class="kpi">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        <span class="label">TimeTable</span>
      </span>
      <svg id="timetableChevron" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="transition:transform .2s;">
        <path d="M6 9l6 6 6-6"/>
      </svg>
    </button>

    <div id="timetableMenu" class="group__items" hidden>
      <div class="group__inner">
        <a href="/smartcampus/dashboard/timetables/upload_timetable.php" class="<?= $activePage === 'upload_timetable' ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14"/><path d="M5 12h14"/>
          </svg>
          <span class="label">Upload</span>
        </a>

        <a href="/smartcampus/dashboard/timetables/view_timetable.php" class="<?= $activePage === 'view_timetable' ? 'active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33
                     1.65 1.65 0 0 0-1 1.51V22a2 2 0 1 1-4 0v-.09
                     a1.65 1.65 0 0 0-1-1.51
                     1.65 1.65 0 0 0-1.82.33l-.06.06
                     a2 2 0 1 1-2.83-2.83l.06-.06
                     a1.65 1.65 0 0 0 .33-1.82
                     1.65 1.65 0 0 0-1.51-1H2
                     a2 2 0 1 1 0-4h.09
                     a1.65 1.65 0 0 0 1.51-1
                     1.65 1.65 0 0 0-.33-1.82l-.06-.06
                     a2 2 0 1 1 2.83-2.83l.06.06
                     a1.65 1.65 0 0 0 1.82.33h.09
                     A1.65 1.65 0 0 0 10 2.09V2
                     a2 2 0 1 1 4 0v.09
                     a1.65 1.65 0 0 0 1 1.51h.09
                     a1.65 1.65 0 0 0 1.82-.33l.06-.06
                     a2 2 0 1 1 2.83 2.83l-.06.06
                     a1.65 1.65 0 0 0-.33 1.82v.09
                     A1.65 1.65 0 0 0 21.91 10H22
                     a2 2 0 1 1 0 4h-.09
                     a1.65 1.65 0 0 0-1.51 1z"/>
          </svg>
          <span class="label">Manage</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="sidebar__footer">
    Logged in as <strong>Admin</strong>
    <br>
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
