<?php if (isset($this->error)): ?>
    <div class="custom-alert alert-error">
        <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="alert-content">
            <strong>Error</strong>
            <p><?= $this->error; ?></p>
        </div>
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">
            <i class="fas fa-times"></i>
        </button>
    </div>
<?php endif; ?>