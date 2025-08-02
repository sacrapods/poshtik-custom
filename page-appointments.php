<?php
/**
 * Template Name: Appointments Dashboard
 * Description: Full appointments management system for veterinarians
 */

// Security check - only logged in users with proper permissions
if ( ! is_user_logged_in() ) {
    wp_redirect( home_url( 'login' ) );
    exit;
}

// Check if user has vet or admin role
$current_user = wp_get_current_user();
$allowed_roles = array('administrator', 'vet', 'editor');
if ( ! array_intersect($allowed_roles, $current_user->roles) ) {
    wp_die('Access denied. You must be a veterinarian or administrator to access this page.');
}

// Don't show admin bar on this page
show_admin_bar(false);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointments Dashboard - <?php bloginfo( 'name' ); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>
<body class="appointments-dashboard">
    <!-- Header -->
    <header class="glass-card sticky top-0 z-50 rounded-none border-x-0 border-t-0">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center space-x-4">
                <button id="sidebarToggle" class="p-2 rounded-lg bg-white/20 hover:bg-white/30 transition-colors">
                    <i class="fas fa-bars text-white"></i>
                </button>
                <h1 class="text-xl font-bold text-white">📅 Appointments Dashboard</h1>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-white text-sm">
                    <span><?php echo esc_html( $current_user->display_name ); ?></span>
                    <span class="opacity-70">• <?php echo esc_html( ucfirst( $current_user->roles[0] ) ); ?></span>
                </div>
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center text-white font-semibold">
                    <?php echo esc_html( strtoupper( substr( $current_user->display_name, 0, 1 ) ) ); ?>
                </div>
                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="text-white/70 hover:text-white transition-colors" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-80 glass-card m-4 rounded-2xl transition-all duration-300 appointments-sidebar">
            <div class="p-6">
                <!-- Quick Stats -->
                <div class="mb-6">
                    <h3 class="text-white font-semibold mb-4">Today's Overview</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/10 rounded-xl p-3 text-center">
                            <div class="text-2xl font-bold text-white" id="todayCount">0</div>
                            <div class="text-xs text-white/70">Today</div>
                        </div>
                        <div class="bg-white/10 rounded-xl p-3 text-center">
                            <div class="text-2xl font-bold text-white" id="pendingCount">0</div>
                            <div class="text-xs text-white/70">Pending</div>
                        </div>
                        <div class="bg-white/10 rounded-xl p-3 text-center">
                            <div class="text-2xl font-bold text-white" id="completedCount">0</div>
                            <div class="text-xs text-white/70">Completed</div>
                        </div>
                        <div class="bg-white/10 rounded-xl p-3 text-center">
                            <div class="text-2xl font-bold text-white" id="urgentCount">0</div>
                            <div class="text-xs text-white/70">Urgent</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mb-6">
                    <h3 class="text-white font-semibold mb-3">Quick Actions</h3>
                    <button id="newAppointmentBtn" class="w-full bg-white/20 hover:bg-white/30 text-white py-3 px-4 rounded-xl transition-colors flex items-center justify-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>New Appointment</span>
                    </button>
                </div>

                <!-- Upcoming Appointments -->
                <div class="mb-6">
                    <h3 class="text-white font-semibold mb-3">Next Appointments</h3>
                    <div class="space-y-2" id="upcomingList">
                        <div class="text-white/70 text-sm text-center py-4">Loading...</div>
                    </div>
                </div>

                <!-- Filters -->
                <div>
                    <h3 class="text-white font-semibold mb-3">Filters</h3>
                    <div class="space-y-2">
                        <select id="vetFilter" class="w-full bg-white/10 border-white/20 text-white rounded-lg p-2 text-sm">
                            <option value="">All Vets</option>
                            <?php
                            $vets = get_users(array('role' => 'vet'));
                            foreach ($vets as $vet) {
                                echo '<option value="' . esc_attr($vet->ID) . '">' . esc_html($vet->display_name) . '</option>';
                            }
                            ?>
                        </select>
                        <select id="statusFilter" class="w-full bg-white/10 border-white/20 text-white rounded-lg p-2 text-sm">
                            <option value="">All Status</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="rescheduled">Rescheduled</option>
                        </select>
                        <input type="date" id="dateFilter" class="w-full bg-white/10 border-white/20 text-white rounded-lg p-2 text-sm">
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-4">
            <!-- View Toggle -->
            <div class="glass-card rounded-2xl p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-1">
                        <button id="dayViewBtn" class="px-4 py-2 bg-white/20 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-list mr-2"></i>Day View
                        </button>
                        <button id="calendarViewBtn" class="px-4 py-2 text-white/70 hover:bg-white/10 rounded-lg font-medium transition-colors">
                            <i class="fas fa-calendar mr-2"></i>Calendar
                        </button>
                        <button id="historyViewBtn" class="px-4 py-2 text-white/70 hover:bg-white/10 rounded-lg font-medium transition-colors">
                            <i class="fas fa-history mr-2"></i>History
                        </button>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="prevDay" class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="currentDate" class="text-white font-medium px-4">Today</span>
                        <button id="nextDay" class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Day View -->
            <div id="dayView" class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-white">Today's Schedule</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-white/70 text-sm" id="appointmentCount">0 appointments</span>
                        <div class="flex space-x-1">
                            <div class="appointment-dot bg-blue-400"></div>
                            <div class="appointment-dot bg-green-400"></div>
                            <div class="appointment-dot bg-yellow-400"></div>
                            <div class="appointment-dot bg-red-400"></div>
                        </div>
                    </div>
                </div>
                <div class="space-y-4" id="appointmentsList">
                    <div class="text-center py-12 text-white/70">
                        <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                        <p class="text-lg mb-2">Loading appointments...</p>
                    </div>
                </div>
            </div>

            <!-- Calendar View -->
            <div id="calendarView" class="glass-card rounded-2xl p-6 hidden">
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white" id="calendarMonth">March 2024</h2>
                        <div class="flex items-center space-x-2">
                            <button id="prevMonth" class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button id="nextMonth" class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7 gap-1" id="calendarGrid">
                        <!-- Headers -->
                        <div class="p-2 text-center text-white/70 font-medium text-sm">Sun</div>
                        <div class="p-2 text-center text-white/70 font-medium text-sm">Mon</div>
                        <div class="p-2 text-center text-white/70 font-medium text-sm">Tue</div>
                        <div class="p-2 text-center text-white/70 font-medium text-sm">Wed</div>
                        <div class="p-2 text-center text-white/70 font-medium text-sm">Thu</div>
                        <div class="p-2 text-center text-white/70 font-medium text-sm">Fri</div>
                        <div class="p-2 text-center text-white/70 font-medium text-sm">Sat</div>
                    </div>
                </div>
            </div>

            <!-- History View -->
            <div id="historyView" class="glass-card rounded-2xl p-6 hidden">
                <h2 class="text-xl font-bold text-white mb-6">Activity History</h2>
                <div class="space-y-4" id="historyList">
                    <div class="text-center py-12 text-white/70">
                        <i class="fas fa-history text-4xl mb-4"></i>
                        <p class="text-lg mb-2">Loading history...</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals will be inserted here by JavaScript -->
    <div id="modalContainer"></div>

    <!-- WordPress Footer -->
    <script>
        // Pass WordPress data to JavaScript
        window.wpData = {
            ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('vet_appointments_nonce'); ?>',
            currentUser: {
                id: <?php echo get_current_user_id(); ?>,
                name: '<?php echo esc_js($current_user->display_name); ?>',
                role: '<?php echo esc_js($current_user->roles[0]); ?>'
            }
        };
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>