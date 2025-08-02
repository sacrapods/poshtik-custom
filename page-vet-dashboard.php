<?php
/**
 * Template Name: Vet Dashboard
 */

if ( ! is_user_logged_in() ) {
  wp_redirect( home_url( 'login' ) );
  exit;
}

$current_user = wp_get_current_user();
$pets_count = wp_count_posts('pets')->publish ?? 0;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vet Dashboard</title>
  
  <style>
    /* PERFORMANCE-OPTIMIZED CSS - NO SLUGGISHNESS */
    * { 
      margin: 0; 
      padding: 0; 
      box-sizing: border-box; 
    }
    
    body {
      font-family: -apple-system, BlinkMacSystemFont, sans-serif;
      background: #667eea;
      margin: 0;
      overflow-x: hidden;
      /* CRITICAL: Force hardware acceleration */
      transform: translateZ(0);
      -webkit-transform: translateZ(0);
    }
    
    /* Header - No heavy effects */
    .header {
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .header h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: #667eea;
      margin: 0;
    }
    
    .toggle-btn {
      background: #667eea;
      border: none;
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 6px;
      cursor: pointer;
      margin-right: 1rem;
      font-size: 1rem;
      /* CRITICAL: Instant response */
      transition: background-color 0.1s ease;
    }
    
    .toggle-btn:hover {
      background: #5a67d8;
    }
    
    .user-info {
      color: #6b7280;
      font-weight: 500;
    }
    
    .user-info a {
      color: #6b7280;
      text-decoration: none;
    }
    
    /* Dashboard Layout - Simplified */
    .dashboard {
      display: flex;
      height: calc(100vh - 80px);
      gap: 1rem;
      padding: 1rem;
      /* CRITICAL: Prevent layout thrashing */
      will-change: transform;
    }
    
    /* Sidebar - Ultra lightweight */
    .sidebar {
      width: 280px;
      background: #fff;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      overflow-y: auto;
      /* CRITICAL: Smooth scrolling */
      -webkit-overflow-scrolling: touch;
    }
    
    .sidebar-section {
      margin-bottom: 1.5rem;
    }
    
    .sidebar-title {
      color: #374151;
      font-weight: 600;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin: 0 0 1rem 0;
    }
    
    /* User Card - No effects */
    .user-card {
      background: #f8fafc;
      border-radius: 8px;
      padding: 1rem;
      text-align: center;
      margin-bottom: 1.5rem;
    }
    
    .user-avatar {
      width: 40px;
      height: 40px;
      background: #667eea;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      margin: 0 auto 0.5rem;
    }
    
    .user-name {
      font-weight: 600;
      color: #374151;
      margin-bottom: 0.25rem;
    }
    
    .user-role {
      font-size: 0.875rem;
      color: #6b7280;
    }
    
    /* Navigation - Instant response */
    .nav-btn {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 8px;
      background: #f8fafc;
      color: #374151;
      font-weight: 500;
      cursor: pointer;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-align: left;
      /* CRITICAL: Ultra-fast response */
      transition: all 0.1s ease;
    }
    
    .nav-btn:hover {
      background: #667eea;
      color: white;
    }
    
    .nav-btn:active {
      transform: scale(0.98);
    }
    
    .nav-icon {
      width: 20px;
      height: 20px;
      background: #667eea;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      color: white;
    }
    
    .nav-btn:hover .nav-icon {
      background: rgba(255,255,255,0.2);
    }
    
    .pet-count {
      margin-left: auto;
      background: #e5e7eb;
      color: #374151;
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 10px;
    }
    
    /* Pet List - Simplified */
    .pet-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem;
      border-radius: 8px;
      margin-bottom: 0.5rem;
      cursor: pointer;
      background: #f8fafc;
      /* CRITICAL: Instant hover */
      transition: background-color 0.1s ease;
    }
    
    .pet-item:hover {
      background: #e5e7eb;
    }
    
    .pet-avatar {
      width: 32px;
      height: 32px;
      background: #f093fb;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 0.875rem;
    }
    
    .pet-name {
      font-weight: 600;
      color: #374151;
      font-size: 0.9rem;
    }
    
    /* Main Content - No heavy effects */
    .main-content {
      flex: 1;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    
    /* Tabs - Instant switching */
    .tabs {
      display: flex;
      padding: 1.5rem 1.5rem 0;
      gap: 0.5rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .tab {
      background: #f8fafc;
      border: 1px solid #e5e7eb;
      color: #6b7280;
      padding: 0.75rem 1.5rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 500;
      /* CRITICAL: No delay */
      transition: all 0.1s ease;
    }
    
    .tab:hover {
      background: #e5e7eb;
      color: #374151;
    }
    
    .tab.active {
      background: #667eea;
      color: white;
      border-color: #667eea;
    }
    
    /* Content Area */
    .content {
      flex: 1;
      padding: 2rem;
      overflow-y: auto;
      /* CRITICAL: Smooth scrolling */
      -webkit-overflow-scrolling: touch;
    }
    
    /* Welcome Screen - Lightweight */
    .welcome {
      text-align: center;
      padding: 2rem 0;
    }
    
    .welcome-icon {
      width: 80px;
      height: 80px;
      background: #667eea;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1.5rem;
      font-size: 2rem;
      color: white;
    }
    
    .welcome-title {
      font-size: 1.8rem;
      font-weight: 700;
      color: #374151;
      margin-bottom: 0.5rem;
    }
    
    .welcome-subtitle {
      font-size: 1rem;
      color: #6b7280;
      margin-bottom: 2rem;
    }
    
    /* Stats - No hover effects for performance */
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
      margin-top: 1.5rem;
    }
    
    .stat {
      background: #f8fafc;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 1.5rem;
      text-align: center;
    }
    
    .stat-icon {
      width: 40px;
      height: 40px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1rem;
      margin: 0 auto 1rem;
    }
    
    .stat-number {
      font-size: 1.5rem;
      font-weight: 700;
      color: #374151;
      margin-bottom: 0.5rem;
    }
    
    .stat-label {
      color: #6b7280;
      font-weight: 500;
      font-size: 0.875rem;
    }
    
    /* Utility */
    .hidden { display: none !important; }
    
    /* Responsive - Simplified */
    @media (max-width: 768px) {
      .dashboard {
        flex-direction: column;
        padding: 0.5rem;
      }
      .sidebar {
        width: 100%;
      }
      .stats {
        grid-template-columns: 1fr;
      }
    }
    
    /* Collapsed sidebar - No animations */
    .dashboard.collapsed .sidebar {
      display: none;
    }
    
    /* CRITICAL: Disable all transitions on touch devices */
    @media (hover: none) and (pointer: coarse) {
      * {
        transition: none !important;
      }
    }
  </style>
  
  <?php wp_head(); ?>
</head>
<body>
  
  <!-- Header -->
  <header class="header">
    <div style="display: flex; align-items: center;">
      <button class="toggle-btn" id="sidebar-toggle">☰</button>
      <h1>Vet Dashboard</h1>
    </div>
    <div class="user-info">
      Howdy, <?php echo esc_html( $current_user->display_name ); ?> |
      <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">Log Out</a>
    </div>
  </header>

  <!-- Dashboard -->
  <div class="dashboard" id="dashboard">
    
    <!-- Sidebar -->
    <aside class="sidebar">
      <!-- User Card -->
      <div class="user-card">
        <div class="user-avatar">
          <?php echo esc_html( strtoupper( substr( $current_user->display_name, 0, 1 ) ) ); ?>
        </div>
        <div class="user-name"><?php echo esc_html( $current_user->display_name ); ?></div>
        <div class="user-role">Administrator</div>
      </div>

      <!-- Navigation -->
      <div class="sidebar-section">
        <h3 class="sidebar-title">Navigation</h3>
        <button class="nav-btn" id="home-btn">
          <div class="nav-icon">🏠</div>
          Dashboard
        </button>
        <button class="nav-btn" id="pets-btn">
          <div class="nav-icon">🐾</div>
          All Pets
          <span class="pet-count"><?php echo $pets_count; ?></span>
        </button>
        <button class="nav-btn" id="appointments-btn">
          <div class="nav-icon">📅</div>
          All Appointments
          <span class="pet-count" id="appointments-count">0</span>
        </button>
      </div>

      <!-- Recent Pets -->
      <div class="sidebar-section">
        <h3 class="sidebar-title">Recent Pets</h3>
        <?php
        $recent_pets = get_posts(array(
          'post_type' => 'pets',
          'posts_per_page' => 3,
          'orderby' => 'date',
          'order' => 'DESC'
        ));
        foreach ( $recent_pets as $pet ) :
          $pet_name = get_the_title( $pet );
          $pet_initials = strtoupper( substr( $pet_name, 0, 1 ) );
        ?>
        <div class="pet-item" data-pet-id="<?php echo $pet->ID; ?>">
          <div class="pet-avatar"><?php echo esc_html( $pet_initials ); ?></div>
          <div class="pet-name"><?php echo esc_html( $pet_name ); ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Tabs -->
      <nav class="tabs">
        <button class="tab active" data-panel="overview">Overview</button>
        <button class="tab" data-panel="visits">Visits</button>
        <button class="tab" data-panel="files">Files</button>
        <button class="tab" data-panel="notes">Notes</button>
      </nav>

      <!-- Content -->
      <div class="content">
        <div id="overview-panel">
          <div class="welcome">
            <div class="welcome-icon">🐾</div>
            <h2 class="welcome-title">Welcome to VetCare Dashboard</h2>
            <p class="welcome-subtitle">Manage your patients with care and efficiency</p>
            
            <div class="stats">
              <div class="stat">
                <div class="stat-icon" style="background: #667eea;">🐾</div>
                <div class="stat-number"><?php echo $pets_count; ?></div>
                <div class="stat-label">Total Pets</div>
              </div>
              <div class="stat">
                <div class="stat-icon" style="background: #4facfe;">📅</div>
                <div class="stat-number">0</div>
                <div class="stat-label">Today's Appointments</div>
              </div>
              <div class="stat">
                <div class="stat-icon" style="background: #43e97b;">🩺</div>
                <div class="stat-number">0</div>
                <div class="stat-label">Visits This Month</div>
              </div>
              <div class="stat">
                <div class="stat-icon" style="background: #f093fb;">⚠️</div>
                <div class="stat-number">0</div>
                <div class="stat-label">Urgent Cases</div>
              </div>
            </div>
          </div>
        </div>

        <div id="visits-panel" class="hidden">
          <h2>Visits</h2>
          <p>Visit records will be loaded here...</p>
        </div>

        <div id="files-panel" class="hidden">
          <h2>Files</h2>
          <p>Medical files will be loaded here...</p>
        </div>

        <div id="notes-panel" class="hidden">
          <h2>Notes</h2>
          <p>Medical notes will be loaded here...</p>
        </div>
      </div>
    </main>
  </div>

<script>
// ULTRA-FAST Performance Dashboard
document.addEventListener('click', function(e) {
  // Sidebar toggle
  if (e.target.id === 'sidebar-toggle') {
    document.getElementById('dashboard').classList.toggle('collapsed');
    return;
  }
  // Appointments button click
if (e.target.id === 'appointments-btn') {
  loadAppointments();
  return;
}

  // Dashboard navigation - FIXED
  if (e.target.id === 'home-btn') {
    showWelcomeScreen();
    return;
  }
  
  // All Pets button - Enhanced
  if (e.target.id === 'pets-btn') {
    loadAllPetsEnhanced();
    return;
  }
  
  // Edit Profile buttons
  if (e.target.classList.contains('edit-profile-btn')) {
    var petId = e.target.getAttribute('data-pet-id');
    loadPetEditProfile(petId);
    return;
  }
  
  // Pet row clicks
  if (e.target.closest('.pet-row')) {
    var row = e.target.closest('.pet-row');
    var editBtn = row.querySelector('.edit-profile-btn');
    if (editBtn) {
      var petId = editBtn.getAttribute('data-pet-id');
      loadPetEditProfile(petId);
    }
    return;
  }
  
  // Tab switching
  if (e.target.classList.contains('tab')) {
    var panelId = e.target.getAttribute('data-panel') + '-panel';
    
    // Hide all panels
    var panels = document.querySelectorAll('[id$="-panel"]');
    for (var i = 0; i < panels.length; i++) {
      panels[i].classList.add('hidden');
    }
    
    // Remove active from all tabs
    var tabs = document.querySelectorAll('.tab');
    for (var i = 0; i < tabs.length; i++) {
      tabs[i].classList.remove('active');
    }
    
    // Show target panel and activate tab
    var targetPanel = document.getElementById(panelId);
    if (targetPanel) {
      targetPanel.classList.remove('hidden');
    }
    e.target.classList.add('active');
    return;
  }
  // Load appointments function
function loadAppointments() {
  var content = document.querySelector('.content');
  content.innerHTML = '<div style="text-align: center; padding: 2rem;"><div style="width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>Loading appointments...</div>';
  
  fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=poshtik_load_appointments&view=day&date=' + new Date().toISOString().split('T')[0]
  })
  .then(response => response.text())
  .then(html => {
    content.innerHTML = html;
  })
  .catch(() => {
    content.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;">Error loading appointments. Please try again.</div>';
  });
}
});

// Show welcome screen
function showWelcomeScreen() {
  var content = document.querySelector('.content');
  content.innerHTML = `
    <div class="welcome">
      <div class="welcome-icon">🐾</div>
      <h2 class="welcome-title">Welcome to VetCare Dashboard</h2>
      <p class="welcome-subtitle">Manage your patients with care and efficiency</p>
      <div class="stats">
        <div class="stat">
          <div class="stat-icon" style="background: #667eea;">🐾</div>
          <div class="stat-number">${document.querySelector('.pet-count').textContent}</div>
          <div class="stat-label">Total Pets</div>
        </div>
        <div class="stat">
          <div class="stat-icon" style="background: #4facfe;">📅</div>
          <div class="stat-number">0</div>
          <div class="stat-label">Today's Appointments</div>
        </div>
        <div class="stat">
          <div class="stat-icon" style="background: #43e97b;">🩺</div>
          <div class="stat-number">0</div>
          <div class="stat-label">Visits This Month</div>
        </div>
        <div class="stat">
          <div class="stat-icon" style="background: #f093fb;">⚠️</div>
          <div class="stat-number">0</div>
          <div class="stat-label">Urgent Cases</div>
        </div>
      </div>
    </div>
  `;
}

// Load enhanced pets list
function loadAllPetsEnhanced() {
  var content = document.querySelector('.content');
  content.innerHTML = '<div style="text-align: center; padding: 2rem;"><div style="width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>Loading pets...</div>';
  
  fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=load_all_pets_enhanced'
  })
  .then(response => response.text())
  .then(html => {
    content.innerHTML = html;
    
    // Add instant search functionality
    var searchInput = document.getElementById('pet-search');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        var searchTerm = this.value.toLowerCase();
        var petRows = document.querySelectorAll('.pet-row');
        
        petRows.forEach(function(row) {
          var petName = row.getAttribute('data-pet-name');
          if (petName.includes(searchTerm)) {
            row.style.display = 'flex';
          } else {
            row.style.display = 'none';
          }
        });
      });
      
      // Focus search on load
      searchInput.focus();
    }
    
    // Add hover effects
    var petRows = document.querySelectorAll('.pet-row');
    petRows.forEach(function(row) {
      row.addEventListener('mouseenter', function() {
        this.style.background = '#e5e7eb';
        this.style.transform = 'translateX(2px)';
      });
      row.addEventListener('mouseleave', function() {
        this.style.background = '#f8fafc';
        this.style.transform = 'translateX(0)';
      });
    });
  })
  .catch(() => {
    content.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;">Error loading pets. Please try again.</div>';
  });
}

// Load pet edit profile
function loadPetEditProfile(petId) {
  var content = document.querySelector('.content');
  content.innerHTML = '<div style="text-align: center; padding: 2rem;"><div style="width: 40px; height: 40px; border: 3px solid #e5e7eb; border-top-color: #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>Loading pet profile...</div>';
  
  fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=load_pet_edit_profile&pet_id=' + petId
  })
  .then(response => response.text())
  .then(html => {
    content.innerHTML = html;
    
    // Add form submission handling
    var editForm = document.getElementById('pet-edit-form');
    if (editForm) {
      editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Add form submission logic here
        alert('Profile updated! (Form submission to be implemented)');
      });
    }
    
    var commentForm = document.getElementById('add-comment-form');
    if (commentForm) {
      commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Add comment submission logic here
        alert('Comment added! (Comment submission to be implemented)');
      });
    }
  })
  .catch(() => {
    content.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;">Error loading pet profile. Please try again.</div>';
  });
}

// Initialize welcome screen on load
document.addEventListener('DOMContentLoaded', function() {
  showWelcomeScreen();
});
</script>

  <?php wp_footer(); ?>
</body>
</html>