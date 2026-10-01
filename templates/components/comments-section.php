<?php
/**
 * Modern Interactive Reader Discussion & Comments Component (Facebook / YouTube Style)
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

// Auth state for commenting: Reader or Staff
$currentReader = \App\Core\Auth::reader();
$currentStaff = \App\Core\Auth::user();
$isLoggedIn = !empty($currentReader) || !empty($currentStaff);
$currentUser = $currentReader ?: ($currentStaff ? ['name' => $currentStaff['username'], 'email' => $currentStaff['email']] : null);

// Helper for generating consistent avatar background colors
if (!function_exists('getAvatarBgColor')) {
    function getAvatarBgColor(string $name): string {
        $colors = ['#c8102e', '#0f172a', '#1e40af', '#047857', '#b45309', '#6d28d9', '#be185d', '#0369a1'];
        $hash = crc32($name);
        return $colors[abs($hash) % count($colors)];
    }
}

if (!function_exists('getInitials')) {
    function getInitials(string $name): string {
        $parts = preg_split('/\s+/u', trim($name));
        if (count($parts) >= 2) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr($parts[1], 0, 1));
        }
        return mb_strtoupper(mb_substr($name, 0, 2));
    }
}

$currentLang = $_SESSION['lang'] ?? 'kh';
$isKhmer = ($currentLang === 'kh' || $currentLang === 'km');
?>

<div class="comments-section-container mt-4 pt-3 border-top" id="commentsSection">
    <!-- Header: Compact & Clean -->
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-1.5" style="font-size: 1.05rem;">
                <i class="bi bi-chat-left-text-fill text-danger"></i>
                <span><?= __('comments_title') ?></span>
            </h5>
            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2 py-0.5 text-xs fw-bold" id="commentsCounterBadge">
                <?= $isKhmer ? km_num($totalCommentCount) : $totalCommentCount ?>
            </span>
        </div>
        <?php if ($isLoggedIn): ?>
            <a href="#commentContent" class="btn btn-outline-danger btn-sm rounded-pill px-2.5 py-0.5 text-3xs fw-semibold">
                <i class="bi bi-pencil me-1"></i><?= __('leave_comment') ?>
            </a>
        <?php endif; ?>
    </div>

    <!-- Comment Input Area (FB / YouTube Style) -->
    <div class="card border-0 mb-3 rounded-3" id="commentFormCard" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
        <div class="card-body p-2.5 p-sm-3">
            <?php if ($isLoggedIn): ?>
                <!-- Replying To Banner -->
                <div id="replyingToBadge" class="d-none align-items-center justify-content-between bg-white border border-danger-subtle px-2.5 py-1 rounded-2 mb-2 text-2xs">
                    <span class="text-muted">
                        <i class="bi bi-reply-fill text-danger me-1"></i><?= __('replying_to') ?? 'ឆ្លើយតបទៅកាន់' ?>: <strong id="replyingToName" class="text-dark"></strong>
                    </span>
                    <button type="button" class="btn btn-link text-muted p-0 text-decoration-none" id="cancelReplyBtn" title="<?= __('cancel_reply') ?>">
                        <i class="bi bi-x-circle-fill text-secondary"></i>
                    </button>
                </div>

                <div id="commentAlertBox" class="alert d-none mb-2 py-1.5 px-2.5 text-2xs rounded-2"></div>

                <form id="commentForm" action="<?= url('comment.php') ?>" method="POST">
                    <input type="hidden" name="article_id" value="<?= (int)$article['id'] ?>">
                    <input type="hidden" name="parent_id" id="commentParentId" value="">

                    <div class="d-flex align-items-start gap-2">
                        <!-- Current User Avatar -->
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0 shadow-2xs"
                            style="width: 34px; height: 34px; font-size: 0.78rem; background-color: <?= getAvatarBgColor($currentUser['name']) ?>;">
                            <?= getInitials($currentUser['name']) ?>
                        </div>

                        <!-- Textarea & Action Controls -->
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex align-items-center gap-1.5 mb-1">
                                <span class="fw-bold text-dark text-xs"><?= e($currentUser['name']) ?></span>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-3xs px-1.5 py-0 rounded-1">
                                    <?= !empty($currentStaff) ? 'STAFF' : __('verified_reader_badge') ?>
                                </span>
                            </div>
                            <div class="position-relative">
                                <textarea name="content" id="commentContent" class="form-control rounded-3 text-xs shadow-none"
                                    rows="2" placeholder="<?= __('write_a_comment') ?>" required maxlength="3000"
                                    style="background:#ffffff; border:1px solid #cbd5e1; resize:vertical; font-size:0.84rem; line-height:1.45;"></textarea>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-0.5">
                                <span class="text-3xs text-muted d-none d-sm-inline">
                                    <i class="bi bi-shield-check text-success me-1"></i><?= __('civil_discourse_hint') ?? 'មតិស្ថាបនា និងគោរពគ្នាតាមក្រមសីលធម៌' ?>
                                </span>
                                <div class="d-flex align-items-center gap-1.5 ms-auto">
                                    <button type="button" class="btn btn-light btn-sm rounded-pill px-2.5 py-1 text-3xs fw-bold text-secondary border d-none" id="clearCommentBtn">
                                        <?= __('cancel') ?>
                                    </button>
                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 py-1 text-3xs fw-bold shadow-2xs d-inline-flex align-items-center gap-1" id="submitCommentBtn">
                                        <i class="bi bi-send-fill" style="font-size: 0.75rem;"></i>
                                        <span><?= __('post_comment') ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <!-- Not Logged In Callout (YouTube / Facebook Style) -->
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2.5 py-1">
                    <div class="d-flex align-items-center gap-2.5 text-center text-sm-start">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-danger flex-shrink-0"
                            style="width: 38px; height: 38px; background: rgba(200, 16, 46, 0.08);">
                            <i class="bi bi-chat-heart fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark text-xs"><?= __('comment_login_prompt_title') ?></div>
                            <div class="text-muted text-3xs"><?= __('comment_login_prompt_desc') ?></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1.5 flex-shrink-0 mt-1 mt-sm-0">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-2.5 py-1 text-3xs fw-bold rounded-pill text-dark"
                            data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="login">
                            <i class="bi bi-box-arrow-in-right me-1"></i><?= __('sign_in') ?>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm px-2.5 py-1 text-3xs fw-bold rounded-pill shadow-2xs"
                            data-bs-toggle="modal" data-bs-target="#readerAuthModal" data-auth-tab="register">
                            <i class="bi bi-person-plus me-1"></i><?= __('create_account') ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Comments List Stream -->
    <div class="comments-stream mb-4" id="commentsStream">
        <?php if (empty($rootComments)): ?>
            <div class="text-center py-4 px-3 rounded-2 text-muted mb-3" id="noCommentsPrompt" style="background:#f8fafc; border:1px dashed #e2e8f0;">
                <i class="bi bi-chat-square-dots text-secondary mb-1 d-block" style="font-size: 1.6rem; opacity: 0.7;"></i>
                <p class="mb-0 text-xs fw-medium text-secondary"><?= __('no_comments_yet') ?></p>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-2.5">
                <?php foreach ($rootComments as $c): ?>
                    <div class="comment-thread" id="comment-thread-<?= $c['id'] ?>">
                        <!-- Parent Comment Bubble -->
                        <div class="d-flex align-items-start gap-2 comment-item" id="comment-<?= $c['id'] ?>">
                            <!-- Avatar -->
                            <div class="comment-avatar rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                style="width: 32px; height: 32px; font-size: 0.75rem; background-color: <?= getAvatarBgColor($c['user_name']) ?>;">
                                <?= getInitials($c['user_name']) ?>
                            </div>

                            <div class="flex-grow-1 min-w-0">
                                <!-- Facebook-Style Bubble -->
                                <div class="p-2 px-2.5 rounded-3 d-inline-block" style="background: #f1f5f9; max-width: 100%;">
                                    <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                        <span class="fw-bold text-dark text-xs"><?= e($c['user_name']) ?></span>
                                        <?php if (str_contains(strtolower($c['user_name']), 'editor') || str_contains(strtolower($c['user_name']), 'desk')): ?>
                                            <span class="badge bg-danger text-white rounded-pill px-1.5 py-0 text-3xs font-monospace"><?= __('editorial_badge') ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment-text text-dark" style="font-size: 0.83rem; line-height: 1.4; word-break: break-word; white-space: pre-line;"><?= e($c['content']) ?></div>
                                </div>

                                <!-- Action Line (Like, Reply, Time Ago) -->
                                <div class="d-flex align-items-center gap-2.5 ps-2 pt-1 text-3xs text-muted">
                                    <span><?= \App\Core\TemplateEngine::timeAgo($c['created_at']) ?></span>

                                    <button type="button" class="btn btn-link text-decoration-none p-0 comment-like-btn text-muted text-3xs fw-bold d-inline-flex align-items-center gap-1"
                                        data-comment-id="<?= $c['id'] ?>">
                                        <i class="bi bi-heart text-danger"></i>
                                        <span><?= __('like') ?></span>
                                        <?php if ((int)$c['likes_count'] > 0): ?>
                                            <span class="likes-count fw-bold text-danger"><?= (int)$c['likes_count'] ?></span>
                                        <?php else: ?>
                                            <span class="likes-count d-none">0</span>
                                        <?php endif; ?>
                                    </button>

                                    <button type="button" class="btn btn-link text-decoration-none p-0 comment-reply-btn text-muted text-3xs fw-bold d-inline-flex align-items-center gap-1"
                                        data-parent-id="<?= $c['id'] ?>" data-parent-name="<?= e($c['user_name']) ?>">
                                        <span><?= __('reply') ?></span>
                                    </button>
                                </div>

                                <!-- Nested Child Replies Stream -->
                                <?php if (!empty($repliesByParent[$c['id']])): ?>
                                    <div class="comment-replies ms-2 ms-sm-3 ps-2 mt-2 border-start border-2 border-light-subtle d-flex flex-column gap-2">
                                        <?php foreach ($repliesByParent[$c['id']] as $reply): ?>
                                            <div class="d-flex align-items-start gap-2" id="comment-<?= $reply['id'] ?>">
                                                <div class="comment-avatar rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                                    style="width: 26px; height: 26px; font-size: 0.68rem; background-color: <?= getAvatarBgColor($reply['user_name']) ?>;">
                                                    <?= getInitials($reply['user_name']) ?>
                                                </div>
                                                <div class="flex-grow-1 min-w-0">
                                                    <div class="p-1.5 px-2.5 rounded-3 d-inline-block" style="background: #f1f5f9; max-width: 100%;">
                                                        <div class="d-flex align-items-center gap-1.5 mb-0.5">
                                                            <span class="fw-bold text-dark text-xs"><?= e($reply['user_name']) ?></span>
                                                            <?php if (str_contains(strtolower($reply['user_name']), 'editor') || str_contains(strtolower($reply['user_name']), 'desk')): ?>
                                                                <span class="badge bg-danger text-white rounded-pill px-1.5 py-0 text-3xs font-monospace"><?= __('editorial_badge') ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="comment-text text-dark" style="font-size: 0.81rem; line-height: 1.4; word-break: break-word; white-space: pre-line;"><?= e($reply['content']) ?></div>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2.5 ps-2 pt-0.5 text-3xs text-muted">
                                                        <span><?= \App\Core\TemplateEngine::timeAgo($reply['created_at']) ?></span>
                                                        <button type="button" class="btn btn-link text-decoration-none p-0 comment-like-btn text-muted text-3xs fw-bold d-inline-flex align-items-center gap-1"
                                                            data-comment-id="<?= $reply['id'] ?>">
                                                            <i class="bi bi-heart text-danger"></i>
                                                            <span><?= __('like') ?></span>
                                                            <?php if ((int)$reply['likes_count'] > 0): ?>
                                                                <span class="likes-count fw-bold text-danger"><?= (int)$reply['likes_count'] ?></span>
                                                            <?php else: ?>
                                                                <span class="likes-count d-none">0</span>
                                                            <?php endif; ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
    const clearCommentBtn = document.getElementById('clearCommentBtn');
    const commentContent = document.getElementById('commentContent');
    const commentsCounter = document.getElementById('commentsCounterBadge');
    const noCommentsPrompt = document.getElementById('noCommentsPrompt');
    const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;

    // 1. Reply Button Action
    document.addEventListener('click', function (e) {
        const replyBtn = e.target.closest('.comment-reply-btn');
        if (replyBtn) {
            e.preventDefault();
            if (!isLoggedIn) {
                const authModal = document.getElementById('readerAuthModal');
                if (authModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(authModal).show();
                }
                return;
            }

            const parentId = replyBtn.getAttribute('data-parent-id');
            const authorName = replyBtn.getAttribute('data-parent-name');
            if (parentIdInput) parentIdInput.value = parentId;
            if (replyingName) replyingName.textContent = authorName;
            if (replyingBadge) {
                replyingBadge.classList.remove('d-none');
                replyingBadge.classList.add('d-flex');
            }
            if (clearCommentBtn) clearCommentBtn.classList.remove('d-none');

            const formCard = document.getElementById('commentFormCard');
            if (formCard) {
                formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                if (commentContent) {
                    commentContent.placeholder = '<?= addslashes(__('replying_to') ?? 'ឆ្លើយតបទៅកាន់') ?> @' + authorName + '...';
                    commentContent.focus();
                }
            }
        }
    });

    // 2. Cancel Reply Action
    function resetReplyState() {
        if (parentIdInput) parentIdInput.value = '';
        if (replyingBadge) {
            replyingBadge.classList.remove('d-flex');
            replyingBadge.classList.add('d-none');
        }
        if (clearCommentBtn) clearCommentBtn.classList.add('d-none');
        if (commentContent) {
            commentContent.placeholder = '<?= addslashes(__('write_a_comment')) ?>';
        }
    }

    if (cancelReplyBtn) {
        cancelReplyBtn.addEventListener('click', resetReplyState);
    }
    if (clearCommentBtn) {
        clearCommentBtn.addEventListener('click', function () {
            if (commentContent) commentContent.value = '';
            resetReplyState();
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

            let currentLikes = parseInt(countSpan ? countSpan.textContent : '0') || 0;
            currentLikes++;
            if (countSpan) {
                countSpan.textContent = currentLikes;
                countSpan.classList.remove('d-none');
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
                if (data.success && countSpan && data.likes_count) {
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
            if (!isLoggedIn) {
                const authModal = document.getElementById('readerAuthModal');
                if (authModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(authModal).show();
                }
                return;
            }

            const content = commentContent ? commentContent.value.trim() : '';
            if (content.length < 2) return;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Posting...';
            }
            if (alertBox) {
                alertBox.className = 'alert d-none mb-2 py-1.5 px-2.5 text-2xs rounded-2';
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
                    submitBtn.innerHTML = '<i class="bi bi-send-fill" style="font-size:0.75rem;"></i> <span><?= addslashes(__("post_comment")) ?></span>';
                }

                if (data.success) {
                    if (alertBox) {
                        alertBox.className = 'alert alert-success d-block mb-2 py-1.5 px-2.5 text-2xs rounded-2';
                        alertBox.textContent = data.message;
                    }
                    if (noCommentsPrompt) {
                        noCommentsPrompt.style.display = 'none';
                    }

                    if (commentContent) commentContent.value = '';
                    resetReplyState();

                    setTimeout(() => {
                        window.location.reload();
                    }, 600);
                } else if (data.require_login) {
                    const authModal = document.getElementById('readerAuthModal');
                    if (authModal && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        bootstrap.Modal.getOrCreateInstance(authModal).show();
                    }
                } else {
                    if (alertBox) {
                        alertBox.className = 'alert alert-danger d-block mb-2 py-1.5 px-2.5 text-2xs rounded-2';
                        alertBox.textContent = data.message || 'Error posting comment.';
                    }
                }
            })
            .catch(err => {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-send-fill" style="font-size:0.75rem;"></i> <span><?= addslashes(__("post_comment")) ?></span>';
                }
                if (alertBox) {
                    alertBox.className = 'alert alert-danger d-block mb-2 py-1.5 px-2.5 text-2xs rounded-2';
                    alertBox.textContent = 'Network error. Please try again.';
                }
            });
        });
    }
});
</script>
