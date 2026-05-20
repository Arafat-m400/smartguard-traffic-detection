
  document.getElementById('roleSelect').addEventListener('change', function () {
    const licenseField = document.getElementById('licenseField');
    if (this.value === 'Driver') {
      licenseField.style.display = 'block';
    } else {
      licenseField.style.display = 'none';
    }
  });
document.addEventListener('DOMContentLoaded', function () {
  // Role-based license field on sign-up
  const roleSelect = document.getElementById('roleSelect');
  const licenseField = document.getElementById('licenseField');

  if (roleSelect) {
    roleSelect.addEventListener('change', function () {
      if (this.value === 'Driver') {
        licenseField.style.display = 'block';
      } else {
        licenseField.style.display = 'none';
      }
    });
  }

  // Login method toggle
  const toggleUser = document.getElementById('toggleUserLogin');
  const toggleLicense = document.getElementById('toggleLicenseLogin');
  const userLoginForm = document.getElementById('userLoginForm');
  const licenseLoginForm = document.getElementById('licenseLoginForm');

  if (toggleUser && toggleLicense && userLoginForm && licenseLoginForm) {
    toggleUser.addEventListener('click', function () {
      userLoginForm.style.display = 'block';
      licenseLoginForm.style.display = 'none';
      toggleUser.classList.add('active');
      toggleLicense.classList.remove('active');
    });

    toggleLicense.addEventListener('click', function () {
      userLoginForm.style.display = 'none';
      licenseLoginForm.style.display = 'block';
      toggleLicense.classList.add('active');
      toggleUser.classList.remove('active');
    });
  }
});

