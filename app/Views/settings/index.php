<?php $pageTitle = 'Paramètres'; ?>
<h4 class="mb-4"><i class="fas fa-cog me-2 text-primary"></i> Paramètres</h4>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="/settings/update">
            <input type="hidden" name="_token" value="<?= $csrfToken ?>">
            <?php $currentGroup = ''; foreach ($settings as $s): ?>
                <?php if ($s['group_name'] !== $currentGroup): $currentGroup = $s['group_name']; ?>
                <h6 class="text-primary mt-3 mb-2"><?= ucfirst($currentGroup) ?></h6>
                <hr>
                <?php endif; ?>
                <div class="mb-3">
                    <label class="form-label fw-bold"><?= $s['label'] ?></label>
                    <?php if ($s['type'] === 'textarea'): ?>
                    <textarea class="form-control" name="<?= $s['key_name'] ?>" rows="3"><?= $s['value'] ?></textarea>
                    <?php elseif ($s['type'] === 'boolean'): ?>
                    <select class="form-select form-select-sm" name="<?= $s['key_name'] ?>"><option value="1" <?= $s['value']?'selected':'' ?>>Oui</option><option value="0" <?= !$s['value']?'selected':'' ?>>Non</option></select>
                    <?php else: ?>
                    <input type="<?= $s['type'] === 'number' ? 'number' : 'text' ?>" class="form-control" name="<?= $s['key_name'] ?>" value="<?= $s['value'] ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i> Enregistrer</button>
        </form>
    </div>
</div>
