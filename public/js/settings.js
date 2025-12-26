// public/js/settings.js
document.addEventListener('DOMContentLoaded', function () {
  // Owner avatar
  const btnOwner = document.getElementById('btnAvatarPickOwner');
  const inputOwner = document.getElementById('avatarInputOwner');
  const previewOwner = document.getElementById('avatarPreviewOwner');

  if (btnOwner && inputOwner && previewOwner) { 
    btnOwner.addEventListener('click', (e) => {
      e.preventDefault();
      inputOwner.click();
    });

    inputOwner.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;
      if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        e.target.value = '';
        return;
      }
      previewOwner.src = URL.createObjectURL(file);
    });
  }

  // Store avatar
  const btnStore = document.getElementById('btnAvatarPickStore');
  const inputStore = document.getElementById('avatarInputStore');
  const previewStore = document.getElementById('avatarPreviewStore');

  if (btnStore && inputStore && previewStore) {
    btnStore.addEventListener('click', (e) => {
      e.preventDefault();
      inputStore.click();
    });

    inputStore.addEventListener('change', function (e) {
      const file = e.target.files[0];
      if (!file) return;
      if (!file.type.startsWith('image/')) {
        alert('Please select an image file.');
        e.target.value = '';
        return;
      }
      previewStore.src = URL.createObjectURL(file);
    });
  }

  // Disable submit button for each form when submitting (prevent double submit)
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function () {
      const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      if (!submitBtn) return;
      submitBtn.disabled = true;
      // add spinner text safely
      submitBtn.dataset.origHtml = submitBtn.innerHTML;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
    });
  });

  // Modal delete store confirmation enabling
  const confirmInput = document.getElementById('confirmDelete');
  const btnDelete = document.getElementById('btnDeleteStore');
  if (confirmInput && btnDelete) {
    confirmInput.addEventListener('input', function () {
      btnDelete.disabled = (confirmInput.value.trim() !== 'DELETE');
    });
  }

  // Optional: show toast on success
  const toastEl = document.getElementById('saveToast');
  if (toastEl) {
    const toast = new bootstrap.Toast(toastEl);
    toast.show();
  }
});
