<button type="button" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">Login</button>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content bg-light">
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="exampleModalLabel">ログイン</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <form>
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label text-dark">ユーザー名</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="mb-3">
                        <label for="message-text" class="col-form-label text-dark">パスワード</label>
                        <input type="text" class="form-control" id="recipient-name">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary">ログイン</button>
            </div>
        </div>
    </div>
</div>