<form action="{{ route('recruitment.index') }}" method="get" enctype="multipart/form-data">
    <div class="row mt-5">
        @csrf
        <div class="col-sm-4 mb-2">
            <select name="type" class="w-100 p-2 form-select rounded-0">
                <option value="0">Tất cả vị trí</option>
                @foreach ($types as $item)
                    <option value="{{ $item }}" {{ (old('type') == $item || $selectedType == $item) ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4 mb-2">
            <select name="location" class="w-100 p-2 form-select rounded-0">
                <option value="0">Tất cả nơi làm việc</option>
                @foreach ($locations as $item)
                    <option value="{{ $item }}" {{ (old('location') == $item || $selectedLocation == $item) ? 'selected' : '' }}>
                        {{ $item }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-primary p-2 rounded-0 fw-semibol">Lọc kết quả</button>
        </div>
    </div>
</form>
