<?php
/**
 * Skill Owners Static AI Chatbot Partial
 * Provides a premium, glassmorphism-styled chat interface for common questions.
 */
?>
<div id="skill-chatbot" class="chatbot-container">
    <!-- Chat Toggle FAB -->
    <button class="chatbot-fab" id="chatbot-toggle" aria-label="Toggle Chat">
        <i class="fas fa-comment-dots"></i>
        <i class="fas fa-times d-none"></i>
    </button>

    <!-- Chat Window -->
    <div class="chatbot-window d-none" id="chatbot-window">
        <div class="chatbot-header">
            <div class="d-flex align-items-center gap-2">
                <div class="logo-mini">SO</div>
                <div>
                    <h6 class="mb-0 fw-bold">Skill Assistant</h6>
                    <small class="text-success-so">● Online</small>
                </div>
            </div>
            <button class="btn-close btn-close-white" id="chatbot-close"></button>
        </div>

        <div class="chatbot-body" id="chatbot-messages">
            <div class="message assistant">
                <div class="message-content">
                    Hello! I'm your SkillOwners assistant. How can I help you today?
                </div>
            </div>
        </div>

        <div class="chatbot-footer">
            <div class="quick-questions">
                <button class="btn-quick" data-question="services">What services do you offer?</button>
                <button class="btn-quick" data-question="timeline">How long do projects take?</button>
                <button class="btn-quick" data-question="pricing">Do you charge platform fees?</button>
                <button class="btn-quick" data-question="location">Where are you located?</button>
                <button class="btn-quick" data-question="quote">How can I get a quote?</button>
            </div>
        </div>
    </div>
</div>
