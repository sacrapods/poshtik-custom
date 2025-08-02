<?php
/**
 * Enhanced Template Part: Pet Overview
 * Modern, beautiful pet details display with interactive elements.
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
<div class="pet-header-card" style="
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
  border: 1px solid rgba(102, 126, 234, 0.2);
  border-radius: 20px;
  padding: 2rem;
  margin-bottom: 2rem;
  backdrop-filter: blur(10px);
  box-shadow: 0 8px 32px rgba(102, 126, 234, 0.1);
">
  <div style="display: flex; align-items: center; gap: 2rem; margin-bottom: 1.5rem;">
    <!-- Pet Photo/Avatar -->
    <div class="pet-photo-container" style="
      position: relative;
      flex-shrink: 0;
    ">
      <?php if ( has_post_thumbnail( $pet_id ) ) : ?>
        <div style="
          width: 120px;
          height: 120px;
          border-radius: 20px;
          overflow: hidden;
          box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
          border: 3px solid rgba(255, 255, 255, 0.3);
        ">
          <?php echo get_the_post_thumbnail( $pet_id, 'medium', array( 
            'style' => 'width: 100%; height: 100%; object-fit: cover;'
          ) ); ?>
        </div>
      <?php else : ?>
        <div style="
          width: 120px;
          height: 120px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-radius: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 3rem;
          font-weight: 600;
          box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
        ">
          <?php echo esc_html( strtoupper( substr( get_the_title(), 0, 1 ) ) ); ?>
        </div>
      <?php endif; ?>
      
      <!-- Status Indicators -->
      <?php if ( $is_urgent ) : ?>
      <div class="urgent-badge" style="
        position: absolute;
        top: -8px;
        right: -8px;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(240, 147, 251, 0.5);
        animation: pulse 2s infinite;
      " title="Urgent Case">!</div>
      <?php endif; ?>
    </div>

    <!-- Pet Basic Info -->
    <div style="flex: 1;">
      <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
        <h2 style="
          font-size: 2rem;
          font-weight: 700;
          color: var(--text-primary);
          margin: 0;
        "><?php echo esc_html( get_the_title() ); ?></h2>
        
        <?php if ( $sex ) : ?>
        <span class="gender-badge" style="
          background: <?php echo $sex === 'Male' ? '#3b82f6' : '#ec4899'; ?>;
          color: white;
          font-size: 0.75rem;
          font-weight: 600;
          padding: 0.25rem 0.75rem;
          border-radius: 12px;
          text-transform: uppercase;
        "><?php echo esc_html( $sex ); ?></span>
        <?php endif; ?>
      </div>
      
      <div class="pet-meta" style="
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
      ">
        <?php if ( ! empty( $species_terms ) || ! empty( $breed_terms ) ) : ?>
        <div class="breed-info" style="
          background: rgba(255, 255, 255, 0.7);
          padding: 0.5rem 1rem;
          border-radius: 12px;
          font-weight: 500;
          color: var(--text-primary);
          backdrop-filter: blur(10px);
        ">
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
        <div class="age-info" style="
          background: rgba(255, 255, 255, 0.7);
          padding: 0.5rem 1rem;
          border-radius: 12px;
          font-weight: 500;
          color: var(--text-primary);
          backdrop-filter: blur(10px);
        ">
          <?php echo esc_html( $age_string ); ?> old
        </div>
        <?php endif; ?>
        
        <?php if ( $weight ) : ?>
        <div class="weight-info" style="
          background: rgba(255, 255, 255, 0.7);
          padding: 0.5rem 1rem;
          border-radius: 12px;
          font-weight: 500;
          color: var(--text-primary);
          backdrop-filter: blur(10px);
        ">
          <?php echo esc_html( $weight ); ?>
        </div>
        <?php endif; ?>
      </div>
      
      <!-- Owner Info -->
      <?php if ( $owner ) : ?>
      <div class="owner-info" style="
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 12px;
        backdrop-filter: blur(10px);
      ">
        <div style="
          width: 40px;
          height: 40px;
          background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
          border-radius: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-weight: 600;
        ">
          <?php echo esc_html( strtoupper( substr( $owner->display_name, 0, 1 ) ) ); ?>
        </div>
        <div>
          <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.125rem;">
            Owner: <?php echo esc_html( $owner->display_name ); ?>
          </div>
          <div style="font-size: 0.875rem; color: var(--text-secondary);">
            <?php echo esc_html( $owner->user_email ); ?>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Quick Action Buttons -->
  <div class="quick-actions" style="
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
  ">
    <button class="action-btn" onclick="switchToTab('visits')" style="
      background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(67, 233, 123, 0.3);
    ">
      📋 View Visits (<?php echo count( $recent_visits ); ?>)
    </button>
    
    <button class="action-btn edit-profile-btn" data-pet-id="<?php echo esc_attr( $pet_id ); ?>" style="
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    ">
      ✏️ Edit Profile
    </button>
    
    <button class="action-btn" onclick="switchToTab('files')" style="
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
    ">
      📁 Medical Files
    </button>
  </div>
</div>

<!-- Medical Alerts -->
<?php if ( $medical_alerts || ! empty( $allergy_terms ) ) : ?>
<div class="medical-alerts" style="
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  border-radius: 16px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  backdrop-filter: blur(10px);
">
  <h3 style="
    color: #dc2626;
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  ">
    ⚠️ Medical Alerts
  </h3>
  
  <?php if ( ! empty( $allergy_terms ) ) : ?>
  <div class="allergies" style="margin-bottom: 1rem;">
    <strong style="color: #991b1b;">Allergies:</strong>
    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
      <?php foreach ( $allergy_terms as $allergy ) : ?>
      <span style="
        background: rgba(239, 68, 68, 0.2);
        color: #991b1b;
        padding: 0.25rem 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
      "><?php echo esc_html( $allergy ); ?></span>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
  
  <?php if ( $medical_alerts ) : ?>
  <div class="alerts">
    <strong style="color: #991b1b;">Notes:</strong>
    <p style="color: #7f1d1d; margin-top: 0.5rem; line-height: 1.5;">
      <?php echo esc_html( $medical_alerts ); ?>
    </p>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>

<!-- Detailed Information Grid -->
<div class="pet-details-grid" style="
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
">
  <!-- Basic Information Card -->
  <div class="info-card" style="
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  ">
    <h3 style="
      color: var(--text-primary);
      font-weight: 700;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    ">
      📋 Basic Information
    </h3>
    
    <div class="info-list">
      <?php if ( $date_of_birth ) : ?>
      <div class="info-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      ">
        <span style="color: var(--text-secondary); font-weight: 500;">Date of Birth</span>
        <span style="color: var(--text-primary); font-weight: 600;">
          <?php echo esc_html( date( 'M j, Y', strtotime( $date_of_birth ) ) ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <?php if ( $microchip_number ) : ?>
      <div class="info-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      ">
        <span style="color: var(--text-secondary); font-weight: 500;">Microchip #</span>
        <span style="
          color: var(--text-primary); 
          font-weight: 600;
          font-family: monospace;
          background: rgba(102, 126, 234, 0.1);
          padding: 0.25rem 0.5rem;
          border-radius: 6px;
        ">
          <?php echo esc_html( $microchip_number ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <div class="info-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      ">
        <span style="color: var(--text-secondary); font-weight: 500;">Spayed/Neutered</span>
        <span style="
          color: white;
          font-weight: 600;
          font-size: 0.75rem;
          padding: 0.25rem 0.75rem;
          border-radius: 12px;
          background: <?php echo $spayed_neutered ? 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)' : 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'; ?>;
        ">
          <?php echo $spayed_neutered ? 'YES' : 'NO'; ?>
        </span>
      </div>
    </div>
  </div>

  <!-- Medical History Card -->
  <div class="info-card" style="
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  ">
    <h3 style="
      color: var(--text-primary);
      font-weight: 700;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    ">
      🩺 Medical History
    </h3>
    
    <div class="info-list">
      <?php if ( $last_visit ) : ?>
      <div class="info-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      ">
        <span style="color: var(--text-secondary); font-weight: 500;">Last Visit</span>
        <span style="color: var(--text-primary); font-weight: 600;">
          <?php echo esc_html( date( 'M j, Y', strtotime( $last_visit ) ) ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <?php if ( $next_appointment ) : ?>
      <div class="info-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      ">
        <span style="color: var(--text-secondary); font-weight: 500;">Next Appointment</span>
        <span style="
          color: white;
          font-weight: 600;
          font-size: 0.875rem;
          padding: 0.5rem 1rem;
          border-radius: 8px;
          background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        ">
          <?php echo esc_html( date( 'M j, Y', strtotime( $next_appointment ) ) ); ?>
        </span>
      </div>
      <?php endif; ?>
      
      <div class="info-item" style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
      ">
        <span style="color: var(--text-secondary); font-weight: 500;">Total Visits</span>
        <span style="
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          font-weight: 600;
          font-size: 0.875rem;
          padding: 0.5rem 1rem;
          border-radius: 8px;
        ">
          <?php echo count( $recent_visits ); ?>
        </span>
      </div>
    </div>
  </div>
</div>

<?php if ( $medical_notes ) : ?>
<!-- Medical Notes -->
<div class="medical-notes" style="
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 16px;
  padding: 1.5rem;
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
">
  <h3 style="
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  ">
    📝 Medical Notes
  </h3>
  <div style="
    background: rgba(102, 126, 234, 0.05);
    border-left: 4px solid #667eea;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
    color: var(--text-secondary);
    line-height: 1.6;
  ">
    <?php echo wp_kses_post( wpautop( $medical_notes ) ); ?>
  </div>
</div>
<?php endif; ?>

<style>
.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15) !important;
}

.info-item:last-child {
  border-bottom: none !important;
}

@media (max-width: 768px) {
  .pet-header-card > div:first-child {
    flex-direction: column;
    text-align: center;
    gap: 1rem !important;
  }
  
  .quick-actions {
    justify-content: center;
  }
  
  .pet-details-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<script>
function switchToTab(tabName) {
  var tabButton = document.getElementById('tab-' + tabName);
  if (tabButton) {
    tabButton.click();
  }
}
</script>