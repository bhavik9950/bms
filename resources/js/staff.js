import { getCsrfToken } from './main.js';

const DEFAULT_IMG = "https://avatar.iran.liara.run/public"; //  placeholder

// Function to handle edit staff button click
window.editStaff = function(staff) {
    // Use the correct route name for staff edit
    const editUrl = `/dashboard/staff/edit/${staff.id}`;
    window.location.href = editUrl;
};

// Helper function to convert DD-MM-YYYY or DD-MM-YY to YYYY-MM-DD
function convertDateFormat(dateString) {
    if (!dateString) return '';
    
    // Check if it's already in YYYY-MM-DD format
    if (/^\d{4}-\d{2}-\d{2}$/.test(dateString)) {
        return dateString;
    }
    
    // Convert from DD-MM-YYYY or DD-MM-YY to YYYY-MM-DD
    const parts = dateString.split('-');
    if (parts.length === 3) {
        const day = parts[0];
        const month = parts[1];
        let year = parts[2];
        
        // Handle 2-digit years (YY -> 20YY)
        if (year.length === 2) {
            year = '20' + year;
        }
        
        // Validate date parts
        if (day && month && year && day.length === 2 && month.length === 2 && year.length === 4) {
            return `${year}-${month}-${day}`;
        }
    }
    
    return dateString; // Return as is if conversion fails
}

document.querySelectorAll(".staff-file-upload-input").forEach(input => {
    const extraOutline = input.closest(".extraOutline");
    const fileNameSpan = extraOutline.querySelector(".file-name");
    const instructions = extraOutline.querySelector(".upload-instructions");
    const preview = document.getElementById("profilePreview");
    // Add remove button logic (if you want the file name itself clickable)
    fileNameSpan.addEventListener("click", function () {
        input.value = "";
        preview.src = DEFAULT_IMG;
        fileNameSpan.textContent = "";
        fileNameSpan.classList.add("hidden");
        instructions.classList.remove("hidden");
    });

    input.addEventListener("change", function () {
        const file = this.files[0]; // Get the selected file
        // Update image preview
        if (file) {
            preview.src = URL.createObjectURL(file);
        }
        // Update file name display
        if (this.files.length > 0) {
            fileNameSpan.textContent = this.files[0].name;
            fileNameSpan.classList.remove("hidden");
            instructions.classList.add("hidden");
        } else {
            fileNameSpan.textContent = "";
            fileNameSpan.classList.add("hidden");
            instructions.classList.remove("hidden");
        }
    });
});

document.querySelectorAll(".staff-id-upload-input").forEach(input => {
    const extraOutline = input.closest(".extraOutline-id");
    const fileNameSpan = extraOutline.querySelector(".file-name-id");
    const instructions = extraOutline.querySelector(".upload-instructions-id");

    // Show file name and hide instructions on file select
    input.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            fileNameSpan.textContent = file.name;
            fileNameSpan.classList.remove("hidden");
            instructions.classList.add("hidden");
        } else {
            fileNameSpan.textContent = "";
            fileNameSpan.classList.add("hidden");
            instructions.classList.remove("hidden");
        }
    });

    // Allow clicking file name to clear and reselect
    fileNameSpan.addEventListener("click", function () {
        input.value = "";
        fileNameSpan.textContent = "";
        fileNameSpan.classList.add("hidden");
        instructions.classList.remove("hidden");
        input.click(); // Open file dialog again
    });
});

// Form Submission Handling
document.addEventListener('DOMContentLoaded', () => {
    const staffForm = document.getElementById('staff-info');
    console.log('Staff form found:', staffForm);
    if (!staffForm) return;
    
    let submitting = false;

    staffForm.addEventListener('submit', async function (e) {
        console.log('Form submit triggered', e.target.action);
        e.preventDefault();

        if (submitting) return;
        submitting = true;

        const submitBtn = staffForm.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn?.innerHTML;

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading loading-spinner"></span> Saving Staff...';
        }

        try {
            const formData = new FormData(staffForm);
            console.log("🚀 Staff Form Data before submit:");
for (let [key, value] of formData.entries()) {
    console.log(`${key}:`, value);
}

            // Convert date format before sending
            const joiningDate = formData.get('joining_date');
            if (joiningDate) {
                formData.set('joining_date', convertDateFormat(joiningDate));
            }
            
            // Rename fields to match controller
            if (formData.has('member_name')) {
                formData.set('full_name', formData.get('member_name'));
                formData.delete('member_name');
            }
            
            if (formData.has('member_email')) {
                formData.set('email', formData.get('member_email'));
                formData.delete('member_email');
            }
            
            if (formData.has('roles')) {
                formData.set('role_id', formData.get('roles'));
                formData.delete('roles');
            }
            
            if (formData.has('start_time')) {
                formData.set('shift_start_time', formData.get('start_time'));
                formData.delete('start_time');
            }
            
            if (formData.has('end_time')) {
                formData.set('shift_end_time', formData.get('end_time'));
                formData.delete('end_time');
            }
            
            if (formData.has('photo')) {
                formData.set('profile_picture', formData.get('photo'));
                formData.delete('photo');
            }
            
            const url = staffForm.action;
            const method = staffForm.method || 'POST';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: formData,
            });

            const data = await res.json().catch(() => ({}));
console.log("📥 Server Response:", data);

            if (!res.ok) {
                // Prioritize showing actual validation errors over generic message
                let errorMessage = '';

                if (data.errors && Object.keys(data.errors).length > 0) {
                    // Show specific validation errors
                    const errorList = Object.values(data.errors).flat();
                    errorMessage = errorList.join('\n');
                } else if (data.message) {
                    // Fallback to generic message
                    errorMessage = data.message;
                } else {
                    errorMessage = 'An unknown error occurred';
                }

                throw new Error(errorMessage);
            }

            // Handle success
            const successMessage = data.message || 'Staff member saved successfully!';

  Swal.fire('Success!', successMessage, 'success').then(() => {
                // Redirect to staff management list
                window.location.href = '/dashboard/staff';
            });

            // Only reset form if it's create (store), not update
            if (url.includes('/store')) {
                staffForm.reset();

                // Reset profile preview
                const profilePreview = document.getElementById('profilePreview');
                if (profilePreview) {
                    profilePreview.src = DEFAULT_IMG;
                }

                // Reset file name displays
                document.querySelectorAll('.file-name, .file-name-id').forEach(span => {
                    span.textContent = '';
                    span.classList.add('hidden');
                });
                document.querySelectorAll('.upload-instructions, .upload-instructions-id').forEach(div => {
                    div.classList.remove('hidden');
                });
            }

        } catch (error) {
            console.error('Error saving staff:', error);
            Swal.fire('Error!', error.message || 'Unexpected error occurred while saving staff.', 'error');
        } finally {
            submitting = false;
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }
    });
});


// Edit Staff member

// Toggle staff status (active/inactive)
window.toggleStaffStatus = async function(staffId, currentStatus) {
    const action = currentStatus ? 'deactivate' : 'activate';
    const actionText = currentStatus ? 'Deactivate' : 'Activate';

    // Use SweetAlert for confirmation instead of browser alert
    const result = await Swal.fire({
        title: `${actionText} Staff Member`,
        text: `Are you sure you want to ${action} this staff member?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: currentStatus ? '#dc2626' : '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${action}`,
        cancelButtonText: 'Cancel'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/dashboard/staff/${staffId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Update the status badge in the table
            const statusCell = document.querySelector(`#row-${staffId} .col-status`);
            if (statusCell) {
                statusCell.innerHTML = data.status ?
                    '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600">Active</span>' :
                    '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">Inactive</span>';
            }

            // Show success message
            if (window.Swal) {
                Swal.fire('Success!', data.message, 'success');
            } else {
                alert(data.message);
            }

            // Reload page to update dropdown menu
            setTimeout(() => window.location.reload(), 1000);
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error toggling status:', error);
        if (window.Swal) {
            Swal.fire('Error!', error.message || 'Failed to update staff status', 'error');
        } else {
            alert(error.message || 'Failed to update staff status');
        }
    }
};

// Toggle password visibility for password field
document.getElementById('togglePassword')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const icon = this;
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
});

// Toggle password visibility for password confirmation field
document.getElementById('togglePasswordConfirm')?.addEventListener('click', function() {
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const icon = this;
    if (passwordConfirmInput.type === 'password') {
        passwordConfirmInput.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordConfirmInput.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
});

// Send credentials via email
window.sendCredentials = async function(staffId) {
    const result = await Swal.fire({
        title: 'Send Login Credentials',
        text: 'Send login credentials to this staff member via email?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Send Email',
        cancelButtonText: 'Cancel'
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch(`/dashboard/staff/${staffId}/send-credentials`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            if (window.Swal) {
                Swal.fire('Success!', data.message, 'success');
            } else {
                alert(data.message);
            }
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Error sending credentials:', error);
        if (window.Swal) {
            Swal.fire('Error!', error.message || 'Failed to send credentials', 'error');
        } else {
            alert(error.message || 'Failed to send credentials');
        }
    }
};
