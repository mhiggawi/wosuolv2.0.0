document.addEventListener('DOMContentLoaded', () => {
    // Tab Logic
    const tabs = document.querySelectorAll('.tab-button');
    const contents = document.querySelectorAll('.tab-content');
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab') || 'general-settings';

    function switchTab(tabId) {
        contents.forEach(content => content.classList.remove('active'));
        tabs.forEach(tab => tab.classList.remove('active'));
        const contentToShow = document.getElementById(tabId);
        const tabToActivate = document.querySelector(`[data-tab='${tabId}']`);
        if(contentToShow) contentToShow.classList.add('active');
        if(tabToActivate) tabToActivate.classList.add('active');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            const tabId = tab.dataset.tab;
            switchTab(tabId);
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            window.history.pushState({}, '', url);
        });
    });
    switchTab(activeTab);
});

// Accordion Logic
function toggleAccordion(header) {
    const content = header.nextElementSibling;
    const icon = header.querySelector('.toggle-icon');
    const isOpen = content.style.display === 'block';

    content.style.display = isOpen ? 'none' : 'block';
    icon.textContent = isOpen ? '▼' : '▲';
}

// Image Management Functions
function previewNewImage(input, type) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(type + '-image-preview').src = e.target.result;
            document.getElementById(type + '-image-preview-container').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function cancelImageSelection(type) {
    document.getElementById(type + '_image_upload').value = '';
    document.getElementById(type + '-image-preview-container').style.display = 'none';
}

function toggleImageUpload(checkbox, type) {
    const uploadSection = document.getElementById(type + '-image-upload-section');
    if (checkbox.checked) {
        uploadSection.style.display = 'none';
        cancelImageSelection(type);
    } else {
        uploadSection.style.display = 'block';
    }
}

// User Management Functions
function toggleEventSelect(role, containerId) {
    const container = document.getElementById(containerId);
    if(container) {
        container.style.display = (role === 'viewer' || role === 'checkin_user') ? 'block' : 'none';
    }
}

function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_password').value = '';
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_user_event_id').value = user.event_id || '';

    toggleEventSelect(user.role, 'edit_event_select_container');
    document.getElementById('editUserModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editUserModal').classList.remove('active');
}

// Close modal when clicking outside
const editUserModal = document.getElementById('editUserModal');
if (editUserModal) {
    editUserModal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
}
