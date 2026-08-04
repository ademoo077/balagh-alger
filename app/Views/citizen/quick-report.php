<?php $pageTitle = 'Signaler'; $activeTab = 'reports'; ?>

<div id="qrSteps">
    <!-- Step 1: Photo -->
    <div class="qr-step active" id="qrStep1">
        <div class="qr-step-title">Prenez une photo</div>
        <div style="text-align:center;font-size:0.75rem;color:var(--c-text-muted);margin-bottom:10px;">
            <span id="qrPhotoCount">0</span> / 3 photos max.
        </div>
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
        <div id="qrManualLoc" class="c-card-flat" style="display:none;margin-bottom:12px;padding:12px;">
            <label class="c-label">Adresse manuelle</label>
            <input class="c-input" id="qrManualAddress" placeholder="Entrez votre adresse ou lieu du problème..." maxlength="200">
            <div style="display:flex;gap:8px;margin-top:8px;">
                <input class="c-input" id="qrManualLat" placeholder="Latitude (ex: 36.7538)" style="flex:1;" type="number" step="any">
                <input class="c-input" id="qrManualLng" placeholder="Longitude (ex: 3.0588)" style="flex:1;" type="number" step="any">
            </div>
            <button class="c-btn c-btn-outline c-btn-sm" id="qrUseManualLoc" style="margin-top:8px;width:100%;">
                <i class="fas fa-check"></i> Utiliser cette localisation
            </button>
        </div>
        <div id="qrManualLocConfirm" class="qr-location" style="display:none;">
            <i class="fas fa-location-dot"></i>
            <span id="qrManualLocText">Adresse saisie manuellement</span>
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
    var qrLat = null;
    var qrLng = null;

    // Step nav
    function goToStep(n) {
        document.querySelectorAll('.qr-step').forEach(function(s) { s.classList.remove('active'); });
        document.getElementById('qrStep' + n).classList.add('active');
    }

    function updatePhotoCounter() {
        var el = document.getElementById('qrPhotoCount');
        if (el) el.textContent = photos.length;
    }

    // Photo
    photoInput.addEventListener('change', function(e) {
        var files = Array.from(e.target.files).slice(0, 3 - photos.length);
        photos = photos.concat(files);
        updatePhotoCounter();
        if (photos.length) {
            photoPreview.style.display = 'block';
            photoPreview.innerHTML = photos.map(function(f, i) {
                return '<div class="qr-photo-preview"><img src="' + URL.createObjectURL(f) + '"></div>';
            }).join('');
            document.getElementById('qrToStep2').disabled = false;
        }
        if (photos.length >= 3) {
            document.getElementById('qrPhoto').disabled = true;
        }
    });

    // Drag & drop
    var camLabel = document.getElementById('qrCamLabel');
    camLabel.addEventListener('dragover', function(e) { e.preventDefault(); });
    camLabel.addEventListener('drop', function(e) {
        e.preventDefault();
        var maxAdd = 3 - photos.length;
        var files = Array.from(e.dataTransfer.files).filter(function(f) { return f.type.startsWith('image/'); }).slice(0, maxAdd);
        photos = photos.concat(files);
        updatePhotoCounter();
        if (photos.length) {
            photoPreview.style.display = 'block';
            photoPreview.innerHTML = photos.map(function(f) {
                return '<div class="qr-photo-preview"><img src="' + URL.createObjectURL(f) + '"></div>';
            }).join('');
            document.getElementById('qrToStep2').disabled = false;
        }
        if (photos.length >= 3) {
            document.getElementById('qrPhoto').disabled = true;
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
        // GPS with manual fallback
        var gotLoc = false;
        CGeo.getCurrent(function(loc) {
            if (loc) {
                gotLoc = true;
                qrLat = loc.lat;
                qrLng = loc.lng;
                document.getElementById('qrLocation').style.display = 'flex';
                document.getElementById('qrLocText').textContent = loc.lat.toFixed(5) + ', ' + loc.lng.toFixed(5);
            }
        });
        // Show manual input after 5s if GPS didn't respond
        setTimeout(function() {
            if (!gotLoc) {
                document.getElementById('qrManualLoc').style.display = 'block';
            }
        }, 5000);
    });

    document.getElementById('qrUseManualLoc').addEventListener('click', function() {
        var lat = parseFloat(document.getElementById('qrManualLat').value);
        var lng = parseFloat(document.getElementById('qrManualLng').value);
        var addr = document.getElementById('qrManualAddress').value.trim();
        if (isNaN(lat) || isNaN(lng) || lat < 36 || lat > 37 || lng < 2 || lng > 4) {
            CToast.show('Coordonnées invalides. Latitude: 36-37, Longitude: 2-4', 'error');
            return;
        }
        qrLat = lat;
        qrLng = lng;
        document.getElementById('qrManualLoc').style.display = 'none';
        document.getElementById('qrManualLocConfirm').style.display = 'flex';
        document.getElementById('qrManualLocText').textContent = addr || (lat.toFixed(5) + ', ' + lng.toFixed(5));
        CToast.show('Localisation manuelle enregistrée', 'success');
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
        if (!qrLat || !qrLng) {
            CToast.show('Veuillez entrer votre localisation (GPS ou manuelle).', 'error');
            return;
        }
        var formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('category_id', selectedCat);
        if (selectedSubcat) formData.append('subcategory_id', selectedSubcat);
        var title = document.getElementById('qrTitle').value.trim();
        var desc = document.getElementById('qrDesc').value.trim();
        if (title) formData.append('title', title);
        if (desc) formData.append('description', desc);
        formData.append('latitude', qrLat);
        formData.append('longitude', qrLng);
        photos.forEach(function(p) { formData.append('photos[]', p); });

        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';

        fetch('/quick-report', {
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
