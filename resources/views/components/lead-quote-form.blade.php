@php
    $uid = uniqid('lq_');
@endphp

<section id="{{ $anchorId }}" class="lead-quote-section">
    @if($showSidebar)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="row g-0">
                <div class="col-lg-5 text-white p-4 p-md-5 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #002ab0 0%, #0044e3 100%);">
                    <div>
                        <span class="badge bg-white text-dark-blue fw-semibold px-3 py-2 rounded-pill mb-3">
                            Tư vấn &amp; Báo giá trực tiếp DATYSO
                        </span>
                        <h2 class="h3 fw-bold text-white mb-3">{{ $title }}</h2>
                        <p class="mb-4 text-white-50" style="font-size: 0.98rem; line-height: 1.6;">
                            {{ $subtitle }}
                        </p>

                        <ul class="list-unstyled mb-0" style="font-size: 0.95rem;">
                            <li class="d-flex align-items-start gap-2 mb-2">
                                <span class="fw-bold text-warning">&#10003;</span>
                                <span>Tư vấn cấu hình chuẩn theo vật liệu, độ dày &amp; sản lượng xưởng.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-2">
                                <span class="fw-bold text-warning">&#10003;</span>
                                <span>Hỗ trợ test mẫu trực tiếp &amp; giao lắp đặt toàn quốc.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <span class="fw-bold text-warning">&#10003;</span>
                                <span>DATYSO sẽ gọi lại trong giờ làm việc.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-4 pt-3 border-top border-light border-opacity-25">
                        <div class="small text-white-50">Hotline kỹ thuật &amp; báo giá nhanh</div>
                        <a href="tel:0963535196" class="fs-4 fw-bold text-white text-decoration-none d-inline-block mt-1">
                            0963.535.196
                        </a>
                    </div>
                </div>

                <div class="col-lg-7 p-4 p-md-5 bg-white">
    @else
        <div>
    @endif

            @if($sourceName !== '')
                <div class="alert alert-primary border-0 bg-primary bg-opacity-10 text-dark-blue py-2 px-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="small mb-0">
                        <span class="fw-semibold">{{ $sourceType === 'category' ? 'Danh mục quan tâm:' : 'Máy đang xem:' }}</span>
                        <strong class="ms-1">{{ $sourceName }}</strong>
                    </div>
                    <span class="badge bg-primary">Đã chọn sẵn</span>
                </div>
            @endif

            @if(session('messageSuccess') && (old('source_type') === $sourceType || !old('source_type')))
                <div class="alert alert-success lead-form-alert-success" role="alert">
                    <strong>{{ session('messageSuccess') }}</strong>
                </div>
            @endif

            @if($errors->any() && (old('source_type') === $sourceType || !old('source_type')))
                <div class="alert alert-danger" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="alert alert-success d-none js-lead-success" role="status" aria-live="polite"></div>
            <div class="alert alert-danger d-none js-lead-error" role="alert" aria-live="assertive"></div>

            <form
                action="{{ route('contact.register', [], false) }}"
                method="POST"
                class="js-lead-quote-form"
                data-lead-form="1"
                data-recaptcha-site-key="{{ $recaptchaEnabled ? $recaptchaSiteKey : '' }}"
                novalidate
            >
                @csrf
                <input type="hidden" name="source_type" value="{{ $sourceType }}">
                <input type="hidden" name="product_id" value="{{ $productId }}">
                <input type="hidden" name="category_id" value="{{ $categoryId }}">
                <input type="hidden" name="source_name" value="{{ $sourceName }}">
                <input type="hidden" name="source_url" value="{{ $resolvedSourceUrl }}">
                <input type="hidden" name="_form_ts" value="{{ $formTs }}">
                <input type="hidden" name="_form_token" value="{{ $formToken }}">
                <input type="hidden" name="g_recaptcha_response" value="">

                {{-- Honeypot chống spam tự động điền --}}
                <div style="position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden;opacity:0;" aria-hidden="true">
                    <label for="{{ $uid }}-hp">Công ty / Fax</label>
                    <input type="text" id="{{ $uid }}-hp" name="hp_company_fax" value="" tabindex="-1" autocomplete="off">
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="{{ $uid }}-username" class="form-label fw-semibold mb-1">
                            Họ tên <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="{{ $uid }}-username"
                            name="username"
                            class="form-control @error('username') is-invalid @enderror"
                            placeholder="Nhập họ và tên"
                            value="{{ old('username') }}"
                            maxlength="255"
                            required
                        >
                        <div class="invalid-feedback js-field-error" data-field="username"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="{{ $uid }}-phone" class="form-label fw-semibold mb-1">
                            Số điện thoại <span class="text-danger">*</span>
                        </label>
                        <input
                            type="tel"
                            inputmode="tel"
                            id="{{ $uid }}-phone"
                            name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="VD: 0963535196"
                            value="{{ old('phone') }}"
                            maxlength="20"
                            required
                        >
                        <div class="invalid-feedback js-field-error" data-field="phone"></div>
                    </div>

                    <div class="{{ $showEmail ? 'col-md-6' : 'col-12' }}">
                        <label for="{{ $uid }}-province" class="form-label fw-semibold mb-1">
                            Tỉnh/thành
                        </label>
                        <select id="{{ $uid }}-province" name="province" class="form-select @error('province') is-invalid @enderror">
                            <option value="">-- Chọn Tỉnh / Thành phố --</option>
                            @foreach($provinces as $prov)
                                <option value="{{ $prov }}" {{ old('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                            @endforeach
                            <option value="Tỉnh/Thành phố khác" {{ old('province') === 'Tỉnh/Thành phố khác' ? 'selected' : '' }}>Tỉnh/Thành phố khác</option>
                        </select>
                        <div class="invalid-feedback js-field-error" data-field="province"></div>
                    </div>

                    @if($showEmail)
                        <div class="col-md-6">
                            <label for="{{ $uid }}-email" class="form-label fw-semibold mb-1">
                                Email <span class="text-muted fw-normal small">(không bắt buộc)</span>
                            </label>
                            <input
                                type="email"
                                id="{{ $uid }}-email"
                                name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email nhận báo giá (nếu có)"
                                value="{{ old('email') }}"
                                maxlength="150"
                            >
                            <div class="invalid-feedback js-field-error" data-field="email"></div>
                        </div>
                    @endif

                    <div class="col-12">
                        <label for="{{ $uid }}-demand" class="form-label fw-semibold mb-1">
                            Nhu cầu <span class="text-muted fw-normal small">(vật liệu, độ dày, sản lượng – không bắt buộc)</span>
                        </label>
                        <textarea
                            id="{{ $uid }}-demand"
                            name="demand"
                            rows="3"
                            class="form-control @error('demand') is-invalid @enderror"
                            placeholder="VD: Hàn/cắt inox 2mm, thép 5mm, sản lượng khoảng 300 sản phẩm/ngày..."
                            maxlength="2000"
                        >{{ old('demand') }}</textarea>
                        <div class="invalid-feedback js-field-error" data-field="demand"></div>
                    </div>

                    <div class="col-12 pt-1">
                        <button
                            type="submit"
                            class="btn btn-dark-blue w-100 py-2 px-4 fw-bold text-uppercase js-lead-submit"
                            style="min-height: 46px; font-size: 1rem;"
                        >
                            <span class="js-submit-label">Gửi Yêu Cầu Báo Giá</span>
                            <span class="spinner-border spinner-border-sm ms-2 d-none js-submit-spinner" role="status" aria-hidden="true"></span>
                        </button>
                        <div class="text-muted small text-center mt-2">
                            Thông tin được bảo mật. <strong>DATYSO sẽ gọi lại trong giờ làm việc.</strong>
                        </div>
                    </div>
                </div>
            </form>

    @if($showSidebar)
                </div>
            </div>
        </div>
    @else
        </div>
    @endif
</section>

@once
    @if($recaptchaEnabled)
        <script src="https://www.google.com/recaptcha/api.js?render={{ urlencode($recaptchaSiteKey) }}" async defer></script>
    @endif

    @if(session('leadSuccessData'))
        <script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push(@json(array_merge(['event' => 'generate_lead', 'currency' => 'VND'], session('leadSuccessData'))));
        </script>
    @endif

    <script>
        (function () {
            if (window.__datysoLeadFormInitialized) {
                return;
            }
            window.__datysoLeadFormInitialized = true;

            function clearFormErrors(form) {
                form.querySelectorAll('.is-invalid').forEach(function (el) {
                    el.classList.remove('is-invalid');
                });
                form.querySelectorAll('.js-field-error').forEach(function (el) {
                    el.textContent = '';
                });
            }

            function showBanner(section, selector, message) {
                var box = section ? section.querySelector(selector) : null;
                if (!box) return;
                if (message) {
                    box.textContent = message;
                    box.classList.remove('d-none');
                } else {
                    box.textContent = '';
                    box.classList.add('d-none');
                }
            }

            function pushGenerateLead(lead) {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({
                    event: 'generate_lead',
                    lead_source: (lead && lead.source_type) ? lead.source_type : 'contact',
                    item_name: (lead && lead.source_name) ? lead.source_name : '',
                    product_id: (lead && lead.product_id) ? lead.product_id : null,
                    category_id: (lead && lead.category_id) ? lead.category_id : null,
                    province: (lead && lead.province) ? lead.province : '',
                    page_location: (lead && lead.source_url) ? lead.source_url : window.location.href,
                    currency: 'VND',
                    value: Number((lead && lead.value) ? lead.value : 0)
                });
            }

            function getRecaptchaToken(siteKey) {
                return new Promise(function (resolve) {
                    if (!siteKey || !window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
                        resolve('');
                        return;
                    }
                    try {
                        window.grecaptcha.ready(function () {
                            window.grecaptcha.execute(siteKey, { action: 'generate_lead' })
                                .then(function (token) { resolve(token || ''); })
                                .catch(function () { resolve(''); });
                        });
                    } catch (err) {
                        resolve('');
                    }
                });
            }

            document.addEventListener('click', function (e) {
                var trigger = e.target.closest('a[href="#lead-quote-section"], .js-scroll-lead-form');
                if (!trigger) return;
                var target = document.getElementById('lead-quote-section');
                if (!target) return;

                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(function () {
                    var nameInput = target.querySelector('input[name="username"]');
                    if (nameInput) nameInput.focus();
                }, 400);
            });

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (!form || !form.matches('form.js-lead-quote-form')) {
                    return;
                }
                e.preventDefault();

                if (form.dataset.submitting === '1') {
                    return;
                }

                var section = form.closest('.lead-quote-section');
                var submitBtn = form.querySelector('.js-lead-submit');
                var submitLabel = form.querySelector('.js-submit-label');
                var spinner = form.querySelector('.js-submit-spinner');

                clearFormErrors(form);
                showBanner(section, '.js-lead-error', '');
                showBanner(section, '.js-lead-success', '');

                var usernameEl = form.querySelector('[name="username"]');
                var phoneEl = form.querySelector('[name="phone"]');
                var hasClientError = false;

                if (usernameEl && usernameEl.value.trim().length < 2) {
                    usernameEl.classList.add('is-invalid');
                    var errUser = form.querySelector('.js-field-error[data-field="username"]');
                    if (errUser) errUser.textContent = 'Vui lòng nhập họ tên.';
                    hasClientError = true;
                }
                if (phoneEl && phoneEl.value.trim() === '') {
                    phoneEl.classList.add('is-invalid');
                    var errPhone = form.querySelector('.js-field-error[data-field="phone"]');
                    if (errPhone) errPhone.textContent = 'Vui lòng nhập số điện thoại.';
                    hasClientError = true;
                }
                if (hasClientError) {
                    var firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) firstInvalid.focus();
                    return;
                }

                form.dataset.submitting = '1';
                if (submitBtn) submitBtn.disabled = true;
                if (submitLabel) submitLabel.textContent = 'Đang gửi...';
                if (spinner) spinner.classList.remove('d-none');

                var siteKey = form.dataset.recaptchaSiteKey || '';
                getRecaptchaToken(siteKey).then(function (recaptchaToken) {
                    var tokenInput = form.querySelector('input[name="g_recaptcha_response"]');
                    if (tokenInput) {
                        tokenInput.value = recaptchaToken || '';
                    }

                    var formData = new FormData(form);

                    return fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData,
                        credentials: 'same-origin'
                    });
                }).then(function (response) {
                    return response.text().then(function (text) {
                        var data = null;
                        try {
                            data = text ? JSON.parse(text) : {};
                        } catch (parseErr) {
                            data = null;
                        }
                        return { status: response.status, ok: response.ok, data: data };
                    });
                }).then(function (res) {
                    if (res.ok && res.data && res.data.status) {
                        var successMsg = res.data.message || 'Cảm ơn quý khách đã gửi yêu cầu! DATYSO sẽ gọi lại trong giờ làm việc.';
                        showBanner(section, '.js-lead-success', successMsg);

                        if (window.toastr && typeof window.toastr.success === 'function') {
                            window.toastr.success(successMsg);
                        }

                        if (!res.data.duplicate) {
                            pushGenerateLead(res.data.lead || {});
                        }

                        // Chỉ xóa các trường khách nhập, giữ nguyên trường ẩn sản phẩm/danh mục & token
                        if (usernameEl) usernameEl.value = '';
                        if (phoneEl) phoneEl.value = '';
                        var demandEl = form.querySelector('[name="demand"]');
                        if (demandEl) demandEl.value = '';
                        var emailEl = form.querySelector('[name="email"]');
                        if (emailEl) emailEl.value = '';
                        var provinceEl = form.querySelector('[name="province"]');
                        if (provinceEl) provinceEl.value = '';
                        return;
                    }

                    if (res.status === 419) {
                        showBanner(section, '.js-lead-error', 'Phiên làm việc đã hết hạn, vui lòng tải lại trang và gửi lại.');
                        return;
                    }

                    if (res.status === 429) {
                        showBanner(section, '.js-lead-error', 'Bạn thao tác quá nhanh, vui lòng thử lại sau 1 phút.');
                        return;
                    }

                    if (res.status === 422 && res.data && res.data.errors) {
                        Object.keys(res.data.errors).forEach(function (field) {
                            var msgs = res.data.errors[field];
                            var input = form.querySelector('[name="' + field + '"]');
                            var errEl = form.querySelector('.js-field-error[data-field="' + field + '"]');
                            if (input) input.classList.add('is-invalid');
                            if (errEl && msgs && msgs.length) errEl.textContent = msgs[0];
                        });
                        showBanner(section, '.js-lead-error', res.data.message || 'Vui lòng kiểm tra lại thông tin.');
                        return;
                    }

                    showBanner(section, '.js-lead-error', (res.data && res.data.message) ? res.data.message : 'Không thể gửi yêu cầu lúc này, vui lòng gọi Hotline 0963.535.196.');
                }).catch(function () {
                    showBanner(section, '.js-lead-error', 'Lỗi kết nối mạng, vui lòng thử lại hoặc gọi Hotline 0963.535.196.');
                }).finally(function () {
                    form.dataset.submitting = '0';
                    if (submitBtn) submitBtn.disabled = false;
                    if (submitLabel) submitLabel.textContent = 'Gửi Yêu Cầu Báo Giá';
                    if (spinner) spinner.classList.add('d-none');
                });
            });
        })();
    </script>
@endonce
