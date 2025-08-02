<?php
/**
 * Enhanced Dashboard Sidebar
 * Modern sidebar with improved navigation and pet list.
 */

// Fetch all pets with additional metadata
$pets = get_posts( array(
  'post_type'      => 'pets',
  'posts_per_page' => -1,
  'orderby'        => 'title',
  'order'          => 'ASC',
  'meta_query'     => array(
    'relation' => 'OR',
    array(
      'key'     => 'featured',
      'value'   => '1',
      'compare' => '='
    ),
    array(
      'key'     => 'featured',
      'compare' => 'NOT EXISTS'
    )
  )
) );

// Get current user for personalization
$current_user = wp_get_current_user();
$user_role = $current_user->roles[0] ?? 'subscriber';
?>

<nav class="dashboard-sidebar">
  <!-- User Welcome Section -->
  <div class="sidebar-welcome" style="
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
  ">
    <div class="user-avatar" style="
      width: 50px;
      height: 50px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 1.2rem;
      margin: 0 auto 0.5rem;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    ">
      <?php echo esc_html( strtoupper( substr( $current_user->display_name, 0, 1 ) ) ); ?>
    </div>
    <p style="color: var(--text-primary); font-weight: 500; margin-bottom: 0.25rem;">
      <?php echo esc_html( $current_user->display_name ); ?>
    </p>
    <p style="color: var(--text-secondary); font-size: 0.875rem; text-transform: capitalize;">
      <?php echo esc_html( str_replace('_', ' ', $user_role) ); ?>
    </p>
  </div>

  <!-- Navigation Section -->
  <div class="dashboard-section">
    <h3 style="
      color: var(--text-primary);
      font-weight: 600;
      margin-bottom: 1rem;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      opacity: 0.8;
    ">Navigation</h3>
    
    <button id="home-toggle" class="dashboard-home-toggle nav-item" style="
      width: 100%;
      padding: 1rem 1.2rem;
      border: none;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.1);
      color: var(--text-primary);
      font-weight: 500;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-align: left;
    ">
      <span style="
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
      ">🏠</span>
      <span>Dashboard</span>
    </button>
    
    <button id="pets-toggle" class="dashboard-pets-toggle nav-item" style="
      width: 100%;
      padding: 1rem 1.2rem;
      border: none;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.1);
      color: var(--text-primary);
      font-weight: 500;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-align: left;
    ">
      <span style="
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
      ">🐾</span>
      <span>All Pets</span>
      <span style="
        margin-left: auto;
        background: rgba(102, 126, 234, 0.2);
        color: #667eea;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 10px;
      "><?php echo count( $pets ); ?></span>
    </button>

    <!-- Quick Actions -->
    <?php if ( current_user_can( 'manage_options' ) || in_array( 'vet', $current_user->roles ) ) : ?>
    <button class="nav-item" style="
      width: 100%;
      padding: 1rem 1.2rem;
      border: none;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.1);
      color: var(--text-primary);
      font-weight: 500;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-align: left;
    " onclick="window.open('<?php echo admin_url('post-new.php?post_type=pets'); ?>', '_blank')">
      <span style="
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
      ">➕</span>
      <span>Add New Pet</span>
    </button>
    <?php endif; ?>
  </div>

  <!-- Recent/Featured Pets Section -->
  <?php if ( ! empty( $pets ) ) : ?>
  <div class="dashboard-section">
    <h3 style="
      color: var(--text-primary);
      font-weight: 600;
      margin-bottom: 1rem;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      opacity: 0.8;
    ">Recent Pets</h3>
    
    <div class="pets-quick-list" style="max-height: 300px; overflow-y: auto;">
      <?php 
      // Show only first 6 pets in sidebar for better UX
      $sidebar_pets = array_slice( $pets, 0, 6 );
      foreach ( $sidebar_pets as $pet ) : 
        $pet_id = $pet->ID;
        $species = wp_get_post_terms( $pet_id, 'species', array( 'fields' => 'names' ) );
        $breed = wp_get_post_terms( $pet_id, 'breed', array( 'fields' => 'names' ) );
        $owner_id = get_post_meta( $pet_id, 'owner_id', true );
        $owner = $owner_id ? get_userdata( $owner_id ) : null;
        
        // Get pet initials for avatar
        $pet_name = get_the_title( $pet );
        $pet_initials = '';
        $name_parts = explode( ' ', $pet_name );
        foreach ( $name_parts as $part ) {
          $pet_initials .= strtoupper( substr( $part, 0, 1 ) );
        }
        if ( strlen( $pet_initials ) > 2 ) {
          $pet_initials = substr( $pet_initials, 0, 2 );
        }
      ?>
      <div class="pet-quick-item dashboard-pet-link" 
           data-pet-id="<?php echo esc_attr( $pet_id ); ?>"
           style="
             display: flex;
             align-items: center;
             gap: 0.75rem;
             padding: 0.875rem;
             border-radius: 12px;
             margin-bottom: 0.5rem;
             cursor: pointer;
             transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
             background: rgba(255, 255, 255, 0.05);
             border: 1px solid rgba(255, 255, 255, 0.1);
             backdrop-filter: blur(10px);
           ">
        <div class="pet-avatar" style="
          width: 36px;
          height: 36px;
          background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
          border-radius: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-weight: 600;
          font-size: 0.875rem;
          flex-shrink: 0;
          box-shadow: 0 2px 8px rgba(240, 147, 251, 0.3);
        ">
          <?php echo esc_html( $pet_initials ); ?>
        </div>
        <div class="pet-info" style="flex: 1; min-width: 0;">
          <div class="pet-name" style="
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.95rem;
            margin-bottom: 0.125rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
          ">
            <?php echo esc_html( $pet_name ); ?>
          </div>
          <div class="pet-details" style="
            font-size: 0.75rem;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
          ">
            <?php 
            if ( ! empty( $species ) ) {
              echo esc_html( $species[0] );
              if ( ! empty( $breed ) ) {
                echo ' • ' . esc_html( $breed[0] );
              }
            } else {
              echo 'Pet';
            }
            ?>
          </div>
        </div>
        <?php if ( get_post_meta( $pet_id, 'urgent', true ) ) : ?>
        <div class="urgent-indicator" style="
          width: 8px;
          height: 8px;
          background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
          border-radius: 50%;
          animation: pulse 2s infinite;
        "></div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      
      <?php if ( count( $pets ) > 6 ) : ?>
      <div style="text-align: center; margin-top: 1rem;">
        <button id="show-all-pets" class="nav-item" style="
          background: rgba(255, 255, 255, 0.1);
          border: 1px solid rgba(255, 255, 255, 0.2);
          color: var(--text-secondary);
          padding: 0.5rem 1rem;
          border-radius: 8px;
          font-size: 0.875rem;
          cursor: pointer;
          transition: all 0.3s ease;
        ">
          View All <?php echo count( $pets ); ?> Pets
        </button>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Quick Stats Section -->
  <div class="dashboard-section">
    <div class="quick-stats" style="
      background: rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      padding: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
    ">
      <h4 style="
        color: var(--text-primary);
        font-weight: 600;
        margin-bottom: 0.75rem;
        font-size: 0.875rem;
      ">Quick Stats</h4>
      
      <div class="stat-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
      ">
        <span style="color: var(--text-secondary); font-size: 0.875rem;">Today</span>
        <span style="
          background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
          color: white;
          font-weight: 600;
          font-size: 0.75rem;
          padding: 0.25rem 0.5rem;
          border-radius: 8px;
        "><?php echo count( $today_visits ); ?></span>
      </div>
      
      <?php
      // Get this month's visits
      $this_month_visits = get_posts( array(
        'post_type'      => 'visit',
        'posts_per_page' => -1,
        'date_query'     => array(
          array(
            'year'  => date( 'Y' ),
            'month' => date( 'm' ),
          ),
        ),
      ) );
      ?>
      
      <div class="stat-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
      ">
        <span style="color: var(--text-secondary); font-size: 0.875rem;">This Month</span>
        <span style="
          background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
          color: white;
          font-weight: 600;
          font-size: 0.75rem;
          padding: 0.25rem 0.5rem;
          border-radius: 8px;
        "><?php echo count( $this_month_visits ); ?></span>
      </div>
    </div>
  </div>
</nav>

<style>
/* Enhanced sidebar hover effects */
.nav-item:hover {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2)) !important;
  transform: translateX(4px) !important;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2) !important;
}

.pet-quick-item:hover {
  background: rgba(255, 255, 255, 0.15) !important;
  transform: translateX(2px) !important;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
}

.pet-quick-item.active {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2)) !important;
  border-color: rgba(102, 126, 234, 0.5) !important;
}

#show-all-pets:hover {
  background: rgba(255, 255, 255, 0.2) !important;
  color: var(--text-primary) !important;
}

/* Pulse animation for urgent indicators */
@keyframes pulse {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  50% {
    transform: scale(1.2);
    opacity: 0.7;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Custom scrollbar for pets list */
.pets-quick-list::-webkit-scrollbar {
  width: 4px;
}

.pets-quick-list::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}

.pets-quick-list::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 2px;
}

.pets-quick-list::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}
</style>

<script>
// Enhanced sidebar interactions
document.addEventListener('DOMContentLoaded', function() {
  // Pet quick-item click handling
  var petQuickItems = document.querySelectorAll('.pet-quick-item');
  petQuickItems.forEach(function(item) {
    item.addEventListener('click', function() {
      // Remove active class from all items
      petQuickItems.forEach(function(otherItem) {
        otherItem.classList.remove('active');
      });
      
      // Add active class to clicked item
      this.classList.add('active');
      
      // Get pet ID and load overview
      var petId = this.getAttribute('data-pet-id');
      if (typeof loadPanel === 'function') {
        loadPanel(petId, 'overview');
      }
    });
  });

  // Show all pets button
  var showAllBtn = document.getElementById('show-all-pets');
  if (showAllBtn) {
    showAllBtn.addEventListener('click', function() {
      var petsToggle = document.getElementById('pets-toggle');
      if (petsToggle) {
        petsToggle.click();
      }
    });
  }

  // Add keyboard navigation for sidebar
  document.addEventListener('keydown', function(e) {
    if (e.altKey && e.key === 'h') {
      e.preventDefault();
      document.getElementById('home-toggle').click();
    }
    if (e.altKey && e.key === 'p') {
      e.preventDefault();
      document.getElementById('pets-toggle').click();
    }
  });
});
</script>text-secondary); font-size: 0.875rem;">Total Pets</span>
        <span style="
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          font-weight: 600;
          font-size: 0.75rem;
          padding: 0.25rem 0.5rem;
          border-radius: 8px;
        "><?php echo count( $pets ); ?></span>
      </div>
      
      <?php
      // Get today's appointments count (if you have visit/appointment CPT)
      $today_visits = get_posts( array(
        'post_type'      => 'visit',
        'posts_per_page' => -1,
        'meta_query'     => array(
          array(
            'key'     => 'visit_date',
            'value'   => date( 'Y-m-d' ),
            'compare' => '='
          )
        )
      ) );
      ?>
      
      <div class="stat-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
      ">
        <span style="color: var(--