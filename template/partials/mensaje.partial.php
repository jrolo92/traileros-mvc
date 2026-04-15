<?php if (isset($this->notify)): ?>
    <div class="custom-alert alert-notify">
        <div class="alert-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="alert-content">
            <strong>Mensaje</strong>
            <p><?= htmlspecialchars($this->notify) ?></p>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
<?php endif; ?>