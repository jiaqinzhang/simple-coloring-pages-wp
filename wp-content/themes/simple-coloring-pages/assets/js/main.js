document.addEventListener('DOMContentLoaded', function () {
	// Mobile nav toggle
	var mobileBtn = document.querySelector('.mobile-nav-btn');
	var mainNav = document.querySelector('.main-nav');
	if (mobileBtn && mainNav) {
		mobileBtn.addEventListener('click', function () {
			mainNav.classList.toggle('is-open');
			mainNav.style.display = mainNav.classList.contains('is-open') ? 'flex' : '';
		});
	}

	// Page preview modal (single topic template)
	document.querySelectorAll('[data-open-preview]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var modal = document.getElementById('scp-preview-modal');
			if (!modal) return;
			modal.querySelector('[data-modal-title]').textContent = btn.dataset.title || '';
			modal.querySelector('[data-modal-img]').src = btn.dataset.png || '';
			modal.querySelector('[data-modal-download]').href = btn.dataset.pdf || '#';
			modal.querySelector('[data-modal-download-png]').href = btn.dataset.png || '#';
			modal.style.display = 'flex';
		});
	});
	document.querySelectorAll('[data-close-preview]').forEach(function (el) {
		el.addEventListener('click', function () {
			var modal = document.getElementById('scp-preview-modal');
			if (modal) modal.style.display = 'none';
		});
	});
	var modalCard = document.querySelector('#scp-preview-modal [data-modal-card]');
	if (modalCard) {
		modalCard.addEventListener('click', function (e) { e.stopPropagation(); });
	}
});
