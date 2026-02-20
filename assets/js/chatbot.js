/**
 * Skill Owners Static AI Chatbot Logic
 */
document.addEventListener('DOMContentLoaded', () => {
    const fab = document.getElementById('chatbot-toggle');
    const window = document.getElementById('chatbot-window');
    const closeBtn = document.getElementById('chatbot-close');
    const messagesContainer = document.getElementById('chatbot-messages');
    const quickBtns = document.querySelectorAll('.btn-quick');

    const responses = {
        services: "We offer a wide range of senior-level digital solutions, including:<br><br>• <b>Web Development</b> (SaaS, E-commerce, Portals)<br>• <b>SEO & Performance</b> (Aggressive growth strategies)<br>• <b>Digital Marketing</b> (Paid ads, Content, Strategy)<br>• <b>Maintenance</b> (24/7 security monitoring)",
        timeline: "Typical projects follow our Strategic Framework:<br><br>• <b>Business Sites:</b> 2-4 weeks<br>• <b>E-commerce/SaaS:</b> 6-12 weeks<br><br>Every project includes a dedicated project manager to ensure on-time delivery.",
        pricing: "At SkillOwners, we value <b>Radical Transparency</b>.<br><br>We handle direct billing between you and our experts. <b>No platform fees</b>, no hidden escrow deductions, and no intermediary charges.",
        location: "Our primary operations hub is located in <b>Karachi, Pakistan</b>, serving a global clientele with elite-level technical expertise.",
        quote: "Ready to scale? You can get a custom quote by visiting our <a href='/skill_owners/request_quote' class='text-primary fw-bold'>Request a Quote</a> page. Our team typically responds within 24 hours."
    };

    // Toggle Chat
    const toggleChat = () => {
        const isHidden = window.classList.contains('d-none');
        window.classList.toggle('d-none');
        fab.querySelector('.fa-comment-dots').classList.toggle('d-none', !isHidden);
        fab.querySelector('.fa-times').classList.toggle('d-none', isHidden);
    };

    fab.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    // Handle Quick Questions
    quickBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const question = btn.innerText;
            const key = btn.getAttribute('data-question');

            // Add User Message
            addMessage(question, 'user');

            // Add Assistant Thinking (Delay)
            setTimeout(() => {
                addMessage(responses[key], 'assistant');
            }, 500);
        });
    });

    const addMessage = (text, sender) => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${sender}`;
        msgDiv.innerHTML = `<div class="message-content">${text}</div>`;
        messagesContainer.appendChild(msgDiv);

        // Scroll to bottom
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };
});
