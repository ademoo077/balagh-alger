<?php $pageTitle = 'Communauté'; $activeTab = 'feed'; ?>
<style>
.feed-composer { padding: 14px; margin-bottom: 16px; }
.feed-composer-row { display: flex; gap: 10px; align-items: flex-start; }
.feed-composer-input { flex: 1; }
.feed-composer-input textarea { width: 100%; border: none; background: transparent; color: var(--c-text); font-size: 0.88rem; resize: none; outline: none; font-family: inherit; min-height: 60px; }
.feed-composer-input textarea::placeholder { color: var(--c-text-muted); }
.feed-composer-actions { display: flex; align-items: center; gap: 8px; margin-top: 8px; padding-top: 8px; border-top: 1px solid var(--c-card-border); }
.feed-composer-actions button { display: flex; align-items: center; gap: 5px; font-size: 0.78rem; color: var(--c-text-muted); background: none; border: none; cursor: pointer; padding: 4px 8px; border-radius: 8px; }
.feed-composer-actions button:hover { background: var(--c-glass); color: var(--c-accent); }
.feed-photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 4px; margin-top: 8px; border-radius: 12px; overflow: hidden; }
.feed-photo-grid img { width: 100%; aspect-ratio: 1; object-fit: cover; cursor: pointer; }
.feed-comment-form { display: flex; gap: 8px; padding: 8px 14px; }
.feed-comment-form input { flex: 1; border: none; background: transparent; color: var(--c-text); font-size: 0.82rem; outline: none; font-family: inherit; }
.feed-comment-form input::placeholder { color: var(--c-text-muted); }
.feed-comment-form button { background: none; border: none; color: var(--c-accent); font-size: 0.85rem; cursor: pointer; padding: 4px 8px; }
.feed-comments { padding: 0 14px 8px; }
.feed-comment { display: flex; gap: 8px; margin-bottom: 6px; font-size: 0.8rem; }
.feed-comment-avatar { width: 24px; height: 24px; border-radius: 50%; background: var(--c-accent-surface); display: flex; align-items: center; justify-content: center; font-size: 0.6rem; font-weight: 700; color: var(--c-accent); flex-shrink: 0; }
.feed-comment-body { flex: 1; }
.feed-comment-name { font-weight: 600; font-size: 0.78rem; }
.feed-comment-text { color: var(--c-text-secondary); }
.feed-comment-time { font-size: 0.68rem; color: var(--c-text-muted); }
</style>

<div class="c-section-title c-anim-fade">
    <h6><i class="fas fa-users"></i> Communauté</h6>
</div>

<!-- Composer -->
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

<!-- Posts -->
<div id="feedPosts">
<?php if (empty($posts ?? [])): ?>
<div class="c-empty c-anim-fade c-delay-2">
    <i class="fas fa-comments"></i>
    <h5>Aucune publication</h5>
    <p>Soyez le premier à partager avec la communauté !</p>
</div>
<?php else: ?>
<?php foreach (($posts ?? []) as $i => $post):
    $initials = strtoupper(substr($post['first_name'] ?? '?', 0, 1) . substr($post['last_name'] ?? '', 0, 1));
    $liked = !empty($post['user_liked']);
    $ago = \App\Helpers\Helper::timeAgo($post['created_at']);
?>
<div class="c-feed-card c-anim-fade" style="animation-delay:<?= ($i + 2) * 0.05 ?>s;" data-post-id="<?= $post['id'] ?>">
    <div class="c-feed-header">
        <div class="c-feed-avatar"><?= $initials ?></div>
        <div class="c-feed-author">
            <div class="c-feed-author-name"><?= htmlspecialchars(($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? '')) ?></div>
            <div class="c-feed-author-time"><?= $ago ?></div>
        </div>
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
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
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

    // Like / Comment toggle
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-action]');
        if (!btn) return;
        var action = btn.getAttribute('data-action');
        var postId = btn.getAttribute('data-post');

        if (action === 'like') {
            fetch('/feed/' + postId + '/like', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
            }).then(function(r) { return r.json(); }).then(function(d) {
                btn.classList.toggle('liked', d.liked);
                var span = btn.querySelector('span');
                span.textContent = parseInt(span.textContent) + (d.liked ? 1 : -1);
            });
        }
        else if (action === 'comment-toggle') {
            var el = document.getElementById('comments-' + postId);
            el.style.display = el.style.display === 'none' ? 'block' : 'none';
        }
        else if (action === 'send-comment') {
            var input = document.getElementById('comment-input-' + postId);
            var body = input.value.trim();
            if (!body) return;
            var fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            fd.append('body', body);
            fetch('/feed/' + postId + '/comment', { method: 'POST', body: fd })
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.success) {
                        var list = document.getElementById('comment-list-' + postId);
                        var c = d.comment;
                        var div = document.createElement('div');
                        div.className = 'feed-comment';
                        var avatarDiv = document.createElement('div');
                        avatarDiv.className = 'feed-comment-avatar';
                        avatarDiv.textContent = (c.user.first_name || '?')[0];
                        var bodyDiv = document.createElement('div');
                        bodyDiv.className = 'feed-comment-body';
                        var nameSpan = document.createElement('span');
                        nameSpan.className = 'feed-comment-name';
                        nameSpan.textContent = c.user.first_name;
                        var textSpan = document.createElement('span');
                        textSpan.className = 'feed-comment-text';
                        textSpan.textContent = ' ' + c.body;
                        bodyDiv.appendChild(nameSpan);
                        bodyDiv.appendChild(textSpan);
                        div.appendChild(avatarDiv);
                        div.appendChild(bodyDiv);
                        list.appendChild(div);
                        input.value = '';
                    }
                });
        }
    });

    // Enter to send comment
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.matches('[id^="comment-input-"]')) {
            e.preventDefault();
            var postId = e.target.getAttribute('data-post');
            document.querySelector('[data-action="send-comment"][data-post="' + postId + '"]').click();
        }
    });
});
</script>
