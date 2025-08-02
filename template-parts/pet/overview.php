<?php
/**
 * Enhanced Template Part: Pet Overview
 * Modern, beautiful pet details display with CSS classes instead of inline styles.
 */

// Ensure we have access to the global post object.
global $post;
$pet_id = $post->ID;

// Retrieve pet owner (user) ID from post meta.
$owner_id = get_post_meta( $pet_id, 'owner_id', true );
$owner    = $owner_id ? get_userdata( $owner_id ) : null;

// Retrieve taxonomy terms: species, breed, allergies.
$species_terms  = wp_get_post_terms( $pet_id, 'species', array( 'fields' => 'names' ) );
$breed_terms    = wp_get_post_terms( $pet_id, 'breed', array( 'fields' => 'names' ) );
$allergy_terms  = wp_get_post_terms( $pet_id, 'allergies', array( 'fields' => 'names' ) );

// Retrieve custom meta fields.
$date_of_birth     = get_post_meta( $pet_id, 'date_of_birth', true );
$sex               = get_post_meta( $pet_id, 'sex', true );
$weight            = get_post_meta( $pet_id, 'weight', true );
$microchip_number  = get_post_meta( $pet_id, 'microchip_number', true );
$spayed_neutered   = get_post_meta( $pet_id, 'spayed_neutered', true );
$last_visit        = get_post_meta( $pet_id, 'last_visit_date', true );
$next_appointment  = get_post_meta( $pet_id, 'next_appointment', true );
$medical_notes     = get_post_meta( $pet_id, 'medical_notes', true );

// Calculate age if DOB is available
$age_string = '';
if ( $date_of_birth ) {
  $birth_date = new DateTime( $date_of_birth );
  $current_date = new DateTime();
  $age = $birth_date->diff( $current_date );
  
  if ( $age->y > 0 ) {
    $age_string = $age->y . ' year' . ( $age->y > 1 ? 's' : '' );
    if ( $age->m > 0 ) {
      $age_string .= ', ' . $age->m . ' month' . ( $age->m > 1 ? 's' : '' );
    }
  } elseif ( $age->m > 0 ) {
    $age_string = $age->m . ' month' . ( $age->m > 1 ? 's' : '' );
  } else {
    $age_string = $age->d . ' day' . ( $age->d > 1 ? 's' : '' );
  }
}

// Get recent visits count
$recent_visits = get_posts( array(
  'post_type'      => 'visit',
  'posts_per_page' => 5,
  'meta_key'       => 'pet_id',
  'meta_value'     => $pet_id,
  'orderby'        => 'date',
  'order'          => 'DESC'
) );

// Get pet's medical alerts/flags
$medical_alerts = get_post_meta( $pet_id, 'medical_alerts', true );
$is_urgent = get_post_meta( $pet_id, 'urgent', true );
?>

<!-- Pet Header Card -->
<div class="pet-header-card">
  <div class="pet-header-content">
    <!-- Pet Photo/Avatar -->
    <div class="pet-photo-container">
      <?php if ( has_post_thumbnail( $pet_id ) ) : ?>
        <div class="pet-photo">
          <?php echo get_the_post_thumbnail( $pet_id, 'medium' ); ?>
        </div>
      <?php else : ?>
        <div class="pet-avatar-fallback">
          <?php echo esc_html( strtoupper( substr( get_the_title(), 0, 1 ) ) ); ?>
        </div>
      <?php endif; ?>
      
      <!-- Status Indicators -->
      <?php if ( $is_urgent ) : ?>
      <div class="urgent-badge" title="Urgent Case">!</div>
      <?php endif; ?>
    </div>

    <!-- Pet Basic Info -->
    <div class="pet-basic-info">
      <div class="pet-header-title">
        <h2 class="pet-title"><?php echo esc_html( get_the_title() ); ?></h2>
        
        <?php if ( $sex ) : ?>
        <span class="gender-badge <?php echo strtolower( $sex ); ?>">
          <?php echo esc_html( $sex ); ?>
        </span>
        <?php endif; ?>
      </div>
      
      <div class="pet-meta">
        <?php if ( ! empty( $species_terms ) || ! empty( $breed_terms ) ) : ?>
        <div class="breed-info">
          <?php 
          if ( ! empty( $species_terms ) ) {
            echo esc_html( $species_terms[0] );
            if ( ! empty( $breed_terms ) ) {
              echo ' • ' . esc_html( $breed_terms[0] );
            }
          }
          ?>
        </div>
        <?php endif; ?>
        
        <?php if ( $age_string ) : ?>
        <div class="age-info">
          <?php echo esc_html( $age_string ); ?> old
        </div>
        <?php endif; ?>
        
        <?php if ( $weight ) : ?>
        <div class="weight-info">
          <?php echo esc_html( $weight ); ?>
        </div>
        <?php endif; ?>
      </div>
      
      <!-- Owner Info -->
      <?php if ( $owner ) : ?>
      <div class="owner-info">
        <div class="owner-avatar">
          <?php echo esc_html( strtoupper( substr( $owner->display_name, 0, 1 ) ) ); ?>
        </div>
        <div class="owner-details">
          <div class="owner-name">
            Owner: <?php echo esc_html( $owner->display_name ); ?>
          </div>
          <div class="owner-email">
            <?php echo esc_html( $owner->user_email ); ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Quick Action Buttons -->
  <div class="quick-actions">
    <button class="action-btn visits" onclick="switchToTab('visits')">
      📋 View Visits (<?php echo count( $recent_visits ); ?>)
    </button>
    
    <button class="action-btn edit edit-profile-btn" data-pet-id="<?php echo esc_attr( $pet_id ); ?>">
      ✏️ Edit Profile
    </button>
    
    <button class="action-btn files" onclick="switchToTab('files')">
      📁 Medical Files
    </button>
  </div>
</div>

<!-- Medical Alerts -->
<?php if ( $medical_alerts || ! empty( $allergy_terms ) ) : ?>
<div class="medical-alerts">
  <h3>⚠️ Medical Alerts</h3>
  
  <?php if ( ! empty( $allergy_terms ) ) : ?>
  <div class="allergies">
    <strong>Allergies:</strong>
    <div class="allergy-tags">
      <?php foreach ( $allergy_terms as $allergy ) : ?>
      <span class="allergy-tag"><?php echo esc_html( $allergy ); ?></span>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
  
  <?php if ( $medical_alerts ) : ?>
  <div class="alerts">
    <strong>Notes:</strong>
    <p class="alert-notes">
      <?php echo esc_html( $medical_alerts ); ?>
    </p>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- Detailed Information Grid -->
<div class="pet-details-grid">
  <!-- Basic Information Card -->
  <div class="info-card">
    <h3>📋 Basic Information</h3>
    
    <div class="info-list">
      <?php if ( $date_of_birth ) : ?>
      <div class="info-item">
        <span class="info-label">Date of Birth</span>
        <span class="info-value">
          <?php echo esc_html( date( 'M j, Y', strtotime( $date_of_birth ) ) ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <?php if ( $microchip_number ) : ?>
      <div class="info-item">
        <span class="info-label">Microchip #</span>
        <span class="info-value microchip">
          <?php echo esc_html( $microchip_number ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <div class="info-item">
        <span class="info-label">Spayed/Neutered</span>
        <span class="info-value badge <?php echo $spayed_neutered ? 'yes' : 'no'; ?>">
          <?php echo $spayed_neutered ? 'YES' : 'NO'; ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Medical History Card -->
  <div class="info-card">
    <h3>🩺 Medical History</h3>
    
    <div class="info-list">
      <?php if ( $last_visit ) : ?>
      <div class="info-item">
        <span class="info-label">Last Visit</span>
        <span class="info-value">
          <?php echo esc_html( date( 'M j, Y', strtotime( $last_visit ) ) ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <?php if ( $next_appointment ) : ?>
      <div class="info-item">
        <span class="info-label">Next Appointment</span>
        <span class="info-value appointment">
          <?php echo esc_html( date( 'M j, Y', strtotime( $next_appointment ) ) ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <div class="info-item">
        <span class="info-label">Total Visits</span>
        <span class="info-value visit-count">
          <?php echo count( $recent_visits ); ?>
        </span>
      </div>
    </div>
  </div>
</div>

<?php if ( $medical_notes ) : ?>
<!-- Medical Notes -->
<div class="medical-notes">
  <h3>📝 Medical Notes</h3>
  <div class="notes-content">
    <?php echo wp_kses_post( wpautop( $medical_notes ) ); ?>
  </div>
</div>
<?php endif; ?>

<script>
function switchToTab(tabName) {
  var tabButton = document.getElementById('tab-' + tabName);
  if (tabButton) {
    tabButton.click();
  }
}
</script>