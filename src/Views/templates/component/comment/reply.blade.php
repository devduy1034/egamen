<form class="mt-3" id="form-reply" action="" method="post" enctype="multipart/form-data">
    <div class="response-reply"></div>
    <div class="form-group mb-2">
        <textarea class="form-control text-sm" placeholder="Viet cau tra loi cua ban" name="dataReview[content]" id="reply-content" data-name="@{{ $params['fullname'] }}:" rows="5"></textarea>
    </div>
    <div class="form-group">
        <div class="row row-10">
            <div class="col-4 mg-col-10 mb-2">
                <input type="text" class="form-control text-sm" name="dataReview[fullname]" id="reply-fullname" placeholder="Nhap ho ten lien he *" required>
            </div>
            <div class="col-4 mg-col-10 mb-2">
                <input type="text" class="form-control text-sm" name="dataReview[phone]" id="reply-phone" placeholder="Nhap so dien thoai lien he *">
            </div>
            <div class="col-4 mg-col-10 mb-2">
                <input type="text" class="form-control text-sm" name="dataReview[email]" id="reply-email" placeholder="Nhap email lien he *" required>
            </div>
        </div>
    </div>
    <div class="text-right">
        <button type="submit" class="btn btn-sm btn-warning me-2 py-2 px-3">Gui tra loi</button>
        <button type="button" class="btn btn-sm btn-secondary btn-cancel-reply py-2 px-3">Huy bo</button>
    </div>
    <input type="hidden" name="dataReview[id_parent]" value="{{ $params['id'] }}">
    <input type="hidden" name="dataReview[id_variant]" value="{{ $params['id_variant'] }}">
    <input type="hidden" name="dataReview[type]" value="{{ $params['type'] }}">
    <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">
</form>
