/**
 * Appointments Dashboard JavaScript
 * File: assets/js/appointments.js
 */

class AppointmentsDashboard {
    constructor() {
        this.currentView = 'day';
        this.currentDate = new Date();
        this.appointments = [];
        this.history = [];
        this.isLoading = false;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadInitialData();
        this.updateDateDisplay();
        this.createModals();
    }

    bindEvents() {
        // View switching
        document.getElementById('dayViewBtn')?.addEventListener('click', () => this.switchView('day'));
        document.getElementById('calendarViewBtn')?.addEventListener('click', () => this.switchView('calendar'));
        document.getElementById('historyViewBtn')?.addEventListener('click', () => this.switchView('history'));

        // Date navigation
        document.getElementById('prevDay')?.addEventListener('click', () => this.changeDate(-1));
        document.getElementById('nextDay')?.addEventListener('click', () => this.changeDate(1));
        document.getElementById('prevMonth')?.addEventListener('click', () => this.changeMonth(-1));
        document.getElementById('nextMonth')?.addEventListener('click', () => this.changeMonth(1));

        // Modal triggers
        document.getElementById('newAppointmentBtn')?.addEventListener('click', () => this.openNewAppointmentModal());

        // Filters
        document.getElementById('vetFilter')?.addEventListener('change', () => this.applyFilters());
        document.getElementById('statusFilter')?.addEventListener('change', () => this.applyFilters());
        document.getElementById('dateFilter')?.addEventListener('change', (e) => {
            if (e.target.value) {
                this.currentDate = new Date(e.target.value);
                this.updateDateDisplay();
                this.loadAppointments();
            }
        });

        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', this.toggleSidebar);

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboardShortcuts(e));
    }

    async loadInitialData() {
        await this.loadAppointments();
        this.updateStats();
        this.renderUpcoming();
    }

    async loadAppointments() {
        if (this.isLoading) return;
        this.isLoading = true;

        try {
            const formData = new FormData();
            formData.append('action', 'get_appointments');
            formData.append('nonce', window.wpData.nonce);
            formData.append('date', this.formatDate(this.currentDate));
            formData.append('view', this.currentView);

            const response = await fetch(window.wpData.ajaxUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.appointments = result.data.appointments || [];
                this.renderCurrentView();
            } else {
                this.showNotification('Failed to load appointments', 'error');
            }
        } catch (error) {
            console.error('Error loading appointments:', error);
            this.showNotification('Network error loading appointments', 'error');
        } finally {
            this.isLoading = false;
        }
    }

    async createAppointment(appointmentData) {
        try {
            const formData = new FormData();
            formData.append('action', 'create_appointment');
            formData.append('nonce', window.wpData.nonce);
            
            Object.keys(appointmentData).forEach(key => {
                formData.append(key, appointmentData[key]);
            });

            const response = await fetch(window.wpData.ajaxUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Appointment created successfully!', 'success');
                this.loadAppointments();
                this.closeModals();
            } else {
                this.showNotification(result.data || 'Failed to create appointment', 'error');
            }
        } catch (error) {
            console.error('Error creating appointment:', error);
            this.showNotification('Network error creating appointment', 'error');
        }
    }

    switchView(view) {
        this.currentView = view;
        
        // Update button states
        document.querySelectorAll('[id$="ViewBtn"]').forEach(btn => {
            btn.classList.remove('bg-white/20');
            btn.classList.add('text-white/70', 'hover:bg-white/10');
        });

        const activeBtn = document.getElementById(`${view}ViewBtn`);
        if (activeBtn) {
            activeBtn.classList.add('bg-white/20');
            activeBtn.classList.remove('text-white/70', 'hover:bg-white/10');
        }

        // Show/hide views
        document.getElementById('dayView')?.classList.toggle('hidden', view !== 'day');
        document.getElementById('calendarView')?.classList.toggle('hidden', view !== 'calendar');
        document.getElementById('historyView')?.classList.toggle('hidden', view !== 'history');

        this.renderCurrentView();
    }

    renderCurrentView() {
        switch (this.currentView) {
            case 'day':
                this.renderDayView();
                break;
            case 'calendar':
                this.renderCalendarView();
                break;
            case 'history':
                this.renderHistoryView();
                break;
        }
    }

    renderDayView() {
        const today = this.formatDate(this.currentDate);
        const todayAppointments = this.appointments.filter(apt => apt.appointment_date === today);
        const appointmentsList = document.getElementById('appointmentsList');
        
        if (!appointmentsList) return;

        if (todayAppointments.length === 0) {
            appointmentsList.innerHTML = `
                <div class="text-center py-12 text-white/70">
                    <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                    <p class="text-lg mb-2">No appointments today</p>
                    <p class="text-sm">Click "New Appointment" to schedule one</p>
                </div>
            `;
            return;
        }

        todayAppointments.sort((a, b) => a.appointment_time.localeCompare(b.appointment_time));

        appointmentsList.innerHTML = todayAppointments.map(apt => this.renderAppointmentCard(apt)).join('');
        
        // Update appointment count
        const countEl = document.getElementById('appointmentCount');
        if (countEl) {
            countEl.textContent = `${todayAppointments.length} appointment${todayAppointments.length !== 1 ? 's' : ''}`;
        }
    }

    renderAppointmentCard(apt) {
        return `
            <div class="bg-white/10 rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-colors fade-in" data-id="${apt.id}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-2 rounded-full ${this.getStatusColor(apt.status)}"></div>
                        <span class="text-white font-medium">${this.formatTime(apt.appointment_time)}</span>
                        ${apt.is_urgent ? '<i class="fas fa-exclamation-triangle text-red-400 text-sm" title="Urgent"></i>' : ''}
                    </div>
                    <span class="status-${apt.status} px-2 py-1 rounded-full text-xs font-medium border">
                        ${this.capitalizeFirst(apt.status)}
                    </span>
                </div>
                <div class="text-white mb-1">
                    <span class="font-medium">${apt.pet_name}</span> - ${apt.owner_name}
                </div>
                <div class="text-white/70 text-sm mb-2">
                    ${this.capitalizeFirst(apt.appointment_type)} • ${apt.vet_name || 'Unassigned'} • ${apt.duration}min
                </div>
                ${apt.notes ? `<div class="text-white/60 text-sm mb-3">${apt.notes}</div>` : ''}
                <div class="flex items-center justify-end space-x-2">
                    ${this.renderAppointmentActions(apt)}
                </div>
            </div>
        `;
    }

    renderAppointmentActions(apt) {
        let actions = '';
        
        if (apt.status === 'confirmed') {
            actions += `
                <button class="text-green-400 hover:text-green-300 text-sm transition-colors" onclick="dashboard.markCompleted(${apt.id})">
                    <i class="fas fa-check mr-1"></i>Complete
                </button>
                <button class="text-blue-400 hover:text-blue-300 text-sm transition-colors" onclick="dashboard.rescheduleAppointment(${apt.id})">
                    <i class="fas fa-clock mr-1"></i>Reschedule
                </button>
            `;
        }
        
        actions += `
            <button class="text-white/70 hover:text-white text-sm transition-colors" onclick="dashboard.editAppointment(${apt.id})">
                <i class="fas fa-edit mr-1"></i>Edit
            </button>
        `;
        
        return actions;
    }

    renderCalendarView() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        const calendarGrid = document.getElementById('calendarGrid');
        const calendarMonth = document.getElementById('calendarMonth');
        
        if (!calendarGrid || !calendarMonth) return;

        // Update month header
        calendarMonth.textContent = this.currentDate.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long' 
        });

        // Clear existing calendar days
        const existingDays = calendarGrid.querySelectorAll('.calendar-day');
        existingDays.forEach(day => day.remove());

        // Generate calendar days
        for (let i = 0; i < 42; i++) {
            const date = new Date(startDate);
            date.setDate(startDate.getDate() + i);
            
            const dayAppointments = this.appointments.filter(apt => 
                apt.appointment_date === this.formatDate(date)
            );
            
            const isCurrentMonth = date.getMonth() === month;
            const isToday = this.formatDate(date) === this.formatDate(new Date());

            const dayElement = document.createElement('div');
            dayElement.className = `calendar-day bg-white/5 rounded-lg p-2 border border-white/10 cursor-pointer ${
                isCurrentMonth ? '' : 'opacity-50'
            } ${isToday ? 'ring-2 ring-white/50' : ''}`;
            
            dayElement.innerHTML = `
                <div class="text-white text-sm mb-1">${date.getDate()}</div>
                <div class="space-y-1">
                    ${dayAppointments.slice(0, 3).map(apt => `
                        <div class="text-xs px-1 py-0.5 rounded text-white/90 ${
                            apt.is_urgent ? 'bg-red-500/50' : 'bg-blue-500/50'
                        }" title="${apt.pet_name} - ${apt.appointment_time}">
                            ${apt.appointment_time.substring(0, 5)} ${apt.pet_name}
                        </div>
                    `).join('')}
                    ${dayAppointments.length > 3 ? 
                        `<div class="text-xs text-white/70">+${dayAppointments.length - 3} more</div>` : 
                        ''
                    }
                </div>
            `;
            
            dayElement.addEventListener('click', () => {
                this.currentDate = date;
                this.switchView('day');
            });
            
            calendarGrid.appendChild(dayElement);
        }
    }

    renderHistoryView() {
        // This would load and render appointment history
        const historyList = document.getElementById('historyList');
        if (!historyList) return;

        // Placeholder for now - would load from server
        historyList.innerHTML = `
            <div class="text-center py-12 text-white/70">
                <i class="fas fa-history text-4xl mb-4"></i>
                <p class="text-lg mb-2">History feature coming soon</p>
                <p class="text-sm">All appointment changes will be logged here</p>
            </div>
        `;
    }

    renderUpcoming() {
        const upcomingList = document.getElementById('upcomingList');
        if (!upcomingList) return;

        const now = new Date();
        const upcoming = this.appointments
            .filter(apt => {
                const aptDateTime = new Date(apt.appointment_date + 'T' + apt.appointment_time);
                return aptDateTime > now && apt.status === 'confirmed';
            })
            .sort((a, b) => {
                const aTime = new Date(a.appointment_date + 'T' + a.appointment_time);
                const bTime = new Date(b.appointment_date + 'T' + b.appointment_time);
                return aTime - bTime;
            })
            .slice(0, 3);

        if (upcoming.length === 0) {
            upcomingList.innerHTML = `
                <div class="text-white/70 text-sm text-center py-4">
                    No upcoming appointments
                </div>
            `;
            return;
        }

        upcomingList.innerHTML = upcoming.map(apt => `
            <div class="bg-white/10 rounded-lg p-3 border border-white/20 cursor-pointer hover:bg-white/15 transition-colors" onclick="dashboard.viewAppointment(${apt.id})">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-white text-sm font-medium">${apt.pet_name}</span>
                    <span class="text-white/70 text-xs">${this.formatTime(apt.appointment_time)}</span>
                </div>
                <div class="text-white/70 text-xs">${this.capitalizeFirst(apt.appointment_type)} • ${apt.owner_name}</div>
                ${apt.is_urgent ? '<div class="text-red-400 text-xs mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>Urgent</div>' : ''}
            </div>
        `).join('');
    }

    updateStats() {
        const today = this.formatDate(this.currentDate);
        const todayAppointments = this.appointments.filter(apt => apt.appointment_date === today);
        
        const stats = {
            today: todayAppointments.length,
            pending: todayAppointments.filter(apt => apt.status === 'confirmed').length,
            completed: todayAppointments.filter(apt => apt.status === 'completed').length,
            urgent: todayAppointments.filter(apt => apt.is_urgent).length
        };

        Object.keys(stats).forEach(key => {
            const element = document.getElementById(`${key}Count`);
            if (element) {
                element.textContent = stats[key];
            }
        });
    }

    createModals() {
        const modalContainer = document.getElementById('modalContainer');
        if (!modalContainer) return;

        modalContainer.innerHTML = `
            <!-- New Appointment Modal -->
            <div id="appointmentModal" class="fixed inset-0 z-50 hidden">
                <div class="modal-backdrop absolute inset-0" onclick="dashboard.closeModals()"></div>
                <div class="relative flex items-center justify-center min-h-screen p-4">
                    <div class="glass-card rounded-2xl p-6 w-full max-w-md">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white">New Appointment</h3>
                            <button onclick="dashboard.closeModals()" class="text-white/70 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <form id="appointmentForm" class="space-y-4">
                            <div>
                                <label class="block text-white/90 text-sm font-medium mb-1">Pet Name</label>
                                <input type="text" id="petName" class="w-full bg-white/10 border border-white/20 text-white rounded-lg p-2 placeholder-white/50" placeholder="Enter pet name" required>
                            </div>
                            
                            <div>
                                <label class="block text-white/90 text-sm font-medium mb-1">Owner Name</label>
                                <input type="text" id="ownerName" class="w-full bg-white/10 border border-white/20 text-white rounded-lg p-2 placeholder-white/50" placeholder="Enter owner name" required>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-white/90 text-sm font-medium mb-1">Date</label>
                                    <input type="date" id="appointmentDate" class="w-full bg-white/10 border border-white/20 text-white rounded-lg p-2" required>
                                </div>
                                <div>
                                    <label class="block text-white/90 text-sm font-medium mb-1">Time</label>
                                    <input type="time" id="appointmentTime" class="w-full bg-white/10 border border-white/20 text-white rounded-lg p-2" required>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-white/90 text-sm font-medium mb-1">Type</label>
                                <select id="appointmentType" class="w-full bg-white/10 border border-white/20 text-white rounded-lg p-2" required>
                                    <option value="">Select type</option>
                                    <option value="checkup">Regular Checkup</option>
                                    <option value="vaccination">Vaccination</option>
                                    <option value="surgery">Surgery</option>
                                    <option value="emergency">Emergency</option>
                                    <option value="consultation">Consultation</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-white/90 text-sm font-medium mb-1">Duration (minutes)</label>
                                <input type="number" id="duration" class="w-full bg-white/10 border border-white/20 text-white rounded-lg p-2" placeholder="30" min="15" max="180" value="30">
                            </div>
                            
                            <div>
                                <label class="block text-white/90 text-sm font-medium mb-1">Notes</label>
                                <textarea id="notes" class="w-full bg-white/10 border border-white/20 text-white rounded-lg p-2 placeholder-white/50" rows="3" placeholder="Additional notes..."></textarea>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" id="isUrgent" class="rounded">
                                <label for="isUrgent" class="text-white/90 text-sm">Mark as urgent</label>
                            </div>
                            
                            <div class="flex space-x-3 pt-4">
                                <button type="button" onclick="dashboard.closeModals()" class="flex-1 bg-white/10 text-white py-2 px-4 rounded-lg hover:bg-white/20 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="flex-1 bg-white text-gray-900 py-2 px-4 rounded-lg hover:bg-white/90 transition-colors font-medium">
                                    Create Appointment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        // Bind form submission
        const form = document.getElementById('appointmentForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmission();
            });
        }
    }

    handleFormSubmission() {
        const formData = {
            pet_name: document.getElementById('petName').value,
            owner_name: document.getElementById('ownerName').value,
            appointment_date: document.getElementById('appointmentDate').value,
            appointment_time: document.getElementById('appointmentTime').value,
            appointment_type: document.getElementById('appointmentType').value,
            duration: document.getElementById('duration').value || 30,
            notes: document.getElementById('notes').value,
            is_urgent: document.getElementById('isUrgent').checked ? 1 : 0
        };

        this.createAppointment(formData);
    }

    openNewAppointmentModal() {
        const modal = document.getElementById('appointmentModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.getElementById('appointmentDate').value = this.formatDate(this.currentDate);
        }
    }

    closeModals() {
        const modals = document.querySelectorAll('[id$="Modal"]');
        modals.forEach(modal => modal.classList.add('hidden'));
        
        // Reset forms
        const forms = document.querySelectorAll('form');
        forms.forEach(form => form.reset());
    }

    async markCompleted(appointmentId) {
        if (!confirm('Mark this appointment as completed?')) return;

        try {
            const formData = new FormData();
            formData.append('action', 'update_appointment_status');
            formData.append('nonce', window.wpData.nonce);
            formData.append('appointment_id', appointmentId);
            formData.append('status', 'completed');

            const response = await fetch(window.wpData.ajaxUrl, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showNotification('Appointment marked as completed!', 'success');
                this.loadAppointments();
            } else {
                this.showNotification('Failed to update appointment', 'error');
            }
        } catch (error) {
            console.error('Error updating appointment:', error);
            this.showNotification('Network error', 'error');
        }
    }

    rescheduleAppointment(appointmentId) {
        // This would open a reschedule modal
        this.showNotification('Reschedule feature coming soon!', 'info');
    }

    editAppointment(appointmentId) {
        // This would open edit modal with pre-filled data
        this.showNotification('Edit feature coming soon!', 'info');
    }

    viewAppointment(appointmentId) {
        // This would show appointment details
        this.showNotification('View details feature coming soon!', 'info');
    }

    changeDate(direction) {
        const newDate = new Date(this.currentDate);
        newDate.setDate(newDate.getDate() + direction);
        this.currentDate = newDate;
        this.updateDateDisplay();
        this.loadAppointments();
    }

    changeMonth(direction) {
        const newDate = new Date(this.currentDate);
        newDate.setMonth(newDate.getMonth() + direction);
        this.currentDate = newDate;
        this.updateDateDisplay();
        this.loadAppointments();
    }

    updateDateDisplay() {
        const currentDateEl = document.getElementById('currentDate');
        if (currentDateEl) {
            currentDateEl.textContent = this.currentDate.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            dateFilter.value = this.formatDate(this.currentDate);
        }
    }

    applyFilters() {
        // Apply current filter values and re-render
        this.renderCurrentView();
    }

    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('open');
        }
    }

    handleKeyboardShortcuts(e) {
        if (e.key === 'Escape') {
            this.closeModals();
        }
        if (e.key === 'n' && e.ctrlKey) {
            e.preventDefault();
            this.openNewAppointmentModal();
        }
        if (e.key === 'ArrowLeft' && e.ctrlKey) {
            e.preventDefault();
            this.changeDate(-1);
        }
        if (e.key === 'ArrowRight' && e.ctrlKey) {
            e.preventDefault();
            this.changeDate(1);
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        } shadow-lg transform translate-x-full transition-transform duration-300`;
        
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas ${
                    type === 'success' ? 'fa-check' : 
                    type === 'error' ? 'fa-times' : 
                    'fa-info'
                }"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }

    // Utility functions
    formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes));
        return date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    getStatusColor(status) {
        const colors = {
            confirmed: 'bg-blue-400',
            completed: 'bg-green-400',
            cancelled: 'bg-red-400',
            rescheduled: 'bg-yellow-400'
        };
        return colors[status] || 'bg-gray-400';
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new AppointmentsDashboard();
});

// Make dashboard globally accessible for onclick handlers
window.dashboard = window.dashboard || {};