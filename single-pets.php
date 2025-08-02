<?php
/**
 * The template for displaying a single Pet record (CPT 'pets').
 */

get_header();

// Ensure we have the global post object
global $post;

// Pet ID and title
$pet_id   = $post->ID;
$pet_name = get_the_title( $post );

?>
<div class="container mx-auto p-6">
  <!-- Breadcrumb -->
  <nav class="text-sm mb-4">
    <a href="<?php echo esc_url( home_url('clients') ); ?>">Clients</a>
    &raquo;
    <span>Pet #<?php echo esc_html( $pet_id ); ?></span>
  </nav>

  <!-- Pet Snapshot Card -->
  <div class="grid md:grid-cols-3 gap-6 mb-6">
    <div class="col-span-2 bg-white p-4 rounded shadow">
      <h1 class="text-2xl font-bold mb-2">Pet #<?php echo esc_html( $pet_id ); ?>: <?php echo esc_html( $pet_name ); ?></h1>
      <!-- Additional pet details go in template-part 'overview' -->
      <?php get_template_part( 'template-parts/pet/overview' ); ?>
    </div>
  </div>

  <!-- Tabs Navigation -->
  <div class="pet-tabs mb-4">
    <ul class="flex border-b">
      <li class="mr-4"><a href="#overview" class="tab-link active">Overview</a></li>
      <li class="mr-4"><a href="#visits" class="tab-link">Visits</a></li>
      <li class="mr-4"><a href="#files" class="tab-link">Files</a></li>
      <li><a href="#comments" class="tab-link">Comments</a></li>
    </ul>
  </div>

  <!-- Tabs Content -->
  <div class="tab-content">
    <div id="overview" class="tab-panel">
      <?php get_template_part( 'template-parts/pet/overview' ); ?>
    </div>
    <div id="visits" class="tab-panel hidden">
      <?php get_template_part( 'template-parts/pet/visits' ); ?>
    </div>
    <div id="files" class="tab-panel hidden">
      <?php get_template_part( 'template-parts/pet/files' ); ?>
    </div>
    <div id="comments" class="tab-panel hidden">
      <?php get_template_part( 'template-parts/pet/comments' ); ?>
    </div>
  </div>
</div>

<script>
// Tab switching logic
document.addEventListener('DOMContentLoaded', function() {
  var links = document.querySelectorAll('.tab-link');
  links.forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      var target = this.getAttribute('href').substring(1);
      // Hide all panels
      document.querySelectorAll('.tab-panel').forEach(function(panel) {
        panel.classList.add('hidden');
      });
      // Remove active class from all links
      links.forEach(function(l) {
        l.classList.remove('active');
      });
      // Show the selected panel
      document.getElementById(target).classList.remove('hidden');
      this.classList.add('active');
    });
  });
});
</script>

<?php
get_footer();
