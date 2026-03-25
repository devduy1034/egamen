/* Validation form */
validateForm('validation-contact');
validateForm('validation-newsletter');
// validateForm('validation-cart');
// validateForm('validation-user');

/* Load name input file */
NN_FRAMEWORK.loadNameInputFile = function () {
	if (isExist($('.custom-file input[type=file]'))) {
		$('body').on('change', '.custom-file input[type=file]', function () {
			var fileName = $(this).val();
			fileName = fileName.substr(fileName.lastIndexOf('\\') + 1, fileName.length);
			$(this).siblings('label').html(fileName);
		});
	}
};

/* Back to top */
NN_FRAMEWORK.GoTop = function () {
	$(window).scroll(function () {
		if (!$('.scrollToTop').length)
			$('body').append('<div class="scrollToTop"><img src="' + GOTOP + '" alt="Go Top"/></div>');
		if ($(this).scrollTop() > 100) $('.scrollToTop').fadeIn();
		else $('.scrollToTop').fadeOut();
	});

	$('body').on('click', '.scrollToTop', function () {
		$('html, body').animate({ scrollTop: 0 }, 800);
		return false;
	});
};

/* Menu */
NN_FRAMEWORK.Menu = function () {
	if ($('.navigation').length) {
		let navHeight = $('.navigation').outerHeight();
		function checkScrollDirection(callback) {
			let lastScrollTop = 0;
			$(window).on('scroll', function () {
				let currentScroll = $(this).scrollTop();
				if (currentScroll > lastScrollTop) {
					callback('down');
				} else {
					callback('up');
				}
				lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
			});
		}
		function detectStatusOfNavigation(event) {
			const OFFSET_TOP = 60,
				MIN_COMPUTER_VIEWPORT = 1025;
			const eventType = event.type,
				fixedOnLoad = !$('.above-nav').length ||
					$('.above-nav')[0].clientHeight === 0 ||
					($('.below-nav-rps').length && $('body').width() > MIN_COMPUTER_VIEWPORT),
				scrolledPassAbove = $(window).scrollTop() >= (fixedOnLoad ? OFFSET_TOP : $('.above-nav').outerHeight());
			const isBottom = window.innerHeight + window.pageYOffset >= document.body.offsetHeight,
				isTop = window.pageYOffset === 0,
				isMobile = $('body').width() < MIN_COMPUTER_VIEWPORT,
				isIndex = isExist($('.slideshow')) ? true : false;

			$('.navigation').toggleClass('is-bottom', isBottom);
			$('.navigation').toggleClass('is-top', isTop);
			$('.navigation').toggleClass('is-mobile', isMobile);
			$('.navigation').toggleClass('is-desktop', !isMobile);

			if (eventType !== 'scroll') {
				$('.navigation').toggleClass('is-index', isIndex);
				$('.navigation').toggleClass('not-index', !isIndex);
			}
			if (fixedOnLoad) {
				$('.navigation').addClass('is-fixed');
				$('.navigation').toggleClass('was-scrolled', scrolledPassAbove);
			} else {
				$('.navigation').toggleClass('is-fixed was-scrolled', scrolledPassAbove);
			}
			if (fixedOnLoad) {
				if (!$('.below-nav-rps').length || (($('.below-nav-rps').length || $('.above-nav')[0].clientHeight > 0) && isMobile)) {
					$('.below-nav').css({
						marginTop: navHeight,
					});
				} else {
					$('.below-nav').css({
						marginTop: 0,
					});
				}
			} else {
				$('.below-nav').css({
					marginTop: scrolledPassAbove ? navHeight : 0,
				});
			}
			if (isMobile && !$('.nav-menu ul').length) {
				$('.nav-menu').html($('.menu ul.ulmn').clone(true));
			} else if (!isMobile) {
				$('.nav-menu').empty();
			}
		}

		checkScrollDirection(function (direction) {
			$('.navigation')[direction === 'down' ? 'addClass' : 'removeClass']('scrolling-down')[direction === 'up' ? 'addClass' : 'removeClass']('scrolling-up');
		});
		$(window).bind('load resize', function (event) {
			detectStatusOfNavigation(event);
		}).scroll(function (event) {
			detectStatusOfNavigation(event);
		});

		if ($('.menu-mobile-btn').length) {
			$('body').on('click', 'span.btn-dropdown-menu', function () {
				var o = $(this);
				if (!o.hasClass('active')) {
					o.addClass('active');
					o.next('.sub-menu').stop().slideDown(300);
				} else {
					o.removeClass('active');
					o.next('.sub-menu').stop().slideUp(300);
				}
			});
			$('.menu-mobile-btn').click(function (e) {
				e.preventDefault();
				e.stopPropagation();
				$('.header-left-fixwidth').toggleClass('open-sidebar-menu');
				$('.opacity-menu').toggleClass('open-opacity');

				$('body').toggleClass('no-scroll', $('.opacity-menu').hasClass('open-opacity'));
			});
			$('.opacity-menu').click(function (e) {
				$('.header-left-fixwidth').removeClass('open-sidebar-menu');
				$('.opacity-menu').removeClass('open-opacity');

				$('body').removeClass('no-scroll');
			});
		}
	}
};

/* Tools */
NN_FRAMEWORK.Tools = function () {
	if (isExist($('.toolbar'))) {
		$('.footer').css({ marginBottom: $('.toolbar').innerHeight() });
	}
};

/* Popup */
NN_FRAMEWORK.Popup = function () {
	if (isExist($('#popup'))) {
		validateForm('validation-popup');
		$('#popup').modal('show');
	}
};

/* Wow */
NN_FRAMEWORK.Wows = function () {
	new WOW().init();
};

/* Search */
NN_FRAMEWORK.Search = function () {
	if (isExist($('.search-toggle'))) {
		var closeNavigationSearch = function () {
			$('.navigation.search-open').removeClass('search-open');
			$('.search-toggle i').removeClass('bi-x-lg').addClass('bi-search');
			$('.navigation-search-panel .show-search').hide();
		};

		$('.search-toggle').click(function (e) {
			e.preventDefault();
			var nav = $(this).closest('.navigation');
			var icon = $(this).find('i');
			var isOpen = nav.hasClass('search-open');

			closeNavigationSearch();

			if (!isOpen) {
				nav.addClass('search-open');
				icon.removeClass('bi-search').addClass('bi-x-lg');
				$('#keyword-navigation').trigger('focus');
			}
		});

		$(document).on('click', function (e) {
			if (!$(e.target).closest('.navigation-search-panel, .search-toggle').length) {
				closeNavigationSearch();
			}
		});

		$(document).on('keydown', function (e) {
			if (e.key === 'Escape') {
				closeNavigationSearch();
			}
		});
	}

	if (isExist($('.icon-search'))) {
		$('.icon-search').click(function () {
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
				$('.search-grid').stop(true, true).animate({ opacity: '0', width: '0px' }, 200);
			} else {
				$(this).addClass('active');
				$('.search-grid').stop(true, true).animate({ opacity: '1', width: '230px' }, 200);
			}
			document.getElementById($(this).next().find('input').attr('id')).focus();
			$('.icon-search i').toggleClass('bi bi-x-lg');
		});
	}

	if (isExist($('.search-auto'))) {
		var searchTimer = null;
		$('.show-search').hide().empty();
		$('.search-auto').on('input', function () {
			var $this = $(this);
			var keyword = $.trim($this.val());
			var $resultBox = $this.closest('.navigation-search-panel__inner').find('.show-search');

			if (searchTimer) clearTimeout(searchTimer);
			if (keyword.length < 2) {
				$resultBox.hide().empty();
				return;
			}

			searchTimer = setTimeout(function () {
				$.get('tim-kiem-goi-y', { keyword: keyword }, function (data) {
					if ($.trim(data) !== '') {
						$resultBox.html(data).show();
					} else {
						$resultBox.hide().empty();
					}
				});
			}, 250);
		});

		$(document).on('click', function (e) {
			if (!$(e.target).closest('.navigation-search-form, .show-search').length) {
				$('.show-search').hide();
			}
		});
	}

	if (isExist($('.icon-search-menu'))) {
		$('.icon-search-menu').click(function () {
			if ($(this).hasClass('active')) {
				$(this).removeClass('active');
				$('.search-grid').stop(true, true).animate({ opacity: '0', width: '0px' }, 200);
			} else {
				$(this).addClass('active');
				$('.search-grid').stop(true, true).animate({ opacity: '1', width: '230px' }, 200);
			}
			document.getElementById($(this).next().find('input').attr('id')).focus();
			$('.icon-search-menu i').toggleClass('fa-xmark');
		});
	}
};

/* Password toggle */
NN_FRAMEWORK.PasswordToggle = function () {
	if (isExist($('.js-toggle-password'))) {
		$('.js-toggle-password').off('click').on('click', function () {
			var target = $(this).attr('data-target');
			var input = $(target);
			if (!input.length) return;

			var isPassword = input.attr('type') === 'password';
			input.attr('type', isPassword ? 'text' : 'password');
			$(this).find('i').toggleClass('bi-eye bi-eye-slash');
		});
	}
};

/* Videos */
NN_FRAMEWORK.Videos = function () {
	Fancybox.bind('[data-fancybox]', {});
};

/* Dom Change */
NN_FRAMEWORK.DomChange = function () {
	/* Video Fotorama */
	if (isExist($('#fotorama-videos'))) {
		$('#fotorama-videos').fotorama();
	}
	/* Video Select */
	if (isExist($('.list-video'))) {
		$('.list-video').change(function () {
			var id = $(this).val();
			$.ajax({
				url: 'load-video',
				type: 'GET',
				dataType: 'html',
				data: {
					id: id
				},
				beforeSend: function () {
					holdonOpen();
				},
				success: function (result) {
					$('.video-main').html(result);
					holdonClose();
				}
			});
		});
	}

	/* Chat Facebook */
	$('#messages-facebook').one('DOMSubtreeModified', function () {
		$('.js-facebook-messenger-box').on('click', function () {
			$('.js-facebook-messenger-box, .js-facebook-messenger-container').toggleClass('open'),
				$('.js-facebook-messenger-tooltip').length && $('.js-facebook-messenger-tooltip').toggle();
		}),
			$('.js-facebook-messenger-box').hasClass('cfm') &&
			setTimeout(function () {
				$('.js-facebook-messenger-box').addClass('rubberBand animated');
			}, 3500),
			$('.js-facebook-messenger-tooltip').length &&
			($('.js-facebook-messenger-tooltip').hasClass('fixed')
				? $('.js-facebook-messenger-tooltip').show()
				: $('.js-facebook-messenger-box').on('hover', function () {
					$('.js-facebook-messenger-tooltip').show();
				}),
				$('.js-facebook-messenger-close-tooltip').on('click', function () {
					$('.js-facebook-messenger-tooltip').addClass('closed');
				}));
		$('.search_open').click(function () {
			$('.search_box_hide').toggleClass('opening');
		});
	});
};

NN_FRAMEWORK.SwiperData = function (obj) {
	if (!isExist(obj)) return false;
	var name = obj.attr('data-swiper-name') || 'swiper';
	var thumbs = obj.attr('data-swiper-thumbs');
	var more = obj.attr('data-swiper');

	if (more && more.search('|') >= 0) {
		more = more.split('|');
		var on = more.reduce((a, b) => {
			if (b.search('{') < 0) {
				var c = {
					[b.split(':')[0]]: useStrict(b.split(':')[1])
				};
			} else {
				const b1 = String(b.split(':', 1));
				const b2 = useStrict(b.slice(String(b.split(':', 1)).length + 1).trim());
				var c = {
					[b1]: b2
				};
			}
			return Object.assign({}, a, c);
		}, {});
	} else {
		on = '';
	}
	if (thumbs) {
		on.thumbs = { swiper: window[thumbs] };
	}

	window[name] = new Swiper(obj[0], on);

	if (window[name].passedParams.breakpoints) {
		if (window[name].params.direction == 'vertical') {
			const entries = Object.entries(window[name].passedParams.breakpoints);
			function setHeight() {
				var height = obj.find('.item').outerHeight();
				var countItem = obj.find('.item').length;
				entries.forEach((v) => {
					var Breakpoint = v[0] > 0 ? v[0] : 0;
					if (Breakpoint > 0 && Breakpoint == window[name].currentBreakpoint) {
						var items = v[1].slidesPerView != undefined ? v[1].slidesPerView : 0;
						var margin = v[1].spaceBetween != undefined ? v[1].spaceBetween : 0;
					} else if (window[name].currentBreakpoint == 'max') {
						var items = window[name].passedParams.slidesPerView;
						var margin = window[name].passedParams.spaceBetween;
					}
					if (window[name].params.direction == 'vertical') {
						if (countItem < items) {
							obj.css({
								height: height * countItem + (countItem - 1) * margin
							});
							obj.find('.swiper-slide').addClass('h-auto');
						} else {
							obj.css({
								height: height * items + (items - 1) * margin
							});
						}
						window[name].update();
					} else {
						obj.css({ height: '' });
						window[name].update();
					}
				});
			}
			setHeight();
			obj.find('img').each(function () {
				if (!this.complete) {
					$(this).one('load error', function () {
						setHeight();
					});
				}
			});
			$(window).on('resize', setHeight);
			window[name].on('imagesReady', setHeight);
			window[name].on('breakpoint', setHeight);
			setTimeout(setHeight, 120);
		}
	} else {
		if (window[name].params.direction == 'vertical') {
			function setHeight() {
				var height = obj.find('.item').outerHeight();
				var countItem = obj.find('.item').length;
				var items = window[name].passedParams.slidesPerView;
				var margin = window[name].passedParams.spaceBetween;
				if (countItem < items) {
					obj.css({
						height: height * countItem + (countItem - 1) * margin
					});
					obj.find('.swiper-slide').addClass('h-auto');
				} else {
					obj.css({ height: height * items + (items - 1) * margin });
				}
				window[name].update();
			}
			setHeight();
			obj.find('img').each(function () {
				if (!this.complete) {
					$(this).one('load error', function () {
						setHeight();
					});
				}
			});
			$(window).on('resize', setHeight);
			window[name].on('imagesReady', setHeight);
			window[name].on('breakpoint', setHeight);
			setTimeout(setHeight, 120);
		}
	}
	return window[name];
};

/* Swiper */
NN_FRAMEWORK.Swiper = function () {
	if (isExist($('.swiper-auto'))) {
		$('.swiper-auto[data-swiper-name]').each(function () {
			NN_FRAMEWORK.SwiperData($(this));
		});
		$('.swiper-auto:not([data-swiper-name])').each(function () {
			NN_FRAMEWORK.SwiperData($(this));
		});
	}
};

NN_FRAMEWORK.Api = function () {
	const observeNearViewport = function (element, callback, options = {}) {
		if (!element || typeof callback !== 'function') return;

		const rootMargin = options.rootMargin || '300px 0px';
		const once = options.once !== false;

		if (!('IntersectionObserver' in window)) {
			callback(element);
			return;
		}

		const observer = new IntersectionObserver(function (entries, currentObserver) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;

				callback(entry.target);

				if (once) {
					currentObserver.unobserve(entry.target);
				}
			});
		}, {
			rootMargin: rootMargin
		});

		observer.observe(element);
	};

	if (isExist($('.click-product'))) {
		$('.click-product span').click(function (e) {
			var thisClass = $(this).closest('.other-product');
			thisClass.find('.click-product span').removeClass('active');
			$(this).addClass('active');
			var url = $(this).data('url');
			var type = $(this).data('type');
			var template = $(this).data('template');
			var id_list = $(this).data('list');
			var id_cat = $(this).data('cat');
			var id_item = $(this).data('item');
			var status = $(this).data('status');
			var paginate = $(this).data('paginate');
			var other = $(this).data('other');
			var section = $(this).data('section');
			var slug = $(this).data('slug');
			var eshow = $(this).data('eshow');
			$.ajax({
				url: url,
				type: 'GET',
				data: {
					type: type,
					status: status,
					id_list: id_list,
					id_cat: id_cat,
					id_item: id_item,
					template: template,
					other: other,
					section: section,
					slug: slug,
					paginate: paginate,
					eShow: eshow
				},
				success: function (result) {
					thisClass.find(eshow).html(result);
					NN_FRAMEWORK.Swiper();
					NN_FRAMEWORK.Img();
					NN_FRAMEWORK.ProductCard();
				}
			});
		});
		$('.click-product').each(function () {
			var triggerContainer = $(this);
			var observeTarget = triggerContainer.closest('.list-product').get(0) || triggerContainer.get(0);

			observeNearViewport(observeTarget, function () {
				if (triggerContainer.data('lazy-loaded')) return;
				triggerContainer.data('lazy-loaded', true);
				triggerContainer.find('span').first().trigger('click');
			});
		});
		/* loc san phẩm */
		$('.sort-select-main span').on('click', function () {
			var sort = $(this).data('sort');
			$('.sort-select-main span').removeClass('active');
			$(this).addClass('active');

			var activeProduct = $('.click-product span.active');
			var url = activeProduct.data('url');
			var id_list = activeProduct.data('list');
			var slug = activeProduct.data('slug');
			var paginate = activeProduct.data('paginate');
			var other = activeProduct.data('other');
			var section = activeProduct.data('section');
			var eshow = activeProduct.data('eshow');

			$.ajax({
				url: url,
				type: 'GET',
				data: {
					id_list: id_list,
					slug: slug,
					paginate: paginate,
					other: other,
					section: section,
					eShow: eshow,
					sort: sort
				},
				success: function (result) {
					$(eshow).html(result);
				},
				error: function () {
					alert('Đã xảy ra lỗi, vui lòng thử lại.');
				}
			});
		});
	}
	if (isExist($('.load-home'))) {
		$('.load-home').each(function () {
			var thisClass = $(this);
			observeNearViewport(thisClass.get(0), function () {
				if (thisClass.data('lazy-loaded')) return;
				thisClass.data('lazy-loaded', true);

				var url = thisClass.data('url');
				var type = thisClass.data('type');
				var paginate = thisClass.data('paginate');
				var template = thisClass.data('template');
				var other = thisClass.data('other');
				var slug = thisClass.data('slug');
				var status = thisClass.data('status');
				var id_list = thisClass.data('list');
				var id_cat = thisClass.data('cat');
				var id_item = thisClass.data('item');
				var section = thisClass.data('section');
				var eshow = thisClass.data('eshow');
				$.ajax({
					url: url,
					type: 'GET',
					data: {
						type: type,
						status: status,
						id_list: id_list,
						id_cat: id_cat,
						id_item: id_item,
						template: template,
						other: other,
						section: section,
						slug: slug,
						paginate: paginate,
						eShow: eshow
					},
					success: function (result) {
						thisClass.find(eshow).html(result);
						NN_FRAMEWORK.Swiper();
						NN_FRAMEWORK.Img();
						NN_FRAMEWORK.ProductCard();
					}
				});
			});
		});
	}
	$('body').on('click', '#load-more', function () {
		var page = $(this).data('page');
		var section = $(this).data('section');
		var button = $(this);
		var parentContainer;

		if (section === 'home') {
			parentContainer = button.closest('.load-product-home');
		} else if (section === 'list') {
			parentContainer = button.closest('.product-list').find('span.active');
		}
		else if (section === 'cat') {
			parentContainer = button.closest('.list-product').find('span.active');
		}

		if (parentContainer && parentContainer.length) {
			var url = parentContainer.data('url');
			var type = parentContainer.data('type');
			var paginate = parentContainer.data('paginate');
			var other = parentContainer.data('other');
			var id_list = parentContainer.data('list');
			var id_cat = parentContainer.data('cat');
			var template = parentContainer.data('template');
			var eshow = parentContainer.data('eshow');
			$.ajax({
				url: url,
				type: 'GET',
				data: { type, other, paginate, template, id_list, id_cat, page, eshow, section },
				success: function (response) {
					handleAjaxSuccess(response, button, page, section, eshow);
				}
			});
		}
	});

	function handleAjaxSuccess(response, button, page, section, eshow) {
		if (response.trim()) {
			var newProducts = $(response).find('.row').html();
			if (newProducts) {
				button.parent('.col-12.button').remove();
				$(eshow).find('#product-list-' + section + ' .row').append(newProducts);
				button.data('page', page + 1);
			}
		} else {
			button.remove();
		}
	}

	if (isExist($('.item-search'))) {
		$('.item-search input').click(function () {
			Filter();
		});
	}

	if (isExist($('.sort-select-main'))) {
		$('.sort-select-main p a').click(function () {
			$('.sort-select-main p a').removeClass('check');
			$(this).addClass('check');
			Filter();
		});
	}

	$('.filter').click(function (e) {
		$('.left-product').toggleClass('show');
	});
	TextSort();
};

NN_FRAMEWORK.Properties = function () {
	if (isExist($('.grid-properties'))) {
		$('.properties').click(function (e) {
			$(this).parents('.grid-properties').find('.properties').removeClass('active');
			// $('.properties').removeClass('outstock');
			$(this).addClass('active');
		});
	}
};

NN_FRAMEWORK.ProductCard = function () {
	if (isExist($('.product-colors'))) {
		$('body').on('click', '.product-color', function () {
			var button = $(this);
			var container = button.closest('.product');
			var mainImg = container.find('.pic-product img.product-main-img');
			var mainSrc = button.data('image');
			var cacheBuster = 'v=' + Date.now();
			var finalSrc = mainSrc ? (mainSrc + (mainSrc.indexOf('?') >= 0 ? '&' : '?') + cacheBuster) : '';

			if (mainImg.length && finalSrc) {
				mainImg.attr('src', finalSrc);
				if (mainImg.attr('srcset')) {
					mainImg.attr('srcset', finalSrc);
				}
				if (mainImg.attr('data-src')) {
					mainImg.attr('data-src', finalSrc);
				}
				var picture = mainImg.closest('picture');
				if (picture.length) {
					picture.find('source').attr('srcset', finalSrc);
				}
			}

			button.closest('.product-colors').find('.product-color').removeClass('active');
			button.addClass('active');
		});
	}
};

NN_FRAMEWORK.QuickView = function () {
	var modalId = 'quickview-product-modal';

	function getModalHtml() {
		return `
		<div class="quickview-modal" id="${modalId}" aria-hidden="true">
			<div class="quickview-dialog" role="dialog" aria-modal="true">
				<button type="button" class="quickview-close" aria-label="Close">×</button>
				<div class="quickview-body">
					<div class="quickview-loading">Đang tải...</div>
				</div>
			</div>
		</div>`;
	}

	function ensureModal() {
		var modal = document.getElementById(modalId);
		if (!modal) {
			document.body.insertAdjacentHTML('beforeend', getModalHtml());
			modal = document.getElementById(modalId);
		}
		return modal;
	}

	function closeModal() {
		var modal = document.getElementById(modalId);
		if (!modal) return;
		modal.classList.remove('is-open');
		document.body.classList.remove('quickview-open');
	}

	function openModal() {
		var modal = ensureModal();
		modal.classList.add('is-open');
		document.body.classList.add('quickview-open');
	}

	function setModalContent(html) {
		var modal = ensureModal();
		var body = modal.querySelector('.quickview-body');
		if (!body) return;
		body.innerHTML = html;
	}

	function setLoading() {
		setModalContent('<div class="quickview-loading">Đang tải...</div>');
	}

	function setError() {
		setModalContent('<div class="quickview-error">Không tải được nội dung sản phẩm.</div>');
	}

	function simplifyQuickViewGrid(grid, detailUrl) {
		if (!grid) return '';

		// Keep thumbnails data for image switching, but hide the left thumb column in UI.
		var thumbs = grid.querySelector('.product-detail-thumbs');
		if (thumbs) thumbs.classList.add('quickview-thumbs-hidden');

		// Disable click-to-zoom in quick view.
		var mainZoomAnchor = grid.querySelector('#Zoom-1');
		if (mainZoomAnchor) {
			mainZoomAnchor.classList.remove('MagicZoom');
			mainZoomAnchor.classList.add('quickview-disable-zoom');
			mainZoomAnchor.setAttribute('href', 'javascript:void(0)');
			mainZoomAnchor.removeAttribute('data-options');
		}

		// Add detail page button on the right column.
		var rightCol = grid.querySelector('.right-pro-detail');
		if (rightCol && detailUrl && !rightCol.querySelector('.quickview-detail-link-wrap')) {
			var wrap = document.createElement('div');
			wrap.className = 'quickview-detail-link-wrap';
			wrap.innerHTML =
				'<a class="quickview-detail-link" href="' + detailUrl + '">' +
				'Xem chi tiet' +
				'</a>';
			rightCol.appendChild(wrap);
		}

		return '<div class="grid-pro-detail product-detail-v2 quickview-grid">' + grid.innerHTML + '</div>';
	}

	function extractGridDetail(htmlText, detailUrl) {
		var parser = new DOMParser();
		var doc = parser.parseFromString(htmlText, 'text/html');
		var grid = doc.querySelector('.grid-pro-detail');
		if (!grid) return '';
		return simplifyQuickViewGrid(grid, detailUrl);
	}

	$('body').on('click', '.js-quick-view', function (e) {
		e.preventDefault();
		var url = $(this).data('url') || $(this).attr('href');
		if (!url) return;

		openModal();
		setLoading();

		fetch(url, {
			method: 'GET',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
			.then(function (response) {
				if (!response.ok) throw new Error('Failed');
				return response.text();
			})
			.then(function (htmlText) {
				var gridHtml = extractGridDetail(htmlText, url);
				if (!gridHtml) {
					setError();
					return;
				}
				setModalContent(gridHtml);
				NN_FRAMEWORK.Swiper();
				NN_FRAMEWORK.ProductCard();
				NN_FRAMEWORK.Properties();
			})
			.catch(function () {
				setError();
			});
	});

	$('body').on('click', '#' + modalId + ' .quickview-close', function () {
		closeModal();
	});

	$('body').on('click', '#' + modalId, function (e) {
		if (e.target.id === modalId) closeModal();
	});

	$(document).on('keydown', function (e) {
		if (e.key === 'Escape') closeModal();
	});
};

NN_FRAMEWORK.Main = function () {
	var imgElements = document.querySelectorAll('img');
	imgElements.forEach(function (img) {
		if (!img.hasAttribute('alt')) {
			img.alt = WEBSITE_NAME;
		}
	});
	var anchorElements = document.querySelectorAll('a');
	anchorElements.forEach(function (anchor) {
		if (!anchor.hasAttribute('aria-label')) {
			anchor.setAttribute('aria-label', WEBSITE_NAME);
		}
	});

	$('.tt-toc').click(function (e) {
		$('.box-readmore ul').slideToggle();
	});
	$('.top-banner .close').click(() => {
		$('.top-banner').slideToggle()
		sessionStorage.setItem("top-banner", true)
	})

};

NN_FRAMEWORK.Img = function () {
	const images = document.querySelectorAll('img');
	images.forEach((img) => {
		const handleImageLoad = () => {
			const width = img.clientWidth;
			const height = img.clientHeight;
			const hw = img.getAttribute('width');
			if (width > 0 && height > 0 && !hw) {
				img.setAttribute('width', width);
				img.setAttribute('height', height);
			}
		};
		img.addEventListener('load', handleImageLoad);
		if (img.complete) {
			handleImageLoad();
		}
	});
};

/* Lazy Background Loading */
NN_FRAMEWORK.LazyBackgroundLoading = function () {
	if (isExist($('.lazy-background'))) {
		if ('IntersectionObserver' in window) {
			function handleIntersection(entries) {
				entries.map((entry) => {
					const bgImage = entry.target.dataset.bgImage,
						bgOptions = entry.target.dataset.bgOptions ? entry.target.dataset.bgOptions : '';

					if (entry.isIntersecting) {
						entry.target.style[bgOptions ? 'background' : 'backgroundImage'] = "url('" + bgImage + "') " + bgOptions;
						observer.unobserve(entry.target);
					}
				});
			}

			const elements = document.querySelectorAll('.lazy-background');
			const observer = new IntersectionObserver(handleIntersection, {
				rootMargin: '100px',
			});
			elements.forEach((element) => observer.observe(element));
		} else {
			const elements = document.querySelectorAll('.lazy-background');
			elements.forEach((element) => {
				const bgImage = element.dataset.bgImage,
					bgOptions = element.dataset.bgOptions ? element.dataset.bgOptions : '';

				element.style[bgOptions ? 'background' : 'backgroundImage'] = "url('" + bgImage + "') " + bgOptions;
			});
		}
	}
};

NN_FRAMEWORK.VoucherHome = function () {
	function fallbackCopy(value) {
		var input = document.createElement('textarea');
		input.value = value;
		input.style.position = 'fixed';
		input.style.left = '-9999px';
		document.body.appendChild(input);
		input.focus();
		input.select();
		try {
			document.execCommand('copy');
		} catch (e) {}
		document.body.removeChild(input);
	}

	function copyCode(value) {
		if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
			return navigator.clipboard.writeText(value);
		}
		fallbackCopy(value);
		return Promise.resolve();
	}

	$(document).off('click.voucherHome').on('click.voucherHome', '.js-copy-voucher-home', function (event) {
		event.preventDefault();
		var code = ($(this).data('code') || '').toString().trim();
		if (!code) return;

		copyCode(code)
			.then(function () {
				if (typeof showNotify === 'function') {
					showNotify('\u0110\u00e3 sao ch\u00e9p m\u00e3: ' + code, 'Th\u00f4ng b\u00e1o', 'success');
				}
			})
			.catch(function () {
				if (typeof showNotify === 'function') {
					showNotify('Kh\u00f4ng th\u1ec3 sao ch\u00e9p m\u00e3. Vui l\u00f2ng th\u1eed l\u1ea1i.', 'Th\u00f4ng b\u00e1o', 'warning');
				}
			});
	});
};
/* Ready */
$(document).ready(function () {
	NN_FRAMEWORK.Api();
	NN_FRAMEWORK.Popup();
	NN_FRAMEWORK.Swiper();
	NN_FRAMEWORK.GoTop();
	NN_FRAMEWORK.LazyBackgroundLoading();
	NN_FRAMEWORK.Menu();
	NN_FRAMEWORK.Videos();
	NN_FRAMEWORK.Search();
	NN_FRAMEWORK.PasswordToggle();
	NN_FRAMEWORK.DomChange();
	NN_FRAMEWORK.loadNameInputFile();
	NN_FRAMEWORK.Properties();
	NN_FRAMEWORK.ProductCard();
	NN_FRAMEWORK.QuickView();
	NN_FRAMEWORK.VoucherHome();
	NN_FRAMEWORK.Main();
	if (isExist($('.comment-page'))) {
		new Comments('.comment-page', BASE);
	}
	new Cart(BASE);
});

window.addEventListener('load', () => {
	NN_FRAMEWORK.Img();
});
