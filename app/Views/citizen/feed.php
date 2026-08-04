<?php $pageTitle = 'Communauté'; $activeTab = 'feed';
$currentUserId = \App\Helpers\Session::getUserId();
?>
<div class="c-ptr" id="ptrIndicator" style="display:none;">
    <div class="c-ptr-content">
        <i class="fas fa-arrow-down c-ptr-icon"></i>
        <span class="c-ptr-text">Tirer pour actualiser</span>
    </div>
</div>

<div class="c-section-title c-anim-fade">
    <h6><i class="fas fa-users"></i> Communauté</h6>
</div>

<div class="c-card feed-composer c-anim-fade c-delay-1">
    <div class="feed-composer-row">
        <div class="c-feed-avatar" style="width:36px;height:36px;font-size:0.8rem;">
            <?= strtoupper(substr(\App\Helpers\Session::getUserName(), 0, 1)) ?>
        </div>
        <div class="feed-composer-input">
            <textarea id="feedBody" placeholder="Partagez un retour sur les travaux..."></textarea>
            <div id="feedPhotoPreview" class="feed-photo-grid"></div>
            <div class="feed-composer-actions">
                <button id="feedAddPhoto"><i class="fas fa-image"></i> Photo</button>
                <input type="file" accept="image/*" id="feedPhotoInput" multiple style="display:none;">
                <div style="flex:1;"></div>
                <button id="feedSubmit"><i class="fas fa-paper-plane"></i> Publier</button>
            </div>
        </div>
    </div>
</div>

<div id="feedPosts">
<?php if (empty($posts ?? [])): ?>
<div class="feed-empty c-anim-fade c-delay-2">
    <svg class="c-empty-svg" viewBox="0 0 140 120" fill="none">
        <circle class="c-sv-circle" cx="70" cy="60" r="50"/>
        <circle class="c-sv-icon-bg" cx="70" cy="56" r="18"/>
        <path class="c-sv-icon c-sv-float" d="M62 52l8 6 10-10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
        <circle class="c-sv-dot" cx="36" cy="38" r="4"/>
        <circle class="c-sv-dot" cx="106" cy="42" r="3"/>
        <circle class="c-sv-dot" cx="100" cy="80" r="3.5"/>
        <circle class="c-sv-dot" cx="42" cy="82" r="2.5"/>
    </svg>
    <h5>Aucune publication</h5>
    <p>Soyez le premier à partager avec la communauté !</p>
</div>
<?php else: ?>
<?php foreach (($posts ?? []) as $i => $post):
    $initials = strtoupper(substr($post['first_name'] ?? '?', 0, 1) . substr($post['last_name'] ?? '', 0, 1));
    $liked = !empty($post['user_liked']);
    $ago = \App\Helpers\Helper::timeAgo($post['created_at']);
    $isOwn = (int)$post['user_id'] === (int)$currentUserId;
?>
<div class="c-feed-card c-anim-fade" style="animation-delay:<?= ($i + 2) * 0.05 ?>s;" data-post-id="<?= $post['id'] ?>">
    <div class="c-feed-header">
        <div class="c-feed-avatar"><?= $initials ?></div>
        <div class="c-feed-author">
            <div class="c-feed-author-name"><?= htmlspecialchars(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? '')) ?></div>
            <div class="c-feed-author-time"><?= $ago ?></div>
        </div>
        <?php if ($isOwn): ?>
        <div class="feed-own-actions">
            <button class="feed-delete-post" data-post="<?= $post['id'] ?>" title="Supprimer"><i class="fas fa-trash-can"></i></button>
        </div>
        <?php endif; ?>
    </div>
    <?php if (!empty($post['title'])): ?>
    <div class="c-feed-body" style="padding-top:0;">
        <div class="c-feed-title"><?= htmlspecialchars($post['title']) ?></div>
    </div>
    <?php endif; ?>
    <?php if (!empty($post['body'])): ?>
    <div class="c-feed-body" style="padding-top:0;">
        <div class="c-feed-desc"><?= nl2br(htmlspecialchars($post['body'])) ?></div>
    </div>
    <?php endif; ?>
    <?php if (!empty($post['photos'])): ?>
    <div class="feed-photo-grid" style="margin:0;border-radius:0;">
        <?php foreach ($post['photos'] as $photo): ?>
        <img src="/uploads/community/<?= htmlspecialchars($photo['filename']) ?>" alt="" loading="lazy">
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="c-feed-actions">
        <button class="c-feed-action <?= $liked ? 'liked' : '' ?>" data-action="like" data-post="<?= $post['id'] ?>">
            <i class="fas fa-heart"></i>
            <span><?= $post['likes_count'] ?></span>
        </button>
        <button class="c-feed-action" data-action="comment-toggle" data-post="<?= $post['id'] ?>">
            <i class="fas fa-comment"></i>
            <span><?= $post['comments_count'] ?></span>
        </button>
        <button class="c-feed-action" data-action="share" data-post="<?= $post['id'] ?>" style="margin-left:auto;">
            <i class="fas fa-share-nodes"></i>
        </button>
    </div>
    <div class="feed-comments" id="comments-<?= $post['id'] ?>" style="display:none;">
        <div class="feed-comment-list" id="comment-list-<?= $post['id'] ?>"></div>
        <div class="feed-comment-form">
            <input type="text" placeholder="Écrire un commentaire..." id="comment-input-<?= $post['id'] ?>" data-post="<?= $post['id'] ?>">
            <button data-action="send-comment" data-post="<?= $post['id'] ?>"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var feedBody = document.getElementById('feedBody');
    var feedPhotos = document.getElementById('feedPhotoInput');
    var feedPreview = document.getElementById('feedPhotoPreview');
    var selectedPhotos = [];
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    var currentUserId = <?= json_encode($currentUserId) ?>;

    document.getElementById('feedAddPhoto').addEventListener('click', function() { feedPhotos.click(); });

    feedPhotos.addEventListener('change', function() {
        selectedPhotos = Array.from(this.files).slice(0, 4);
        feedPreview.innerHTML = selectedPhotos.map(function(f) {
            return '<img src="' + URL.createObjectURL(f) + '" alt="">';
        }).join('');
    });

    document.getElementById('feedSubmit').addEventListener('click', function() {
        var body = feedBody.value.trim();
        if (!body) { CToast.show('Écrivez quelque chose'); return; }
        var fd = new FormData();
        fd.append('_token', csrfToken);
        fd.append('title', body.substring(0, 80));
        fd.append('body', body);
        fd.append('post_type', 'discussion');
        selectedPhotos.forEach(function(p) { fd.append('photos[]', p); });

        var btn = this;
        btn.disabled = true;
        fetch('/feed', { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (d.success) { location.reload(); }
                else { CToast.show(d.message || 'Erreur', 'error'); btn.disabled = false; }
            });
    });

    function loadComments(postId, containerId) {
        var list = document.getElementById(containerId);
        if (!list) return;
        if (list.dataset.loaded === '1') return;
        list.dataset.loaded = '1';
        fetch('/feed/' + postId + '/comments')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                if (!d.success || !d.comments) return;
                list.innerHTML = '';
                d.comments.forEach(function(c) {
                    var div = document.createElement('div');
                    div.className = 'feed-comment';
                    var av = document.createElement('div');
                    av.className = 'feed-comment-avatar';
                    av.textContent = (c.first_name || '?')[0];
                    var bd = document.createElement('div');
                    bd.className = 'feed-comment-body';
                    var nm = document.createElement('span');
                    nm.className = 'feed-comment-name';
                    nm.textContent = (c.first_name || '') + ' ' + (c.last_name || '');
                    var tx = document.createElement('span');
                    tx.className = 'feed-comment-text';
                    tx.textContent = ' ' + c.body;
                    bd.appendChild(nm);
                    bd.appendChild(tx);
                    div.appendChild(av);
                    div.appendChild(bd);
                    if (parseInt(c.user_id) === currentUserId) {
                        var del = document.createElement('button');
                        del.className = 'feed-comment-del';
                        del.innerHTML = '<i class="fas fa-xmark"></i>';
                        del.dataset.post = postId;
                        del.dataset.comment = c.id;
                        del.addEventListener('click', function(e) {
                            e.stopPropagation();
                            if (!confirm('Supprimer ce commentaire ?')) return;
                            var f = new FormData();
                            f.append('_token', csrfToken);
                            fetch('/feed/' + this.dataset.post + '/comments/' + this.dataset.comment + '/delete', { method: 'POST', body: f })
                                .then(function(r) { return r.json(); })
                                .then(function(d) {
                                    if (d.success) { div.remove(); CToast.show('Commentaire supprimé', 'success'); }
                                    else { CToast.show('Erreur', 'error'); }
                                });
                        });
                        div.appendChild(del);
                    }
                    var tm = document.createElement('div');
                    tm.style.cssText = 'font-size:0.65rem;color:var(--c-text-muted);margin-top:2px;';
                    tm.textContent = c.created_at ? c.created_at.substring(0, 16).replace('T', ' ') : '';
                    bd.appendChild(tm);
                    list.appendChild(div);
                });
            });
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action]');
        if (btn) {
            var action = btn.getAttribute('data-action');
            var postId = btn.getAttribute('data-post');

            if (action === 'like') {
                fetch('/feed/' + postId + '/like', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': csrfToken}
                }).then(function(r) { return r.json(); }).then(function(d) {
                    btn.classList.toggle('liked', d.liked);
                    var span = btn.querySelector('span');
                    span.textContent = parseInt(span.textContent) + (d.liked ? 1 : -1);
                });
            }
            else if (action === 'comment-toggle') {
                var el = document.getElementById('comments-' + postId);
                var isHidden = el.style.display === 'none' || el.style.display === '';
                el.style.display = isHidden ? 'block' : 'none';
                if (isHidden) loadComments(postId, 'comment-list-' + postId);
            }
            else if (action === 'send-comment') {
                var input = document.getElementById('comment-input-' + postId);
                var body = input.value.trim();
                if (!body) return;
                var fd = new FormData();
                fd.append('_token', csrfToken);
                fd.append('body', body);
                fetch('/feed/' + postId + '/comment', { method: 'POST', body: fd })
                    .then(function(r) { return r.json(); })
                    .then(function(d) {
                        if (d.success) {
                            loadComments(postId, 'comment-list-' + postId);
                            input.value = '';
                            var countSpan = btn.closest('.c-feed-card').querySelector('[data-action="comment-toggle"] span');
                            if (countSpan) countSpan.textContent = parseInt(countSpan.textContent) + 1;
                        }
                    });
            }
            else if (action === 'share') {
                var url = window.location.origin + '/partager/' + postId;
                if (navigator.share) {
                    navigator.share({ title: 'Balagh Alger', text: 'Regardez ça sur Balagh Alger', url: url });
                } else {
                    navigator.clipboard.writeText(url).then(function() {
                        CToast.show('Lien copié !', 'success');
                    }).catch(function() {
                        prompt('Copiez le lien :', url);
                    });
                }
            }
            return;
        }

        // Delete post
        var delBtn = e.target.closest('.feed-delete-post');
        if (delBtn) {
            if (!confirm('Supprimer cette publication ?')) return;
            var postId = delBtn.dataset.post;
            var fd = new FormData();
            fd.append('_token', csrfToken);
            fetch('/feed/' + postId + '/delete', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.success) {
                        delBtn.closest('.c-feed-card').remove();
                        CToast.show('Publication supprimée', 'success');
                    } else { CToast.show('Erreur', 'error'); }
                });
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.matches('[id^="comment-input-"]')) {
            e.preventDefault();
            var postId = e.target.getAttribute('data-post');
            document.querySelector('[data-action="send-comment"][data-post="' + postId + '"]').click();
        }
    });

    // Pull-to-refresh
    (function() {
        var ptr = document.getElementById('ptrIndicator');
        if (!ptr) return;
        var startY = 0, pulling = false, released = false;
        var main = document.getElementById('cMain');
        if (!main) main = document.querySelector('.c-container') || document.body;

        main.addEventListener('touchstart', function(e) {
            if (window.scrollY > 0) return;
            startY = e.touches[0].clientY;
            pulling = true;
            released = false;
        }, { passive: true });

        main.addEventListener('touchmove', function(e) {
            if (!pulling || released) return;
            var dy = e.touches[0].clientY - startY;
            if (dy < 0) { ptr.style.display = 'none'; return; }
            ptr.style.display = 'flex';
            if (dy > 60) {
                ptr.classList.add('visible', 'release');
                ptr.querySelector('.c-ptr-text').textContent = document.documentElement.lang === 'ar' ? 'أطلق للتحديث' : 'Relâchez pour actualiser';
            } else {
                ptr.classList.remove('release');
                ptr.querySelector('.c-ptr-text').textContent = document.documentElement.lang === 'ar' ? 'اسحب للتحديث' : 'Tirer pour actualiser';
            }
        }, { passive: true });

        main.addEventListener('touchend', function() {
            if (!pulling) return;
            pulling = false;
            if (ptr.classList.contains('release')) {
                released = true;
                ptr.classList.add('loading');
                ptr.querySelector('.c-ptr-icon').className = 'fas fa-spinner c-ptr-icon';
                ptr.querySelector('.c-ptr-text').textContent = document.documentElement.lang === 'ar' ? 'تحديث...' : 'Actualisation...';
                location.reload();
            } else {
                ptr.style.display = 'none';
                ptr.classList.remove('visible');
            }
        }, { passive: true });

        window.addEventListener('scroll', function() {
            if (window.scrollY > 0) ptr.style.display = 'none';
        }, { passive: true });
    })();
});
</script>
