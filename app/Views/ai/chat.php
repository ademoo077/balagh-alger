<div class="ai-chat-page">
    <div class="ai-container">
        <div class="ai-header">
            <div class="ai-header-left">
                <div class="ai-avatar">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="ai-header-info">
                    <h2 class="ai-title"><?= __('nav.ai') ?></h2>
                    <div class="ai-status">
                        <span class="ai-status-dot"></span>
                        <span class="ai-status-text"><?= __('ai.online') ?></span>
                    </div>
                </div>
            </div>
            <div class="ai-header-actions">
                <button class="ai-btn-icon" id="aiClearBtn" title="<?= __('ai.clear') ?>">
                    <i class="fas fa-eraser"></i>
                </button>
                <button class="ai-btn-icon" id="aiThemeBtn" title="<?= __('nav.change_theme') ?>">
                    <i class="fas fa-palette"></i>
                </button>
            </div>
        </div>

        <div class="ai-messages" id="aiMessages">
            <div class="ai-message ai-message-assistant">
                <div class="ai-message-avatar">
                    <i class="fas fa-brain"></i>
                </div>
                <div class="ai-message-content">
                    <div class="ai-message-text">
                        <?= __('ai.welcome') ?>
                    </div>
                    <div class="ai-welcome-suggestions">
                        <button class="ai-suggestion-btn" data-question="Quel est le nombre total de signalements ?">📊 Total signalements</button>
                        <button class="ai-suggestion-btn" data-question="Combien de signalements urgents ?">🔴 Signalements urgents</button>
                        <button class="ai-suggestion-btn" data-question="Quels sont les derniers signalements ?">🆕 Derniers signalements</button>
                        <button class="ai-suggestion-btn" data-question="Top catégories de signalements">📈 Top catégories</button>
                    </div>
                    <div class="ai-message-time">
                        <?= __('ai.just_now') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ai-input-area">
            <div class="ai-input-wrapper">
                <textarea class="ai-input" id="aiInput" rows="1" placeholder="<?= __('ai.placeholder') ?>" autocomplete="off"></textarea>
                <button class="ai-send-btn" id="aiSendBtn" disabled>
                    <i class="fas fa-arrow-up"></i>
                </button>
            </div>
            <div class="ai-footer-text">
                <i class="fas fa-robot"></i>
                <?= __('ai.disclaimer') ?>
            </div>
        </div>
    </div>
</div>
