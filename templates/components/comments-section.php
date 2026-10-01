<?php
/**
 * Modern Interactive Reader Discussion & Comments Component
 * news-platform / templates / components / comments-section.php
 */
require_once __DIR__ . '/../../src/Core/helpers.php';
require_once __DIR__ . '/../../languages/common.php';

$commentsList = $comments ?? [];

// Group comments into root comments and their replies
$rootComments = [];
$repliesByParent = [];

foreach ($commentsList as $comm) {
    if (empty($comm['parent_id'])) {
        $rootComments[] = $comm;
    } else {
        $repliesByParent[$comm['parent_id']][] = $comm;
    }
}

$totalCommentCount = count($commentsList);

// Helper for generating consistent avatar background colors
function getAvatarBgColor(string $name): string {
    $colors = ['#c8102e', '#0f172a', '#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777'];
    $hash = crc32($name);
    return $colors[abs($hash) % count($colors)];
}

function getInitials(string $name): string {
    $parts = preg_split('/\s+/u', trim($name));
    if (count($parts) >= 2) {
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
    }
    return mb_strtoupper(mb_substr($name, 0, 2));
}
?>

<div class="comments-section-container mt-5 pt-4 border-top" id="commentsSection">
    <!-- Header & Live Counter -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h4 class="editorial-title fw-bold mb-0 text-dark" style="font-size: 1.35rem;">
                <i class="bi bi-chat-square-text-fill text-danger me-2"></i><?= __('comments_title') ?>
            </h4>
            <span class="badge bg-danger rounded-pill px-2.5 py-1 text-xs" id="commentsCounterBadge">
                <?= $totalCommentCount ?>
            </span>
        </div>
        <a href="#commentForm" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold text-xs">
            <i class="bi bi-pencil-square me-1"></i><?= __('leave_comment') ?>
        </a>
    </div>

    <!-- Comments List Stream -->
    <div class="comments-stream mb-5" id="commentsStream">
        <?php if (empty($rootComments)): ?>
            <div class="card border-0 bg-light p-4 text-center rounded-3 shadow-none text-muted mb-4" id="noCommentsPrompt">
                <i class="bi bi-chat-dots fs-2 text-secondary mb-2"></i>
                <p class="mb-0 small fw-medium"><?= __('no_comments_yet') ?></p>
            </div>
        <?php else: ?>
            <?php foreach ($rootComments as $c): ?>
                <div class="comment-thread mb-3" id="comment-thread-<?= $c['id'] ?>">
                    <!-- Parent Comment Card -->
                    <div class="card border-0 bg-white shadow-sm rounded-3 p-3.5 mb-2 comment-card" id="comment-<?= $c['id'] ?>">
                        <div class="d-flex gap-3">
                            <!-- User Initials Avatar -->
                            <div class="comment-avatar rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                style="width: 42px; height: 42px; font-size: 0.85rem; background-color: <?= getAvatarBgColor($c['user_name']) ?>;">
                                <?= getInitials($c['user_name']) ?>
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;"><?= e($c['user_name']) ?></h6>
                                        <?php if (str_contains(strtolower($c['user_name']), 'editor') || str_contains(strtolower($c['user_name']), 'desk')): ?>
                                            <span class="badge bg-dark text-white rounded-pill px-2 py-0.5 text-xs font-monospace">STAFF</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="text-xs text-muted">
                                        <i class="bi bi-clock me-1"></i><?= \App\Core\TemplateEngine::timeAgo($c['created_at']) ?>
                                    </span>
                                </div>

                                <p class="comment-text text-dark mb-2" style="font-size: 0.925rem; line-height: 1.55; white-space: pre-line;"><?= e($c['content']) ?></p>

                                <!-- Comment Actions: Like & Reply -->
                                <div class="d-flex align-items-center gap-3 pt-1">
                                    <button type="button" class="btn btn-link text-decoration-none p-0 comment-like-btn text-muted text-xs fw-semibold d-inline-flex align-items-center gap-1"
                                        data-comment-id="<?= $c['id'] ?>">
                                        <i class="bi bi-heart text-danger"></i>
                                        <span><?= __('like') ?></span>
                                        <span class="likes-count badge bg-light text-dark border rounded-pill px-1.5 py-0.5" style="font-size: 0.7rem;">
                                            <?= (int)$c['likes_count'] ?>
                                        </span>
                                    </button>

                                    <button type="button" class="btn btn-link text-decoration-none p-0 comment-reply-btn text-muted text-xs fw-semibold d-inline-flex align-items-center gap-1"
                                        data-parent-id="<?= $c['id'] ?>" data-parent-name="<?= e($c['user_name']) ?>">
                                        <i class="bi bi-reply-fill text-secondary"></i>
                                        <span><?= __('reply') ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nested Child Replies Stream -->
                    <?php if (!empty($repliesByParent[$c['id']])): ?>
                        <div class="comment-replies ms-4 ms-md-5 ps-2 border-start border-2 border-danger-subtle">
                            <?php foreach ($repliesByParent[$c['id']] as $reply): ?>
                                <div class="card border-0 bg-light-subtle shadow-sm rounded-3 p-3 mb-2 comment-card" id="comment-<?= $reply['id'] ?>">
                                    <div class="d-flex gap-2.5">
                                        <div class="comment-avatar rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                            style="width: 34px; height: 34px; font-size: 0.75rem; background-color: <?= getAvatarBgColor($reply['user_name']) ?>;">
                                            <?= getInitials($reply['user_name']) ?>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center justify-content-between mb-1 flex-wrap gap-1">
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.88rem;"><?= e($reply['user_name']) ?></h6>
                                                    <?php if (str_contains(strtolower($reply['user_name']), 'editor') || str_contains(strtolower($reply['user_name']), 'desk')): ?>
                                                        <span class="badge bg-danger text-white rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">EDITORIAL</span>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="text-xs text-muted">
                                                    <?= \App\Core\TemplateEngine::timeAgo($reply['created_at']) ?>
                                                </span>
                                            </div>
                                            <p class="comment-text text-dark mb-2" style="font-size: 0.875rem; line-height: 1.5; white-space: pre-line;"><?= e($reply['content']) ?></p>
                                            
                                            <div class="d-flex align-items-center gap-3">
                                                <button type="button" class="btn btn-link text-decoration-none p-0 comment-like-btn text-muted text-xs fw-semibold d-inline-flex align-items-center gap-1"
                                                    data-comment-id="<?= $reply['id'] ?>">
                                                    <i class="bi bi-heart text-danger"></i>
                                                    <span><?= __('like') ?></span>
                                                    <span class="likes-count badge bg-light text-dark border rounded-pill px-1.5 py-0.5" style="font-size: 0.7rem;">
                                                        <?= (int)$reply['likes_count'] ?>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Comment Submission Form Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4" id="commentFormCard">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold mb-0 text-dark editorial-title" style="font-size: 1.15rem;">
                <?= __('leave_comment') ?>
            </h5>
            <div id="replyingToBadge" class="d-none align-items-center gap-2 bg-light border px-2.5 py-1 rounded-pill text-xs">
                <span>Replying to: <strong id="replyingToName" class="text-danger"></strong></span>
                <button type="button" class="btn btn-link text-muted p-0 text-decoration-none" id="cancelReplyBtn" title="<?= __('cancel_reply') ?>">
                    <i class="bi bi-x-circle-fill"></i>
                </button>
            </div>
        </div>

        <div id="commentAlertBox" class="alert d-none mb-3 py-2 px-3 text-xs rounded-2"></div>

        <form id="commentForm" action="<?= url('comment.php') ?>" method="POST">
            <input type="hidden" name="article_id" value="<?= (int)$article['id'] ?>">
            <input type="hidden" name="parent_id" id="commentParentId" value="">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-uppercase text-secondary tracking-wider mb-1"><?= __('name_placeholder') ?> *</label>
                    <input type="text" name="user_name" class="form-control rounded-2 text-sm shadow-none" placeholder="e.g. Sophal Meas" required maxlength="80">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-xs fw-bold text-uppercase text-secondary tracking-wider mb-1"><?= __('email_placeholder') ?> *</label>
                    <input type="email" name="user_email" class="form-control rounded-2 text-sm shadow-none" placeholder="e.g. reader@example.com" required maxlength="120">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-xs fw-bold text-uppercase text-secondary tracking-wider mb-1"><?= __('comment_placeholder') ?> *</label>
                <textarea name="content" class="form-control rounded-2 text-sm shadow-none" rows="4" placeholder="<?= __('comment_placeholder') ?>" required maxlength="3000"></textarea>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="text-xs text-muted">
                    <i class="bi bi-shield-check text-success me-1"></i>Moderated for civil, constructive discourse.
                </span>
                <button type="submit" class="btn btn-danger px-4 py-2 fw-bold rounded-2 text-xs d-inline-flex align-items-center gap-2" id="submitCommentBtn">
                    <i class="bi bi-send-fill"></i>
                    <span><?= __('post_comment') ?></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const commentForm = document.getElementById('commentForm');
    const submitBtn = document.getElementById('submitCommentBtn');
    const alertBox = document.getElementById('commentAlertBox');
    const parentIdInput = document.getElementById('commentParentId');
    const replyingBadge = document.getElementById('replyingToBadge');
    const replyingName = document.getElementById('replyingToName');
    const cancelReplyBtn = document.getElementById('cancelReplyBtn');
    const commentsStream = document.getElementById('commentsStream');
    const commentsCounter = document.getElementById('commentsCounterBadge');
    const noCommentsPrompt = document.getElementById('noCommentsPrompt');

    // 1. Reply Button Action
    document.addEventListener('click', function (e) {
        const replyBtn = e.target.closest('.comment-reply-btn');
        if (replyBtn) {
            e.preventDefault();
            const parentId = replyBtn.getAttribute('data-parent-id');
            const authorName = replyBtn.getAttribute('data-parent-name');
            if (parentIdInput) parentIdInput.value = parentId;
            if (replyingName) replyingName.textContent = authorName;
            if (replyingBadge) {
                replyingBadge.classList.remove('d-none');
                replyingBadge.classList.add('d-flex');
            }
            const formCard = document.getElementById('commentFormCard');
            if (formCard) {
                formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const textarea = formCard.querySelector('textarea[name="content"]');
                if (textarea) textarea.focus();
            }
        }
    });

    // 2. Cancel Reply Action
    if (cancelReplyBtn) {
        cancelReplyBtn.addEventListener('click', function () {
            if (parentIdInput) parentIdInput.value = '';
            if (replyingBadge) {
                replyingBadge.classList.remove('d-flex');
                replyingBadge.classList.add('d-none');
            }
        });
    }

    // 3. Like Button Action
    document.addEventListener('click', function (e) {
        const likeBtn = e.target.closest('.comment-like-btn');
        if (likeBtn) {
            e.preventDefault();
            const commentId = likeBtn.getAttribute('data-comment-id');
            if (!commentId || likeBtn.classList.contains('liked')) return;

            const heartIcon = likeBtn.querySelector('i');
            const countSpan = likeBtn.querySelector('.likes-count');

            // Optimistic UI update
            likeBtn.classList.add('liked');
            if (heartIcon) {
                heartIcon.className = 'bi bi-heart-fill text-danger';
            }

            const formData = new FormData();
            formData.append('action', 'like');
            formData.append('comment_id', commentId);

            fetch('<?= url("comment.php") ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && countSpan) {
                    countSpan.textContent = data.likes_count;
                }
            })
            .catch(err => console.error('Error liking comment:', err));
        }
    });

    // 4. Form Submit Handler (AJAX)
    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Posting...';
            }
            if (alertBox) {
                alertBox.className = 'alert d-none mb-3 py-2 px-3 text-xs rounded-2';
                alertBox.textContent = '';
            }

            const formData = new FormData(commentForm);

            fetch('<?= url("comment.php") ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send-fill"></i> <span><?= addslashes(__("post_comment")) ?></span>';
                }

                if (data.success) {
                    if (alertBox) {
                        alertBox.className = 'alert alert-success d-block mb-3 py-2 px-3 text-xs rounded-2';
                        alertBox.textContent = data.message;
                    }
                    if (noCommentsPrompt) {
                        noCommentsPrompt.style.display = 'none';
                    }

                    // Increment counter badge
                    if (commentsCounter) {
                        let currentCount = parseInt(commentsCounter.textContent.trim()) || 0;
                        commentsCounter.textContent = currentCount + 1;
                    }

                    // Reset form content while preserving name/email for reader convenience
                    const contentField = commentForm.querySelector('textarea[name="content"]');
                    if (contentField) contentField.value = '';
                    if (parentIdInput) parentIdInput.value = '';
                    if (replyingBadge) {
                        replyingBadge.classList.remove('d-flex');
                        replyingBadge.classList.add('d-none');
                    }

                    // Reload page or dynamically append
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert alert-danger d-block mb-3 py-2 px-3 text-xs rounded-2';
                        alertBox.textContent = data.message || 'Error posting comment.';
                    }
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send-fill"></i> <span><?= addslashes(__("post_comment")) ?></span>';
                }
                if (alertBox) {
                    alertBox.className = 'alert alert-danger d-block mb-3 py-2 px-3 text-xs rounded-2';
                    alertBox.textContent = 'Network error. Please try again.';
                }
            });
        });
    }
});
</script>
