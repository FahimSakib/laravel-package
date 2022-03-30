<div class="modal" tabindex="-1" role="dialog" id="saveExcelModal">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" id="storeExcelForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-12">
                                <span>All the (*) marked fileds are required</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <input type="file" class="dropify" name="excel" id="excel" data-errors-position="outside"
                                data-allowed-file-extensions="xlsx cvs" data-max-file-size="1M">
                            <x-selectbox col="col-md-12" required="required" labelName="Type" name="type">
                                <option value="1">Single Sheet</option>
                                <option value="2">Multiple Sheet</option>
                            </x-selectbox>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="save-btn"></button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
