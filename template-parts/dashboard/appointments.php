<?php
// FILE: appointments.php
// LOCATION: Put this in your WordPress theme directory OR as a custom page
// PURPOSE: Ultra-fast appointment system using WordPress database

// WordPress Bootstrap - REQUIRED for WordPress
require_once('../../../wp-config.php'); // Adjust path to your wp-config.php
// OR if this is in theme directory: require_once(ABSPATH . 'wp-config.php');

// WordPress database access
global $wpdb;

// Check if user is logged in (WordPress way)
if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_date = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : date('Y-m-d');
$view = isset($_GET['view']) ? sanitize_text_field($_GET['view']) : 'day';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Vet Dashboard</title>
    
    <!-- PERFORMANCE: Inline CSS for instant load -->
    <style>
    /* SPEED OPTIMIZED CSS - Same as before but WordPress compatible */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body { 
        font: 14px -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
        background: #f5f7fa;
        color: #2d3748;
    }
    
    .container { 
        display: flex;
        min-height: 100vh;
    }
    
    /* Match your existing sidebar */
    .sidebar {
        width: 250px;
        background: #667eea;
        color: white;
        padding: 20px 0;
    }
    
    .nav-item {
        padding: 12px 20px;
        cursor: pointer;
        transition: background 0.1s;
    }
    
    .nav-item:hover, .nav-item.active {
        background: rgba(255,255,255,0.1);
    }
    
    .main {
        flex: 1;
        padding: 20px;
        overflow-x: hidden;
    }
    
    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        background: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .view-toggle {
        display: flex;
        gap: 8px;
    }
    
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: transform 0.1s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn:hover { transform: translateY(-1px); }
    .btn-primary { background: #667eea; color: white; }
    .btn-secondary { background: #e2e8f0; color: #4a5568; }
    .btn.active { background: #5a67d8; }
    
    /* Stats */
    .stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }
    
    .stat-icon.today { background: #48bb78; }
    .stat-icon.week { background: #667eea; }
    .stat-icon.month { background: #ed8936; }
    .stat-icon.rate { background: #9f7aea; }
    
    .stat-info h3 { font-size: 24px; margin-bottom: 4px; }
    .stat-info p { color: #718096; font-size: 13px; }
    
    /* Filters */
    .filters {
        background: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .search-box {
        position: relative;
        flex: 1;
        min-width: 250px;
    }
    
    .search-box input {
        width: 100%;
        padding: 8px 12px 8px 36px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
    }
    
    .search-box::before {
        content: "🔍";
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
    }
    
    select, input[type="date"] {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-size: 14px;
        background: white;
    }
    
    /* Day View */
    .day-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        padding: 16px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 16px;
    }
    
    .nav-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #667eea;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
    }
    
    #appointments-list {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        min-height: 400px;
        padding: 20px;
    }
    
    /* Appointment Cards */
    .apt-card {
        background: #f8fafc;
        border-left: 4px solid #48bb78;
        padding: 16px;
        margin-bottom: 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: transform 0.1s;
    }
    
    .apt-card:hover { transform: translateX(4px); }
    .apt-card.completed { border-left-color: #4299e1; }
    .apt-card.cancelled { border-left-color: #f56565; }
    .apt-card.rescheduled { border-left-color: #ed8936; }
    
    .apt-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .apt-info h4 { font-size: 16px; margin-bottom: 4px; }
    .apt-info p { color: #718096; font-size: 13px; }
    
    .apt-time {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .apt-details {
        display: flex;
        gap: 16px;
        font-size: 13px;
        color: #718096;
        margin-bottom: 8px;
    }
    
    .apt-actions {
        display: flex;
        gap: 6px;
    }
    
    .action-btn {
        padding: 4px 8px;
        border: none;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
        text-transform: uppercase;
        font-weight: 600;
    }
    
    .action-btn.complete { background: #4299e1; color: white; }
    .action-btn.cancel { background: #f56565; color: white; }
    .action-btn.reschedule { background: #ed8936; color: white; }
    
    /* Loading */
    .loading { 
        text-align: center; 
        padding: 40px; 
        color: #a0aec0; 
    }
    
    .spinner {
        border: 2px solid #e2e8f0;
        border-top: 2px solid #667eea;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 8px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .hidden { display: none; }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .container { flex-direction: column; }
        .sidebar { width: 100%; }
        .filters { flex-direction: column; align-items: stretch; }
        .search-box { min-width: auto; }
        .stats { grid-template-columns: repeat(2, 1fr); }
    }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar matching your existing design -->
        <div class="sidebar">
            <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <h2>Vet Dashboard</h2>
            </div>
            
            <div class="nav-item">
                <span>🏠</span> Dashboard
            </div>
            
            <div class="nav-item">
                <span>🐾</span> All Pets
                <span style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: auto;">
                    <?php
                    // Count WordPress posts with post_type 'pet' or custom table
                    $pet_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'pet' AND post_status = 'publish'");
                    echo $pet_count ?: '2'; // fallback to 2 if no custom post type
                    ?>
                </span>
            </div>
            
            <div class="nav-item active">
                <span>📅</span> All Appointments
                <span id="sidebar-apt-count" style="background: #f56565; padding: 2px 6px; border-radius: 10px; font-size: 11px; margin-left: auto;">0</span>
            </div>
        </div>

        <div class="main">
            <!-- Header -->
            <div class="header">
                <div>
                    <h1>📅 All Appointments</h1>
                    <div class="view-toggle">
                        <button class="btn btn-primary active" onclick="switchView('day')">📋 Day View</button>
                        <button class="btn btn-secondary" onclick="switchView('month')">📅 Month View</button>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="openQuickAdd()">+ New Appointment</button>
            </div>

            <!-- Stats -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-icon today">📅</div>
                    <div class="stat-info">
                        <h3 id="today-count">0</h3>
                        <p>Today</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon week">📊</div>
                    <div class="stat-info">
                        <h3 id="week-count">0</h3>
                        <p>This Week</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon month">📈</div>
                    <div class="stat-info">
                        <h3 id="month-count">0</h3>
                        <p>This Month</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rate">✅</div>
                    <div class="stat-info">
                        <h3 id="completion-rate">0%</h3>
                        <p>Completed</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="search" placeholder="Search pets, owners..." onkeyup="filterAppointments()">
                </div>
                <select id="status-filter" onchange="filterAppointments()">
                    <option value="">All Status</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select id="vet-filter" onchange="filterAppointments()">
                    <option value="">All Vets</option>
                </select>
                <input type="date" id="date-filter" value="<?php echo esc_attr($current_date); ?>" onchange="changeDate(this.value)">
            </div>

            <!-- Day View -->
            <div id="day-view">
                <div class="day-header">
                    <button class="nav-btn" onclick="navigateDay(-1)">‹</button>
                    <h2 id="current-day"><?php echo date('l, F j, Y', strtotime($current_date)); ?></h2>
                    <button class="nav-btn" onclick="navigateDay(1)">›</button>
                </div>
                
                <div id="appointments-list">
                    <div class="loading">
                        <div class="spinner"></div>
                        Loading appointments...
                    </div>
                </div>
            </div>

            <!-- Month View -->
            <div id="month-view" class="hidden">
                <div class="day-header">
                    <button class="nav-btn" onclick="navigateMonth(-1)">‹</button>
                    <h2 id="current-month"><?php echo date('F Y', strtotime($current_date)); ?></h2>
                    <button class="nav-btn" onclick="navigateMonth(1)">›</button>
                </div>
                
                <div id="calendar-container">
                    <!-- Calendar loads here -->
                </div>
            </div>
        </div>
    </div>

    <!-- PERFORMANCE CRITICAL: Inline JavaScript -->
    <script>
    // WordPress AJAX endpoint
    const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
    const nonce = '<?php echo wp_create_nonce('vet_appointments_nonce'); ?>';
    
    let currentDate = new Date('<?php echo $current_date; ?>');
    let currentView = '<?php echo $view; ?>';
    let appointments = [];
    let pets = [];
    let vets = [];
    let isLoading = false;

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadData();
    });

    // PERFORMANCE: Single WordPress AJAX call
    async function loadData() {
        if (isLoading) return;
        isLoading = true;
        
        try {
            const formData = new FormData();
            formData.append('action', 'vet_get_all_data');
            formData.append('nonce', nonce);
            formData.append('date', formatDate(currentDate));
            formData.append('view', currentView);
            
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                appointments = data.data.appointments || [];
                pets = data.data.pets || [];
                vets = data.data.vets || [];
                
                updateStats(data.data.stats);
                populateFilters();
                renderAppointments();
            } else {
                showError('Failed to load data: ' + (data.data || 'Unknown error'));
            }
        } catch (error) {
            console.error('Load error:', error);
            showError('Network error loading data');
        } finally {
            isLoading = false;
        }
    }

    function updateStats(stats) {
        document.getElementById('today-count').textContent = stats.today || 0;
        document.getElementById('week-count').textContent = stats.week || 0;
        document.getElementById('month-count').textContent = stats.month || 0;
        document.getElementById('completion-rate').textContent = (stats.completion_rate || 0) + '%';
        document.getElementById('sidebar-apt-count').textContent = stats.today || 0;
    }

    function populateFilters() {
        const vetFilter = document.getElementById('vet-filter');
        vetFilter.innerHTML = '<option value="">All Vets</option>';
        vets.forEach(vet => {
            vetFilter.innerHTML += `<option value="${vet.id}">${vet.name}</option>`;
        });
    }

    function renderAppointments() {
        const container = document.getElementById('appointments-list');
        
        if (appointments.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #a0aec0;">
                    <div style="font-size: 48px; margin-bottom: 16px;">📅</div>
                    <h3>No appointments today</h3>
                    <p>No appointments scheduled for ${formatLongDate(currentDate)}</p>
                </div>
            `;
            return;
        }

        appointments.sort((a, b) => a.appointment_time.localeCompare(b.appointment_time));

        let html = '';
        appointments.forEach(apt => {
            html += `
                <div class="apt-card ${apt.status}" onclick="openDetails(${apt.id})">
                    <div class="apt-header">
                        <div class="apt-info">
                            <h4>${apt.pet_name}</h4>
                            <p>👤 ${apt.owner_name}</p>
                        </div>
                        <div class="apt-time">${formatTime(apt.appointment_time)}</div>
                    </div>
                    <div class="apt-details">
                        <span>🩺 ${apt.appointment_type}</span>
                        <span>👨‍⚕️ ${apt.vet_name || 'Not assigned'}</span>
                        <span>⏱️ ${apt.duration}min</span>
                    </div>
                    <div class="apt-actions">
                        ${generateActions(apt)}
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }

    function generateActions(apt) {
        let actions = '';
        switch (apt.status) {
            case 'confirmed':
                actions = `
                    <button class="action-btn complete" onclick="updateStatus(${apt.id}, 'completed', event)">Complete</button>
                    <button class="action-btn reschedule" onclick="reschedule(${apt.id}, event)">Reschedule</button>
                    <button class="action-btn cancel" onclick="updateStatus(${apt.id}, 'cancelled', event)">Cancel</button>
                `;
                break;
            case 'rescheduled':
                actions = `
                    <button class="action-btn complete" onclick="updateStatus(${apt.id}, 'confirmed', event)">Confirm</button>
                    <button class="action-btn cancel" onclick="updateStatus(${apt.id}, 'cancelled', event)">Cancel</button>
                `;
                break;
        }
        return actions;
    }

    // WordPress AJAX functions
    async function updateStatus(id, status, event) {
        event.stopPropagation();
        
        try {
            const formData = new FormData();
            formData.append('action', 'vet_update_status');
            formData.append('nonce', nonce);
            formData.append('id', id);
            formData.append('status', status);
            
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (result.success) {
                showSuccess(`Appointment ${status}!`);
                loadData();
            } else {
                showError('Update failed');
            }
        } catch (error) {
            showError('Network error');
        }
    }

    function openQuickAdd() {
        const petName = prompt('Pet name:');
        const ownerName = prompt('Owner name:');
        const type = prompt('Type (checkup, vaccination, surgery, emergency):') || 'checkup';
        const time = prompt('Time (HH:MM):') || '09:00';
        
        if (petName && ownerName) {
            createAppointment({
                pet_name: petName,
                owner_name: ownerName,
                appointment_type: type,
                appointment_date: formatDate(currentDate),
                appointment_time: time
            });
        }
    }

    async function createAppointment(data) {
        try {
            const formData = new FormData();
            formData.append('action', 'vet_create_appointment');
            formData.append('nonce', nonce);
            Object.keys(data).forEach(key => {
                formData.append(key, data[key]);
            });
            
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            if (result.success) {
                showSuccess('Appointment created!');
                loadData();
            } else {
                showError('Failed to create appointment');
            }
        } catch (error) {
            showError('Network error');
        }
    }

    // Navigation and utility functions (same as before)
    function switchView(view) {
        currentView = view;
        document.getElementById('day-view').classList.toggle('hidden', view !== 'day');
        document.getElementById('month-view').classList.toggle('hidden', view !== 'month');
        
        document.querySelectorAll('.view-toggle .btn').forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
            btn.classList.add('btn-secondary');
        });
        event.target.classList.add('active', 'btn-primary');
        event.target.classList.remove('btn-secondary');
        
        loadData();
    }

    function navigateDay(direction) {
        currentDate.setDate(currentDate.getDate() + direction);
        updateDateDisplay();
        loadData();
    }

    function navigateMonth(direction) {
        currentDate.setMonth(currentDate.getMonth() + direction);
        updateDateDisplay();
        loadData();
    }

    function changeDate(dateStr) {
        currentDate = new Date(dateStr);
        updateDateDisplay();
        loadData();
    }

    function updateDateDisplay() {
        document.getElementById('current-day').textContent = formatLongDate(currentDate);
        document.getElementById('current-month').textContent = formatMonthYear(currentDate);
        document.getElementById('date-filter').value = formatDate(currentDate);
    }

    let filterTimeout;
    function filterAppointments() {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            const search = document.getElementById('search').value.toLowerCase();
            const status = document.getElementById('status-filter').value;
            const vet = document.getElementById('vet-filter').value;
            
            const filtered = appointments.filter(apt => {
                const matchSearch = !search || 
                    apt.pet_name.toLowerCase().includes(search) ||
                    apt.owner_name.toLowerCase().includes(search);
                const matchStatus = !status || apt.status === status;
                const matchVet = !vet || apt.vet_id == vet;
                
                return matchSearch && matchStatus && matchVet;
            });
            
            const original = appointments;
            appointments = filtered;
            renderAppointments();
            appointments = original;
        }, 200);
    }

    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    function formatLongDate(date) {
        return date.toLocaleDateString('en-US', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    function formatMonthYear(date) {
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
    }

    function formatTime(timeStr) {
        const [hours, minutes] = timeStr.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes));
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric', minute: '2-digit', hour12: true
        });
    }

    function showSuccess(msg) {
        const div = document.createElement('div');
        div.style.cssText = 'position:fixed;top:20px;right:20px;background:#48bb78;color:white;padding:12px 20px;border-radius:6px;z-index:1000;';
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    }

    function showError(msg) {
        const div = document.createElement('div');
        div.style.cssText = 'position:fixed;top:20px;right:20px;background:#f56565;color:white;padding:12px 20px;border-radius:6px;z-index:1000;';
        div.textContent = msg;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    }

    function openDetails(id) {
        const apt = appointments.find(a => a.id === id);
        if (apt) {
            alert(`Appointment Details:\n\nPet: ${apt.pet_name}\nOwner: ${apt.owner_name}\nDate: ${apt.appointment_date}\nTime: ${formatTime(apt.appointment_time)}\nType: ${apt.appointment_type}\nStatus: ${apt.status}\n\n${apt.notes || 'No notes'}`);
        }
    }
    </script>
</body>
</html>