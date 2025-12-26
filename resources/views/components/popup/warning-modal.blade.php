<div id="warningModal" class="warning-modal-overlay" style="display: none;">
    <div class="warning-modal-content">
        <div class="warning-icon-container">
            <svg class="warning-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>
        <h2 class="warning-title">Apakah kamu yakin?</h2>
        <p class="warning-message">Hati-Hati barang palsu dan penipuan</p>
        <div class="warning-buttons">
            <button type="button" class="btn-cancel" onclick="closeWarningModal()">Batal</button>
            <button type="button" class="btn-confirm" onclick="confirmWarningAction()">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<style>
    .warning-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { 
            transform: translateY(-30px);
            opacity: 0;
        }
        to { 
            transform: translateY(0);
            opacity: 1;
        }
    }

    .warning-modal-content {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 40px 50px;
        max-width: 420px;
        width: 90%;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideIn 0.3s ease-out;
    }

    .warning-icon-container {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #FF6B35 0%, #FF4757 100%);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 20px rgba(255, 71, 87, 0.4);
    }

    .warning-icon {
        width: 40px;
        height: 40px;
        color: #FFFFFF;
    }

    .warning-title {
        font-family: 'Chakra Petch', sans-serif;
        font-weight: 700;
        font-size: 28px;
        color: #1A1A1A;
        margin: 0 0 12px 0;
    }

    .warning-message {
        font-family: 'Montserrat', sans-serif;
        font-weight: 500;
        font-size: 16px;
        color: #FF4757;
        margin: 0 0 30px 0;
        padding: 12px 20px;
        background: rgba(255, 71, 87, 0.1);
        border-radius: 8px;
        border-left: 4px solid #FF4757;
    }

    .warning-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-cancel {
        font-family: 'Chakra Petch', sans-serif;
        font-weight: 600;
        font-size: 16px;
        padding: 14px 32px;
        border: 2px solid #E0E0E0;
        background: #FFFFFF;
        color: #666666;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        border-color: #999999;
        background: #F5F5F5;
    }

    .btn-confirm {
        font-family: 'Chakra Petch', sans-serif;
        font-weight: 600;
        font-size: 16px;
        padding: 14px 32px;
        border: none;
        background: linear-gradient(135deg, rgba(6, 124, 194, 0.93) 0%, #0593E3 100%);
        color: #FFFFFF;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(6, 124, 194, 0.3);
    }

    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(6, 124, 194, 0.4);
    }
</style>

<script>
    let pendingForm = null;

    function showWarningModal(form) {
        pendingForm = form;
        document.getElementById('warningModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeWarningModal() {
        document.getElementById('warningModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        pendingForm = null;
    }

    function confirmWarningAction() {
        if (pendingForm) {
            document.getElementById('warningModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            pendingForm.submit();
        }
    }

    document.addEventListener('click', function(e) {
        if (e.target.id === 'warningModal') {
            closeWarningModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeWarningModal();
        }
    });
</script>