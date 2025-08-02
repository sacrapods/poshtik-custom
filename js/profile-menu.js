(function() {
  var toggle = document.getElementById('profile-toggle');
  var menu   = document.getElementById('profile-menu');
  if (!toggle || !menu) return;
  toggle.addEventListener('click', function(e) {
    e.stopPropagation();
    menu.classList.toggle('hidden');
  });
  document.addEventListener('click', function(e) {
    if (!menu.contains(e.target) && !toggle.contains(e.target)) {
      menu.classList.add('hidden');
    }
  });
})();
