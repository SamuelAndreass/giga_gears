document.addEventListener('DOMContentLoaded', function() {
    setupSidebar();
    setupSearch();
    setupModalReset();
    setupFormValidation();
    syncEndDateMin();
});

function setupSidebar() {
    const btnToggle = document.getElementById('btnToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (btnToggle) {
        btnToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            adminSidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
}

function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#workshopTable tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
}

function setupModalReset() {
    const modal = document.getElementById('addEventModal');
    if (modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            resetForm();
        });
    }
}

function resetForm() {
    const form = document.getElementById('workshopForm');
    if (form) {
        form.reset();
        document.getElementById('methodOverride').value = 'POST';
        document.getElementById('modalTitle').textContent = 'Add New Workshop';
        document.getElementById('submitBtn').textContent = 'Save Workshop';
        form.action = '/admin/workshops';
    }
}

function setupFormValidation() {
    const form = document.getElementById('workshopForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const startDate = document.getElementById('start_date').value;
        const startTime = document.getElementById('start_time').value;
        const endDate   = document.getElementById('end_date').value;
        const endTime   = document.getElementById('end_time').value;
        const capacity  = document.getElementById('capacity').value;

        if (!startDate || !startTime || !endDate || !endTime) return;

        const start = new Date(`${startDate}T${startTime}`);
        const end   = new Date(`${endDate}T${endTime}`);

        if (end <= start) {
            e.preventDefault();
            alert('❌ End date & time harus setelah start date & time');
            return;
        }

        if (parseInt(capacity) < 1) {
            e.preventDefault();
            alert('❌ Capacity minimal 1');
            return;
        }
    });
}

function syncEndDateMin() {
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    if (!startDate || !endDate) return;

    startDate.addEventListener('change', function () {
        endDate.min = this.value;
    });
}


function editWorkshop(workshopId) {
    // Fetch dengan credentials included
    fetch(`/admin/workshops/${workshopId}/json`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        // Set form values
        document.getElementById('title').value = data.title || '';
        document.getElementById('description').value = data.description || '';
        document.getElementById('start_date').value = data.start_date || '';
        document.getElementById('start_time').value = data.start_time || '';
        document.getElementById('end_date').value = data.end_date || '';
        document.getElementById('end_time').value = data.end_time || '';
        document.getElementById('instructor').value = data.instructor || '';
        document.getElementById('capacity').value = data.capacity || '';
        document.getElementById('location').value = data.location || '';
        document.getElementById('status').value = data.status || '';
        document.getElementById('requirements').value = data.requirements || '';
        document.getElementById('image_url').value = data.image_url || '';
        
        // Change form to edit mode
        const form = document.getElementById('workshopForm');
        form.action = `/admin/workshops/${data.id}`;
        document.getElementById('methodOverride').value = 'PUT';
        document.getElementById('modalTitle').textContent = 'Edit Workshop';
        document.getElementById('submitBtn').textContent = 'Update Workshop';
        
        // Open modal
        const modal = new bootstrap.Modal(document.getElementById('addEventModal'));
        modal.show();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal memuat data: ' + error.message);
    });
}
