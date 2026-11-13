@section('headerTitle', 'Cấu hình hệ thống')
@extends('admin.layout')
@section('templateContent')
@php
    $url = route('admin.system.store');
@endphp
<div class="wrapper wrapper-content animated fadeInRight">
    @include('admin.core.alert')
</div>

<form action="{{ $url }}" method="post" class="box">
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

                        @if($key === 'odoo')
                        <div class="mt-3 p-3 bg-light rounded border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-primary"><i class="fa fa-info-circle me-1"></i> Thông tin kết nối gửi Dev Odoo:</span>
                                <span class="badge bg-success text-white">REST API v1</span>
                            </div>
                            <div class="small text-muted mb-2">
                                <div><strong>API Endpoint (Lấy danh sách Lead):</strong> <code class="user-select-all text-dark">{{ url('/api/v1/leads') }}</code></div>
                                <div><strong>API Key hiện tại:</strong> <code class="user-select-all text-danger fw-bold">{{ \App\Services\LeadService::getOdooApiKey() }}</code></div>
                                <div><strong>Header xác thực:</strong> <code class="user-select-all text-dark">Authorization: Bearer {{ \App\Services\LeadService::getOdooApiKey() }}</code> hoặc <code class="user-select-all text-dark">X-API-Key: {{ \App\Services\LeadService::getOdooApiKey() }}</code></div>
                            </div>
                            <div class="bg-dark text-white p-2 rounded small font-monospace user-select-all" style="font-size: 12px; overflow-x: auto;">
curl -X GET "{{ url('/api/v1/leads?since_id=0&limit=50') }}" \<br>
&nbsp;&nbsp;-H "Authorization: Bearer {{ \App\Services\LeadService::getOdooApiKey() }}" \<br>
&nbsp;&nbsp;-H "Accept: application/json"
                            </div>
                            <div class="small text-muted mt-2">
                                <em>Gợi ý: Dev Odoo có thể chạy cronjob định kỳ gọi API với tham số <code>?since_id={id_cuoi_cung}</code> để lấy các Lead mới phát sinh mà không bị sót hoặc trùng lặp.</em>
                            </div>
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
