/**
 * Skill Owners - Main JavaScript
 * Core functionality and animations
 */

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function () {
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    const navbarActions = document.querySelector('.navbar-actions');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            navbarMenu?.classList.toggle('mobile-active');
            navbarActions?.classList.toggle('mobile-active');
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
            showFieldError(input, 'This field is required');
        } else {
            input.classList.remove('error');
            hideFieldError(input);
        }

        // Email validation
        if (input.type === 'email' && input.value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                isValid = false;
                input.classList.add('error');
                showFieldError(input, 'Please enter a valid email address');
            }
        }

        // Password validation
        if (input.type === 'password' && input.name === 'password' && input.value) {
            if (input.value.length < 8) {
                isValid = false;
                input.classList.add('error');
                showFieldError(input, 'Password must be at least 8 characters');
            }
        }

        // Password confirmation
        if (input.name === 'confirm_password' && input.value) {
            const password = form.querySelector('[name="password"]');
            if (password && input.value !== password.value) {
                isValid = false;
                input.classList.add('error');
                showFieldError(input, 'Passwords do not match');
            }
        }
    });

    return isValid;
}

function showFieldError(input, message) {
    let errorElement = input.parentElement.querySelector('.field-error');
    if (!errorElement) {
        errorElement = document.createElement('span');
        errorElement.className = 'field-error text-danger';
        errorElement.style.fontSize = '0.75rem';
        errorElement.style.marginTop = '0.25rem';
        errorElement.style.display = 'block';
        errorElement.style.color = 'var(--destructive)';
        input.parentElement.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

function hideFieldError(input) {
    const errorElement = input.parentElement.querySelector('.field-error');
    if (errorElement) {
        errorElement.remove();
    }
}

// Image Preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Character Counter
function updateCharCount(textarea, counterId, maxLength) {
    const counter = document.getElementById(counterId);
    if (!counter) return;

    const currentLength = textarea.value.length;
    counter.textContent = `${currentLength} / ${maxLength}`;

    if (currentLength > maxLength) {
        counter.style.color = 'var(--destructive)';
    } else {
        counter.style.color = 'var(--muted-foreground)';
    }
}

// Confirm Dialog
function confirmAction(message) {
    return confirm(message);
}

// AJAX Helper
async function ajaxRequest(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
    };

    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('AJAX Error:', error);
        return { success: false, message: 'Request failed' };
    }
}

// Chat Functions
let chatPollInterval = null;
let chatIsFetching = false;
let chatIsSending = false;
let chatLastSignature = null;

function initChat(userId, receiverId) {
    if (!receiverId || receiverId <= 0 || userId === receiverId) {
        console.error('Invalid chat participants');
        return;
    }

    loadMessages(userId, receiverId);
    startChatPolling(userId, receiverId);

    // Stop polling when user leaves the page
    window.addEventListener('beforeunload', stopChatPolling);
}

function loadMessages(userId, receiverId) {
    if (chatIsFetching) return;
    chatIsFetching = true;

    // Track whether user is near bottom; don't force-scroll if reading old messages
    const container = document.getElementById('chatMessages');
    const wasNearBottom = container ? isNearBottom(container, 80) : true;

    // Cache-buster avoids any intermediate/proxy caching
    const url = `chat_api.php?action=get_messages&receiver_id=${receiverId}&_=${Date.now()}`;

    ajaxRequest(url)
        .then(data => {
            if (data.success) {
                const signature = buildMessagesSignature(data.messages);
                if (signature !== chatLastSignature) {
                    displayMessages(data.messages);
                    chatLastSignature = signature;

                    if (wasNearBottom) {
                        scrollToBottom('chatMessages');
                    }
                }
            } else {
                console.error('Failed to load messages:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading messages:', error);
        })
        .finally(() => {
            chatIsFetching = false;
        });
}

function sendMessage(userId, receiverId, message) {
    if (chatIsSending) return;
    if (!message || !message.trim()) return;

    const input = document.getElementById('chatInput');
    const button = document.querySelector('.chat-input-area button');

    chatIsSending = true;
    if (button) button.disabled = true;

    ajaxRequest('chat_api.php', 'POST', {
        action: 'send_message',
        receiver_id: receiverId,
        message: message
    }).then(data => {
        if (data.success) {
            if (input) input.value = '';
            // Reload immediately after sending
            loadMessages(userId, receiverId);
        } else {
            console.error('Failed to send message:', data.message);
            alert('Failed to send message: ' + data.message);
        }
    }).catch(error => {
        console.error('Error sending message:', error);
        alert('Error sending message. Check console for details.');
    }).finally(() => {
        chatIsSending = false;
        if (button) button.disabled = false;
    });
}

function displayMessages(messages) {
    const container = document.getElementById('chatMessages');
    if (!container) return;

    container.innerHTML = '';
    messages.forEach(msg => {
        const messageDiv = document.createElement('div');
        messageDiv.className = msg.is_sender ? 'message message-sent' : 'message message-received';

        let roleTag = '';
        if (msg.sender_role) {
            const roleClass = 'role-' + msg.sender_role.toLowerCase();
            roleTag = `<span class="role-tag ${roleClass}">${escapeHtml(msg.sender_role)}</span>`;
        }

        messageDiv.innerHTML = `
            <div class="message-sender">
                ${escapeHtml(msg.sender_name || 'Unknown')}
                ${roleTag}
            </div>
            <div class="message-content">${escapeHtml(msg.message)}</div>
            <div class="message-time">${formatTime(msg.created_at)}</div>
        `;
        container.appendChild(messageDiv);
    });
}

function startChatPolling(userId, receiverId) {
    stopChatPolling();
    chatPollInterval = setInterval(() => {
        loadMessages(userId, receiverId);
    }, 3000); // Poll every 3 seconds
}

function stopChatPolling() {
    if (chatPollInterval) {
        clearInterval(chatPollInterval);
        chatPollInterval = null;
    }
}

function scrollToBottom(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollTop = element.scrollHeight;
    }
}

function isNearBottom(element, thresholdPx = 80) {
    return (element.scrollHeight - element.scrollTop - element.clientHeight) <= thresholdPx;
}

function buildMessagesSignature(messages) {
    // Simple, fast signature to detect changes without re-rendering
    if (!Array.isArray(messages) || messages.length === 0) return '0';
    const last = messages[messages.length - 1];
    return `${messages.length}|${last.created_at}|${last.message}`;
}
// Utility Functions
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return Math.floor(diff / 60000) + ' min ago';
    if (diff < 86400000) return Math.floor(diff / 3600000) + ' hours ago';

    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2);
}

// Search Functionality
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    if (!searchInput || !searchResults) return;

    let searchTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });
}

function performSearch(query) {
    ajaxRequest(`search_api.php?q=${encodeURIComponent(query)}`)
        .then(data => {
            if (data.success) {
                displaySearchResults(data.results);
            }
        });
}

function displaySearchResults(results) {
    const container = document.getElementById('searchResults');
    if (!container) return;

    if (results.length === 0) {
        container.innerHTML = '<div class="no-results">No results found</div>';
        container.style.display = 'block';
        return;
    }

    container.innerHTML = results.map(result => `
        <a href="gig.php?id=${result.id}" class="search-result-item">
            <div class="result-title">${escapeHtml(result.title)}</div>
            <div class="result-category">${escapeHtml(result.category)}</div>
        </a>
    `).join('');
    container.style.display = 'block';
}

// File Upload Validation
function validateFileUpload(input, allowedTypes, maxSize) {
    if (!input.files || !input.files[0]) return false;

    const file = input.files[0];
    const fileType = file.name.split('.').pop().toLowerCase();
    const fileSize = file.size;

    if (!allowedTypes.includes(fileType)) {
        alert(`Invalid file type. Allowed types: ${allowedTypes.join(', ')}`);
        input.value = '';
        return false;
    }

    if (fileSize > maxSize) {
        alert(`File too large. Maximum size: ${(maxSize / 1024 / 1024).toFixed(2)} MB`);
        input.value = '';
        return false;
    }

    return true;
}

// Loading Overlay
function showLoading(message = 'Loading...') {
    let overlay = document.getElementById('loadingOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        `;
        overlay.innerHTML = `<div style="background: white; padding: 2rem; border-radius: 0.75rem; text-align: center;">
            <div class="spinner"></div>
            <div style="margin-top: 1rem;">${message}</div>
        </div>`;
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Tab Switching
function switchTab(tabName, element) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.style.display = 'none');

    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Show selected tab content
    const selectedContent = document.getElementById(tabName);
    if (selectedContent) {
        selectedContent.style.display = 'block';
    }

    // Add active class to selected tab
    if (element) {
        element.classList.add('active');
    }
}

// Export functions for global use
window.validateForm = validateForm;
window.previewImage = previewImage;
window.updateCharCount = updateCharCount;
window.confirmAction = confirmAction;
window.ajaxRequest = ajaxRequest;
window.initChat = initChat;
window.sendMessage = sendMessage;
window.stopChatPolling = stopChatPolling;
window.initSearch = initSearch;
window.validateFileUpload = validateFileUpload;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.openModal = openModal;
window.closeModal = closeModal;
window.switchTab = switchTab;
window.displayMessages = displayMessages;
window.buildMessagesSignature = buildMessagesSignature;
window.scrollToBottom = scrollToBottom;
window.isNearBottom = isNearBottom;
