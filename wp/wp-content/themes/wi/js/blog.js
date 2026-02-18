/**
 * Blog: like/dislike AJAX, TOC smooth scroll.
 * Sticky sidebar is handled by CSS (position: sticky).
 */
(function ($) {
  'use strict';

  var BLOG_STORAGE_KEY = 'blog_voted';

  function getVotedIds() {
    try {
      var raw = localStorage.getItem(BLOG_STORAGE_KEY);
      return raw ? JSON.parse(raw) : {};
    } catch (e) {
      return {};
    }
  }

  function setVoted(postId, type) {
    var data = getVotedIds();
    data[String(postId)] = type;
    try {
      localStorage.setItem(BLOG_STORAGE_KEY, JSON.stringify(data));
    } catch (e) {}
  }

  function hasVoted(postId) {
    return !!getVotedIds()[String(postId)];
  }

  // Like / Dislike
  $(document).on('click', '.blog-like-btn, .blog-dislike-btn', function () {
    var $btn = $(this);
    var postId = $btn.data('post-id');
    var type = $btn.data('type');
    if (!$btn.length || !postId || !type || $btn.hasClass('blog-voted')) {
      return;
    }
    if (typeof blogAjax === 'undefined' || !blogAjax.ajaxurl || !blogAjax.nonce) {
      return;
    }
    if (hasVoted(postId)) {
      return;
    }
    $btn.addClass('blog-voted').prop('disabled', true);
    $.post(blogAjax.ajaxurl, {
      action: 'blog_like',
      nonce: blogAjax.nonce,
      post_id: postId,
      type: type
    })
      .done(function (res) {
        if (res.success && res.data && res.data.value !== undefined) {
          setVoted(postId, type);
          if (type === 'like') {
            $btn.find('.blog-like-count').text(res.data.value);
          } else {
            $btn.find('.blog-dislike-count').text(res.data.value);
          }
        }
      })
      .fail(function () {
        $btn.removeClass('blog-voted').prop('disabled', false);
      });
  });

  // Mark already voted on load
  $('.blog-like-btn, .blog-dislike-btn').each(function () {
    var postId = $(this).data('post-id');
    if (postId && hasVoted(postId)) {
      $(this).addClass('blog-voted').prop('disabled', true);
    }
  });

  // TOC smooth scroll
  $(document).on('click', '.blog-toc-link', function (e) {
    var href = $(this).attr('href');
    if (!href || href.indexOf('#') !== 0) return;
    var id = href.slice(1);
    var $target = document.getElementById(id);
    if ($target) {
      e.preventDefault();
      $target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
})(jQuery);
