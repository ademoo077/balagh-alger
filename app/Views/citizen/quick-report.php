<?php $pageTitle = 'Signaler'; $activeTab = 'reports'; ?>
<style>
.qr-step { display: none; }
.qr-step.active { display: block; }
.qr-step-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 16px; text-align: center; }
.qr-cat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.qr-cat-btn { display: flex; flex-direction: column; align-items: center; gap: 6px; padding: 14px 8px; border-radius: 14px; border: 2px solid var(--c-glass-border); background: var(--c-glass); color: var(--c-text-secondary); cursor: pointer; transition: all 0.2s; font-size: 0.75rem; font-weight: 500; text-align: center; }
.qr-cat-btn i { font-size: 1.3rem; }
.qr-cat-btn:active { transform: scale(0.95); }
.qr-cat-btn.selected { border-color: var(--c-accent); background: var(--c-accent-surface); color: var(--c-accent); box-shadow: 0 0 0 3px var(--c-accent-glow); }
.qr-sub-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-top: 12px; }
.qr-photo-preview { width: 100%; border-radius: var(--c-radius); overflow: hidden; margin-bottom: 16px; position: relative; }
.qr-photo-preview img { width: 100%; aspect-ratio: 4/3; object-fit: cover; }
.qr-photo-preview .remove-photo { position: absolute; top: 8px; right: 8px; width: 32px; height: 32px; border-radius: 50%; background: rgba(0,0,0,0.6); color: #fff; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; }
.qr-location { display: flex; align-items: center; gap: 8px; padding: 12px; background: var(--c-green-surface); border-radius: var(--c-radius-sm); color: var(--c-green); font-size: 0.82rem; margin-bottom: 12px; }
.qr-voice-btn { width: 48px; height: 48px; border-radius: 50%; border: 2px solid var(--c-accent); background: transparent; color: var(--c-accent); font-size: 1.1rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; flex-shrink: 0; }
.qr-voice-btn.recording { background: var(--c-red); border-color: var(--c-red); color: #fff; animation: cPulse 1s infinite; }
</style>

<div id="qrSteps">
    <!-- Step 1: Photo -->
    <div class="qr-step active" id="qrStep1">
        <div class="qr-step-title">Prenez une photo</div>
        <label class="c-quick-cam" id="qrCamLabel">
            <i class="fas fa-camera"></i>
            <span>Appuyez pour capturer</span>
            <small>Ou glissez une photo ici</small>
            <input type="file" accept="image/*" capture="environment" id="qrPhoto" style="display:none;" multiple>
        </label>
        <div id="qrPhotoPreview" style="display:none;"></div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button class="c-btn c-btn-outline c-btn-block" id="qrSkipPhoto">Passer la photo</button>
            <button class="c-btn c-btn-primary c-btn-block" id="qrToStep2" disabled>Continuer</button>
        </div>
    </div>

    <!-- Step 2: Category -->
    <div class="qr-step" id="qrStep2">
        <div class="qr-step-title">Quel est le problème ?</div>
        <div class="qr-cat-grid" id="qrCatGrid">
            <?php foreach ($categories as $cat): ?>
            <button class="qr-cat-btn" data-id="<?= $cat['id'] ?>" style="--cat-color:<?= $cat['color'] ?>">
                <i class="<?= $cat['icon'] ?>" style="color:<?= $cat['color'] ?>"></i>
                <?= $cat['name'] ?>
            </button>
            <?php endforeach; ?>
        </div>
        <div id="qrSubCats" style="display:none;margin-top:12px;"></div>
        <div style="display:flex;gap:10px;margin-top:16px;">
            <button class="c-btn c-btn-outline" id="qrBackStep1"><i class="fas fa-arrow-left"></i></button>
            <button class="c-btn c-btn-primary c-btn-block" id="qrToStep3" disabled>Continuer</button>
        </div>
    </div>

    <!-- Step 3: Confirm & Send -->
    <div class="qr-step" id="qrStep3">
        <div class="qr-step-title">Confirmer l'envoi</div>
        <div id="qrLocation" class="qr-location" style="display:none;">
            <i class="fas fa-location-dot"></i>
            <span id="qrLocText">Localisation...</span>
        </div>
        <div class="c-card-flat" style="margin-bottom:12px;">
            <label class="c-label">Titre (optionnel)</label>
            <input class="c-input" id="qrTitle" placeholder="Décrvez le problème en un mot..." maxlength="100">
        </div>
        <div class="c-card-flat" style="margin-bottom:12px;">
            <label class="c-label">Description (optionnel)</label>
            <div style="display:flex;gap:8px;align-items:flex-start;">
                <textarea class="c-input c-textarea" id="qrDesc" placeholder="Détails..." rows="3" style="flex:1;"></textarea>
                <button class="qr-voice-btn" id="qrVoice" title="Voice input" style="display:none;">
                    <i class="fas fa-microphone"></i>
                </button>
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <button class="c-btn c-btn-outline" id="qrBackStep2"><i class="fas fa-arrow-left"></i></button>
            <button class="c-btn c-btn-primary c-btn-block" id="qrSubmit">
                <i class="fas fa-paper-plane"></i> Envoyer le signalement
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var photoInput = document.getElementById('qrPhoto');
    var photoPreview = document.getElementById('qrPhotoPreview');
    var photos = [];
    var selectedCat = null;
    var selectedSubcat = null;

    // Step nav
    function goToStep(n) {
        document.querySelectorAll('.qr-step').forEach(function(s) { s.classList.remove('active'); });
        document.getElementById('qrStep' + n).classList.add('active');
    }

    // Photo
    photoInput.addEventListener('change', function(e) {
        var files = Array.from(e.target.files).slice(0, 3);
        photos = files;
        if (files.length) {
            photoPreview.style.display = 'block';
            photoPreview.innerHTML = files.map(function(f) {
                return '<div class="qr-photo-preview"><img src="' + URL.createObjectURL(f) + '"></div>';
            }).join('');
            document.getElementById('qrToStep2').disabled = false;
        }
    });

    // Drag & drop
    var camLabel = document.getElementById('qrCamLabel');
    camLabel.addEventListener('dragover', function(e) { e.preventDefault(); });
    camLabel.addEventListener('drop', function(e) {
        e.preventDefault();
        var files = Array.from(e.dataTransfer.files).filter(function(f) { return f.type.startsWith('image/'); }).slice(0, 3);
        if (files.length) {
            photos = files;
            photoPreview.style.display = 'block';
            photoPreview.innerHTML = files.map(function(f) {
                return '<div class="qr-photo-preview"><img src="' + URL.createObjectURL(f) + '"></div>';
            }).join('');
            document.getElementById('qrToStep2').disabled = false;
        }
    });

    document.getElementById('qrSkipPhoto').addEventListener('click', function() { goToStep(2); });
    document.getElementById('qrToStep2').addEventListener('click', function() { goToStep(2); });
    document.getElementById('qrBackStep1').addEventListener('click', function() { goToStep(1); });

    // Categories
    var catBtns = document.querySelectorAll('#qrCatGrid .qr-cat-btn');
    catBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            catBtns.forEach(function(b) { b.classList.remove('selected'); });
            this.classList.add('selected');
            selectedCat = this.getAttribute('data-id');
            selectedSubcat = null;
            document.getElementById('qrToStep3').disabled = false;
            // Load subcategories
            fetch('/api/subcategories/' + selectedCat).then(function(r) { return r.json(); }).then(function(subs) {
                var el = document.getElementById('qrSubCats');
                if (subs.length) {
                    el.style.display = 'block';
                    el.innerHTML = '<label class="c-label">Sous-catégorie (optionnel)</label><div class="qr-sub-grid">' +
                        subs.map(function(s) { return '<button class="qr-cat-btn" data-sub="' + s.id + '">' + s.name + '</button>'; }).join('') +
                        '</div>';
                    el.querySelectorAll('[data-sub]').forEach(function(sb) {
                        sb.addEventListener('click', function() {
                            el.querySelectorAll('[data-sub]').forEach(function(b) { b.classList.remove('selected'); });
                            this.classList.add('selected');
                            selectedSubcat = this.getAttribute('data-sub');
                        });
                    });
                } else { el.style.display = 'none'; }
            });
        });
    });

    document.getElementById('qrToStep3').addEventListener('click', function() {
        goToStep(3);
        // GPS
        CGeo.getCurrent(function(loc) {
            if (loc) {
                document.getElementById('qrLocation').style.display = 'flex';
                document.getElementById('qrLocText').textContent = loc.lat.toFixed(5) + ', ' + loc.lng.toFixed(5);
            }
        });
    });
    document.getElementById('qrBackStep2').addEventListener('click', function() { goToStep(2); });

    // Voice
    if (CVoice.available()) {
        document.getElementById('qrVoice').style.display = 'flex';
        document.getElementById('qrVoice').addEventListener('click', function() {
            var btn = this;
            btn.classList.add('recording');
            CVoice.start(function(text) {
                btn.classList.remove('recording');
                if (text) {
                    var ta = document.getElementById('qrDesc');
                    ta.value = (ta.value ? ta.value + ' ' : '') + text;
                }
            });
        });
    }

    // Submit
    document.getElementById('qrSubmit').addEventListener('click', function() {
        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('category_id', selectedCat);
        if (selectedSubcat) formData.append('subcategory_id', selectedSubcat);
        var title = document.getElementById('qrTitle').value.trim();
        var desc = document.getElementById('qrDesc').value.trim();
        if (title) formData.append('title', title);
        if (desc) formData.append('description', desc);
        photos.forEach(function(p) { formData.append('photos[]', p); });

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

        fetch('/reports/store', {
            method: 'POST',
            body: formData
        }).then(function(r) { return r.json(); }).then(function(data) {
            if (data.success) {
                if (data.offline) {
                    CToast.show(data.message || 'Signalement enregistré hors ligne', 'success');
                    setTimeout(function() { window.location.href = '/reports'; }, 1200);
                } else {
                    CToast.show('Signalement envoyé !', 'success');
                    setTimeout(function() { window.location.href = '/reports/' + (data.report_id || ''); }, 800);
                }
            } else {
                CToast.show(data.message || 'Erreur lors de l\'envoi', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer le signalement';
            }
        }).catch(function() {
            CToast.show('Réseau indisponible. Réessayez plus tard.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane"></i> Envoyer le signalement';
        });
    });
});
</script>
