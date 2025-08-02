<?php
/**
 * Dashboard Panel: Comments
 * Displays and manages doctor’s notes for the selected pet.
 * Hidden by default; shown when a pet is clicked.
 */
?>
<section id="comments-panel" class="dashboard-panel comments-panel hidden">
  <!-- Panel header -->
  <h2 class="text-2xl font-semibold mb-4">Comments</h2>
  <!-- Add Comment button -->
  <button id="add-comment-btn" class="mb-4 px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600">
    Add Comment
  </button>
  <!-- Comments content placeholder -->
  <div id="comments-content">
    <!-- TODO: Populate with comment entries via JS or PHP -->
  </div>
</section>
