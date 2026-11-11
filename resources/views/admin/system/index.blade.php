@section('headerTitle', 'Cấu hình hệ thống')
@extends('admin.layout')
@section('templateContent')
@php
    $url = route('admin.system.store');
@endphp
<div class="wrapper wrapper-content animated fadeInRight">
    @include('admin.core.alert')

    @isset($mailDiagnostics)
        <div class="mx-3 mt-3 alert {{ $mailDiagnostics['ready'] ? 'alert-success' : 'alert-warning' }} shadow-sm" role="alert">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <div class="fw-bold mb-1">
                        @if($mailDiagnostics['ready'])
                            &#10003; Trạng thái gửi Email Lead: Sẵn sàng gửi về Sales
                        @else
                            &#9888; Cảnh báo gửi Email Lead: Chưa sẵn sàng gửi ra hộp thư Sales
                        @endif
                    </div>
                    <div class="small">
                        <strong>Email Sales nhận lead:</strong>
                        {{ !empty($mailDiagnostics['sales_emails']) ? implode(', ', $mailDiagnostics['sales_emails']) : 'Chưa cấu hình' }}
                        &nbsp;|&nbsp;
                        <strong>Phương thức hiệu lực:</strong> <code>{{ $mailDiagnostics['effective_mailer'] }}</code>
                        @if($mailDiagnostics['effective_mailer'] === 'smtp' && !empty($mailDiagnostics['host']))
                            (<code>{{ $mailDiagnostics['host'] }}:{{ $mailDiagnostics['port'] }}</code>)
                        @endif
                        &nbsp;|&nbsp;
                        <strong>From Email:</strong> <code>{{ $mailDiagnostics['from_address'] }}</code>
                    </div>
                    @if(!empty($mailDiagnostics['issues']))
                        <ul class="mb-0 mt-2 ps-3 small text-danger fw-semibold">
                            @foreach($mailDiagnostics['issues'] as $issue)
                                <li>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    @endisset
</div>

<form action="{{ $url }}" method="post" class="box" id="admin-system-form">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight mx-3">
        @foreach($systemConfig as $key => $val)
        <div class="row bg-white p-3 rounded shadow-sm mb-3">
            <div class="col-lg-5">
                <div class="panel-head">
                    <div class="panel-title fw-bold fs-5 mb-2">{{ $val['label'] }}</div>
                    <div class="panel-description text-muted">
                        {{ $val['description'] }}
                    </div>

                    @if($key === 'mail')
                        <div class="mt-3 p-3 bg-light border rounded small">
                            <div class="fw-bold mb-2 text-dark">Mẫu điền nhanh máy chủ gửi Email:</div>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-primary js-mail-preset"
                                    data-mailer="smtp" data-host="mail.datyso.com" data-port="465" data-enc="ssl">
                                    Mail Hosting cPanel (SSL 465)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary js-mail-preset"
                                    data-mailer="smtp" data-host="smtp.gmail.com" data-port="587" data-enc="tls">
                                    Gmail SMTP (TLS 587)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary js-mail-preset"
                                    data-mailer="smtp" data-host="smtp.office365.com" data-port="587" data-enc="tls">
                                    Microsoft 365 (TLS 587)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary js-mail-preset"
                                    data-mailer="sendmail" data-host="" data-port="" data-enc="tls">
                                    Hosting Sendmail
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary js-mail-preset"
                                    data-mailer="php_mail" data-host="" data-port="" data-enc="tls">
                                    Hosting PHP mail()
                                </button>
                            </div>
                            <div class="text-muted">
                                <strong>Lưu ý cho cả Local &amp; Hosting:</strong>
                                <ul class="mb-0 ps-3 mt-1">
                                    <li><strong>Local Windows (Laragon):</strong> Cần dùng <strong>SMTP</strong> (Gmail App Password 16 ký tự hoặc SMTP Mail Hosting).</li>
                                    <li><strong>Hosting cPanel:</strong> Có thể dùng SMTP hoặc Sendmail / PHP mail() với From Email thuộc tên miền web (VD: <code>noreply@datyso.com</code>).</li>
                                    <li><strong>Gmail / Microsoft 365:</strong> <code>From Address</code> nên trùng với email đăng nhập SMTP để không bị chặn SPF/DMARC.</li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7">
                <div class="ibox">
                    @if(count($val['value']))
                    <div class="ibox-content">
                        @foreach($val['value'] as $keyVal => $item)
                        @php
                            $name = $key.'_'.$keyVal;
                        @endphp
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="system-field-{{ $name }}" class="d-flex justify-content-between fw-semibold mb-1">
                                        <span>{{ $item['label'] }}</span>
                                        {!! renderSystemLink($item) !!}
                                        {!! renderSystemTitle($item) !!}
                                    </label>

                                    @switch($item['type'])
                                        @case('text')
                                            {!! renderSystemInput($name, $systems, $item) !!}
                                            @break
                                        @case('password')
                                            {!! renderSystemPassword($name, $systems, $item) !!}
                                            @break
                                        @case('images')
                                            {!! renderSystemImages($name, $systems) !!}
                                            @break
                                        @case('textarea')
                                            {!! renderSystemTextarea($name, $systems) !!}
                                            @break
                                        @case('select')
                                            {!! renderSystemSelect($item, $name, $systems) !!}
                                            @break
                                        @case('editor')
                                            {!! renderSystemEditor($name, $systems) !!}
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($key === 'mail')
                            <div class="mt-4 pt-3 border-top">
                                <label for="js-test-mail-to" class="fw-bold mb-2 d-block">
                                    Kiểm tra gửi Email thật ngay (không lưu Lead rác)
                                </label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        id="js-test-mail-to"
                                        class="form-control"
                                        value="{{ $systems['lead_sales_email'] ?? '' }}"
                                        placeholder="Nhập email nhận test (mặc định dùng Email Sales phía trên)"
                                    >
                                    <button
                                        type="button"
                                        id="js-btn-test-mail"
                                        class="btn btn-success fw-semibold"
                                        data-url="{{ route('admin.system.testMail', [], false) }}"
                                    >
                                        Gửi Email Test Ngay
                                    </button>
                                </div>
                                <div class="form-text">
                                    Hệ thống sẽ dùng ngay các thông số bạn đang nhập ở trên để gửi thử 1 email báo giá mẫu. Hãy bấm <strong>Lưu lại</strong> sau khi test thành công.
                                </div>
                                <div id="js-test-mail-result" class="alert mt-2 d-none" role="status"></div>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div class="text-end mb-4">
            <button class="btn btn-primary btn-lg px-4" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
@endsection

@section('footer')
<script>
(function () {
    document.querySelectorAll('.js-mail-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var mailerEl = document.getElementById('system-field-mail_mailer');
            var hostEl = document.getElementById('system-field-mail_host');
            var portEl = document.getElementById('system-field-mail_port');
            var encEl = document.getElementById('system-field-mail_encryption');

            if (mailerEl) mailerEl.value = btn.dataset.mailer || 'auto';
            if (hostEl) hostEl.value = btn.dataset.host || '';
            if (portEl) portEl.value = btn.dataset.port || '';
            if (encEl && btn.dataset.enc) encEl.value = btn.dataset.enc;
        });
    });

    var testBtn = document.getElementById('js-btn-test-mail');
    var testInput = document.getElementById('js-test-mail-to');
    var resultBox = document.getElementById('js-test-mail-result');
    var sysForm = document.getElementById('admin-system-form');

    if (testBtn && sysForm && resultBox) {
        testBtn.addEventListener('click', function () {
            var url = testBtn.dataset.url;
            var formData = new FormData(sysForm);
            formData.set('test_email', testInput ? testInput.value.trim() : '');

            resultBox.className = 'alert alert-info mt-2';
            resultBox.textContent = 'Đang kết nối máy chủ email và gửi thử...';
            testBtn.disabled = true;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData,
                credentials: 'same-origin'
            }).then(function (resp) {
                return resp.json().then(function (json) {
                    return { ok: resp.ok && json && json.ok, data: json };
                }).catch(function () {
                    return { ok: false, data: { message: 'Phản hồi không hợp lệ (HTTP ' + resp.status + ').' } };
                });
            }).then(function (res) {
                resultBox.className = 'alert mt-2 ' + (res.ok ? 'alert-success' : 'alert-danger');
                resultBox.textContent = (res.data && res.data.message) ? res.data.message : 'Không rõ kết quả.';
            }).catch(function () {
                resultBox.className = 'alert alert-danger mt-2';
                resultBox.textContent = 'Không thể kết nối máy chủ để kiểm tra email.';
            }).finally(function () {
                testBtn.disabled = false;
            });
        });
    }
})();
</script>
@endsection
