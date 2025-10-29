@if (session()->has('messageSuccess'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session()->get('messageSuccess') }}
        <button type="button" class="btn-close p-3" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session()->has('messageError'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session()->get('messageError') }}
        <button type="button" class="btn-close p-3" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
