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
