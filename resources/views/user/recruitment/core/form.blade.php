<div class="">
    <div class="container bg-light py-4">
        <h4 class="text-center fw-bolder text-color">Nộp đơn ứng tuyển</h4>
        <form action="{{ route('recruitment.register') }}" enctype="multipart/form-data" method="post">
            @csrf
            <div class="my-3">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="email" name="email" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('email') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="full_name" name="full_name" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('full_name') }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Vị trí ứng tuyển <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="nominee" name="nominee" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('nominee') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Cấp bậc đào tạo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="level" name="level" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('level') }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Tên trường <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="school_name" name="school_name" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('school_name') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Chuyên ngành <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="specialized" name="specialized" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('specialized') }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Công việc gần nhất <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="latest_job" name="latest_job" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('latest_job') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Năm kinh nghiệm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="years_experience" name="years_experience" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('years_experience') }}</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Mức lương mong muốn <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="desired_salary" name="desired_salary" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('desired_salary') }}</p>
                    </div>
                    <div class="col-sm-6">
                        <label for="title" class="form-label text-color fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-0 p-2" id="phone" name="phone" value="">
                        <p class="help is-danger text-danger">{{ $errors->first('phone') }}</p>
                    </div>
                </div>
                <div class="">
                    <label for="title" class="form-label text-color fw-semibold">Tải lên hồ sơ/CV của bạn <br>
                        <span class="text-danger">Hỗ trợ định dạng *.doc, *docx, *.pdf và không quá 2MB</span>
                    </label>
                    <input type="file" class="form-control rounded-0 p-2" id="file" name="file" value="">
                    <p class="help is-danger text-danger">{{ $errors->first('file') }}</p>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary rounded-0 p-2 fs-5 fw-bolder">Gửi thông tin</button>
            </div>
        </form>
    </div>
</div>