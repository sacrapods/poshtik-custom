(function() {
  // Select all file thumbnails and the modal elements
  var thumbs = document.querySelectorAll('.pet-file-thumb');
  var modal  = document.getElementById('file-modal');
  var body   = document.getElementById('file-modal-body');
  var close  = document.getElementById('file-modal-close');

  // If required elements do not exist, do nothing
  if (!thumbs.length || !modal || !body || !close) return;

  // On thumbnail click, show modal with embedded file
  thumbs.forEach(function(thumb) {
    thumb.addEventListener('click', function(e) {
      e.preventDefault();
      var url = this.getAttribute('data-url');
      var ext = url.split('.').pop().toLowerCase();
      if (ext === 'pdf') {
        body.innerHTML = '<embed src="' + url + '" type="application/pdf" width="100%" height="100%">';
      } else {
        body.innerHTML = '<img src="' + url + '" class="max-w-full max-h-full">';
      }
      modal.classList.remove('hidden');
    });
  });

  // Close modal when the close button is clicked
  close.addEventListener('click', function() {
    modal.classList.add('hidden');
    body.innerHTML = '';
  });

  // Close modal when clicking outside the modal content
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.classList.add('hidden');
      body.innerHTML = '';
    }
  });
})();
