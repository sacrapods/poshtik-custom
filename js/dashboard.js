document.addEventListener('DOMContentLoaded', function() {
  // Cache DOM elements
  var dashboard = document.getElementById('vet-dashboard');
  var toggleBtn = document.getElementById('sidebar-toggle');
  var tabButtons = document.querySelectorAll('.tab-button');
  var panels = {
    overview: document.getElementById('pet-detail-panel'),
    visits: document.getElementById('visits-panel'),
    files: document.getElementById('files-panel'),
    comments: document.getElementById('comments-panel'),
    pet_list: document.getElementById('pet-list-panel'),
    edit_profile: document.getElementById('edit-profile-panel')
  };

  if (!dashboard) return;

  // Enhanced sidebar toggle with animation
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function() {
      dashboard.classList.toggle('collapsed');
      
      // Add rotation animation to toggle button
      toggleBtn.style.transform = dashboard.classList.contains('collapsed') 
        ? 'rotate(180deg)' 
        : 'rotate(0deg)';
    });
  }

  // Enhanced navigation handling
  var petsToggle = document.getElementById('pets-toggle') ||
    Array.from(document.querySelectorAll('button')).find(function(btn) {
      return btn.textContent.trim() === 'Pets';
    });

  if (petsToggle) {
    petsToggle.addEventListener('click', function() {
      loadPanel(null, 'pet_list');
      addLoadingState(this);
    });
  }

  var homeToggle = document.getElementById('home-toggle');
  var tabsNav = document.querySelector('.dashboard-tabs');

  function showPanel(panelKey) {
    hideAll();
    var target = panels[panelKey];
    if (target) {
      target.classList.remove('hidden');
      // Add entrance animation
      target.style.animation = 'fadeInUp 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    }
    
    if (tabsNav) {
      if (panelKey === 'pet_list') {
        tabsNav.classList.add('hidden');
      } else {
        tabsNav.classList.remove('hidden');
      }
    }
  }

  if (homeToggle) {
    homeToggle.addEventListener('click', function() {
      showPanel('overview');
      var btn = document.getElementById('tab-overview');
      if (btn) {
        btn.classList.add('active');
        addRippleEffect(btn, event);
      }
      addLoadingState(this);
    });
  }

  // Initialize to Home view with welcome screen
  showWelcomeScreen();

  // Helper to hide all panels and deactivate all tabs
  function hideAll() {
    Object.values(panels).forEach(function(panel) {
      if (panel) panel.classList.add('hidden');
    });
    tabButtons.forEach(function(btn) {
      btn.classList.remove('active');
    });
  }

  // Enhanced panel loading with better UX
  function loadPanel(petId, panel) {
    hideAll();
    
    var target = panels[panel];
    if (!target) return;

    // Show loading state
    showLoadingState(target);
    
    var data = new URLSearchParams();
    data.append('action', 'poshtik_load_pet_panel');
    data.append('pet_id', petId);
    data.append('panel', panel);

    fetch(ajaxurl, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: data.toString()
    })
    .then(function(res) { 
      if (!res.ok) throw new Error('Network response was not ok');
      return res.text(); 
    })
    .then(function(html) {
      // Process the HTML based on panel type
      if (panel === 'pet_list') {
        var tmpDiv = document.createElement('div');
        tmpDiv.innerHTML = html;
        html = tmpDiv.firstElementChild.innerHTML;
      }
      
      // Hide loading and show content with animation
      hideLoadingState(target);
      target.innerHTML = html;
      target.classList.remove('hidden');
      
      // Add entrance animation
      target.style.animation = 'fadeInUp 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
      
      // Activate corresponding tab
      var tab = document.getElementById('tab-' + panel);
      if (tab) {
        tab.classList.add('active');
      }
      
      // Initialize any new interactive elements
      initializeDynamicContent(target);
    })
    .catch(function(error) {
      console.error('Error loading panel:', error);
      hideLoadingState(target);
      target.innerHTML = '<div class="error-message">Failed to load content. Please try again.</div>';
      target.classList.remove('hidden');
    });
  }

  // Enhanced tab button handling with animations
  tabButtons.forEach(function(btn) {
    btn.addEventListener('click', function(event) {
      var activeLink = document.querySelector('.dashboard-pet-link.active');
      if (!activeLink) return;
      
      var petId = activeLink.getAttribute('data-pet-id');
      var panel = this.id.replace('tab-', '');
      
      // Add ripple effect
      addRippleEffect(this, event);
      
      // Add loading state to button
      addLoadingState(this);
      
      loadPanel(petId, panel);
    });
  });

  // Enhanced edit profile button handling
  document.body.addEventListener('click', function(e) {
    if (e.target.matches('.edit-profile-btn, .dashboard-edit-profile')) {
      var petId = e.target.getAttribute('data-pet-id');
      addRippleEffect(e.target, e);
      addLoadingState(e.target);
      loadPanel(petId, 'edit_profile');
    }
  });

  // Enhanced pet name click handling
  document.body.addEventListener('click', function(e) {
    if (e.target.matches('#pet-list-panel .pet-name') || e.target.closest('.pet-item')) {
      var li = e.target.closest('li');
      if (!li) return;
      
      var button = li.querySelector('button.edit-profile-btn, button.dashboard-edit-profile');
      if (!button) return;
      
      var petId = button.getAttribute('data-pet-id');
      
      // Add selection animation
      li.style.transform = 'scale(0.98)';
      setTimeout(function() {
        li.style.transform = 'scale(1)';
      }, 150);
      
      loadPanel(petId, 'overview');
    }
  });

  // Loading state functions
  function showLoadingState(element) {
    element.innerHTML = `
      <div class="loading-container" style="display: flex; justify-content: center; align-items: center; min-height: 200px;">
        <div class="loading-spinner" style="
          width: 40px; 
          height: 40px; 
          border: 3px solid rgba(102, 126, 234, 0.1); 
          border-top: 3px solid #667eea; 
          border-radius: 50%; 
          animation: spin 1s linear infinite;
        "></div>
      </div>
    `;
    element.classList.remove('hidden');
  }

  function hideLoadingState(element) {
    var loadingContainer = element.querySelector('.loading-container');
    if (loadingContainer) {
      loadingContainer.remove();
    }
  }

  function addLoadingState(button) {
    var originalText = button.textContent;
    button.disabled = true;
    button.style.opacity = '0.7';
    
    setTimeout(function() {
      button.disabled = false;
      button.style.opacity = '1';
    }, 500);
  }

  // Ripple effect for buttons
  function addRippleEffect(element, event) {
    var ripple = document.createElement('span');
    var rect = element.getBoundingClientRect();
    var size = Math.max(rect.width, rect.height);
    var x = event.clientX - rect.left - size / 2;
    var y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
      position: absolute;
      width: ${size}px;
      height: ${size}px;
      left: ${x}px;
      top: ${y}px;
      background: rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      pointer-events: none;
      animation: ripple 0.6s linear;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(function() {
      ripple.remove();
    }, 600);
  }

  // Welcome screen function
  function showWelcomeScreen() {
    hideAll();
    var welcomePanel = panels.overview;
    if (welcomePanel) {
      welcomePanel.innerHTML = `
        <div class="welcome-screen" style="text-align: center; padding: 4rem 2rem;">
          <div class="welcome-icon" style="
            width: 120px; 
            height: 120px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 2rem; 
            font-size: 3rem; 
            color: white; 
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
          ">
            🐾
          </div>
          <h2 style="font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">
            Welcome to VetCare Dashboard
          </h2>
          <p style="font-size: 1.125rem; color: var(--text-secondary); margin-bottom: 2rem;">
            Manage your patients with care and efficiency
          </p>
          <div class="stats-grid" style="
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 1.5rem; 
            margin-top: 2rem;
          ">
            <div class="stat-card" style="
              background: rgba(255, 255, 255, 0.8); 
              border-radius: 16px; 
              padding: 1.5rem; 
              text-align: center; 
              border: 1px solid rgba(229, 231, 235, 0.5); 
              transition: all 0.3s ease; 
              backdrop-filter: blur(10px);
            ">
              <div class="stat-icon" style="
                width: 60px; 
                height: 60px; 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                border-radius: 12px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                color: white; 
                font-size: 1.5rem; 
                margin: 0 auto 1rem;
              ">🐾</div>
              <div class="stat-number" style="
                font-size: 2rem; 
                font-weight: 700; 
                color: var(--text-primary); 
                margin-bottom: 0.5rem;
              ">247</div>
              <div class="stat-label" style="color: var(--text-secondary); font-weight: 500;">Total Pets</div>
            </div>
            <div class="stat-card" style="
              background: rgba(255, 255, 255, 0.8); 
              border-radius: 16px; 
              padding: 1.5rem; 
              text-align: center; 
              border: 1px solid rgba(229, 231, 235, 0.5); 
              transition: all 0.3s ease; 
              backdrop-filter: blur(10px);
            ">
              <div class="stat-icon" style="
                width: 60px; 
                height: 60px; 
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); 
                border-radius: 12px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                color: white; 
                font-size: 1.5rem; 
                margin: 0 auto 1rem;
              ">📅</div>
              <div class="stat-number" style="
                font-size: 2rem; 
                font-weight: 700; 
                color: var(--text-primary); 
                margin-bottom: 0.5rem;
              ">18</div>
              <div class="stat-label" style="color: var(--text-secondary); font-weight: 500;">Today's Appointments</div>
            </div>
            <div class="stat-card" style="
              background: rgba(255, 255, 255, 0.8); 
              border-radius: 16px; 
              padding: 1.5rem; 
              text-align: center; 
              border: 1px solid rgba(229, 231, 235, 0.5); 
              transition: all 0.3s ease; 
              backdrop-filter: blur(10px);
            ">
              <div class="stat-icon" style="
                width: 60px; 
                height: 60px; 
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); 
                border-radius: 12px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                color: white; 
                font-size: 1.5rem; 
                margin: 0 auto 1rem;
              ">🩺</div>
              <div class="stat-number" style="
                font-size: 2rem; 
                font-weight: 700; 
                color: var(--text-primary); 
                margin-bottom: 0.5rem;
              ">156</div>
              <div class="stat-label" style="color: var(--text-secondary); font-weight: 500;">Visits This Month</div>
            </div>
            <div class="stat-card" style="
              background: rgba(255, 255, 255, 0.8); 
              border-radius: 16px; 
              padding: 1.5rem; 
              text-align: center; 
              border: 1px solid rgba(229, 231, 235, 0.5); 
              transition: all 0.3s ease; 
              backdrop-filter: blur(10px);
            ">
              <div class="stat-icon" style="
                width: 60px; 
                height: 60px; 
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); 
                border-radius: 12px; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                color: white; 
                font-size: 1.5rem; 
                margin: 0 auto 1rem;
              ">⚠️</div>
              <div class="stat-number" style="
                font-size: 2rem; 
                font-weight: 700; 
                color: var(--text-primary); 
                margin-bottom: 0.5rem;
              ">3</div>
              <div class="stat-label" style="color: var(--text-secondary); font-weight: 500;">Urgent Cases</div>
            </div>
          </div>
        </div>
      `;
      welcomePanel.classList.remove('hidden');
      welcomePanel.style.animation = 'fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
      
      // Add hover effects to stat cards
      var statCards = welcomePanel.querySelectorAll('.stat-card');
      statCards.forEach(function(card) {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-4px)';
          this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.1)';
        });
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0)';
          this.style.boxShadow = 'none';
        });
      });
    }
  }

  // Initialize dynamic content function
  function initializeDynamicContent(container) {
    // Initialize file modal functionality
    var fileThumbLinks = container.querySelectorAll('.pet-file-thumb');
    fileThumbLinks.forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        showFileModal(this.getAttribute('data-url'));
      });
    });

    // Initialize form enhancements
    var forms = container.querySelectorAll('form');
    forms.forEach(function(form) {
      enhanceForm(form);
    });

    // Initialize table enhancements
    var tables = container.querySelectorAll('table');
    tables.forEach(function(table) {
      enhanceTable(table);
    });
  }

  // File modal functionality
  function showFileModal(url) {
    var modal = document.getElementById('file-modal');
    if (!modal) {
      // Create modal if it doesn't exist
      modal = document.createElement('div');
      modal.id = 'file-modal';
      modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden';
      modal.innerHTML = `
        <div class="modal-content bg-white p-4 rounded-lg shadow-lg max-w-screen-lg max-h-screen overflow-auto relative" style="
          background: rgba(255, 255, 255, 0.95);
          backdrop-filter: blur(20px);
          border: 1px solid rgba(255, 255, 255, 0.2);
          border-radius: 16px;
          box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        ">
          <span class="modal-close absolute top-4 right-4 text-2xl cursor-pointer text-gray-500 hover:text-gray-700" style="
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            transition: all 0.2s ease;
          ">&times;</span>
          <div id="file-modal-body"></div>
        </div>
      `;
      document.body.appendChild(modal);
    }

    var modalBody = modal.querySelector('#file-modal-body');
    var closeBtn = modal.querySelector('.modal-close');

    modalBody.innerHTML = `<img src="${url}" alt="File preview" style="max-width: 100%; height: auto; border-radius: 8px;">`;
    modal.classList.remove('hidden');
    modal.style.animation = 'fadeIn 0.3s ease';

    closeBtn.addEventListener('click', function() {
      modal.classList.add('hidden');
    });

    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  }

  // Form enhancement function
  function enhanceForm(form) {
    var inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(function(input) {
      // Add floating label effect
      input.addEventListener('focus', function() {
        this.style.borderColor = '#667eea';
        this.style.boxShadow = '0 0 0 3px rgba(102, 126, 234, 0.1)';
      });

      input.addEventListener('blur', function() {
        this.style.borderColor = 'rgba(255, 255, 255, 0.3)';
        this.style.boxShadow = 'none';
      });
    });

    // Add form submission handling
    form.addEventListener('submit', function(e) {
      var submitBtn = form.querySelector('button[type="submit"]');
      if (submitBtn) {
        addLoadingState(submitBtn);
      }
    });
  }

  // Table enhancement function
  function enhanceTable(table) {
    var rows = table.querySelectorAll('tbody tr');
    rows.forEach(function(row) {
      row.addEventListener('mouseenter', function() {
        this.style.backgroundColor = 'rgba(102, 126, 234, 0.05)';
        this.style.transform = 'translateX(2px)';
      });

      row.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '';
        this.style.transform = 'translateX(0)';
      });
    });
  }

  // Add CSS animations
  var style = document.createElement('style');
  style.textContent = `
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    @keyframes ripple {
      0% {
        transform: scale(0);
        opacity: 1;
      }
      100% {
        transform: scale(4);
        opacity: 0;
      }
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .error-message {
      background: rgba(239, 68, 68, 0.1);
      border: 1px solid rgba(239, 68, 68, 0.2);
      color: #991b1b;
      padding: 1rem;
      border-radius: 8px;
      text-align: center;
      backdrop-filter: blur(10px);
    }
    
    #sidebar-toggle {
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
  `;
  document.head.appendChild(style);

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    // ESC key to close modals
    if (e.key === 'Escape') {
      var modal = document.getElementById('file-modal');
      if (modal && !modal.classList.contains('hidden')) {
        modal.classList.add('hidden');
      }
    }
    
    // Ctrl/Cmd + number keys for quick tab switching
    if ((e.ctrlKey || e.metaKey) && e.key >= '1' && e.key <= '4') {
      e.preventDefault();
      var tabIndex = parseInt(e.key) - 1;
      var tabs = ['tab-overview', 'tab-visits', 'tab-files', 'tab-comments'];
      var targetTab = document.getElementById(tabs[tabIndex]);
      if (targetTab) {
        targetTab.click();
      }
    }
  });

  // Touch/swipe support for mobile
  var touchStartX = 0;
  var touchEndX = 0;

  document.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
  });

  document.addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  });

  function handleSwipe() {
    var swipeThreshold = 50;
    var diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > swipeThreshold) {
      if (diff > 0) {
        // Swipe left - collapse sidebar
        if (!dashboard.classList.contains('collapsed')) {
          toggleBtn.click();
        }
      } else {
        // Swipe right - expand sidebar
        if (dashboard.classList.contains('collapsed')) {
          toggleBtn.click();
        }
      }
    }
  }

  // Performance monitoring
  var performanceObserver = new PerformanceObserver(function(list) {
    list.getEntries().forEach(function(entry) {
      if (entry.entryType === 'navigation') {
        console.log('Dashboard load time:', entry.loadEventEnd - entry.loadEventStart, 'ms');
      }
    });
  });
  
  if (typeof PerformanceObserver !== 'undefined') {
    performanceObserver.observe({ entryTypes: ['navigation'] });
  }

  console.log('Enhanced Vet Dashboard initialized successfully! 🐾');
});