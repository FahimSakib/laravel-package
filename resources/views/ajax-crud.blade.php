@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-9">
                            Users List
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-info" type="button" id="export-btn">Export Excel
                                File</button>
                            <button class="btn btn-sm btn-success" onclick="showExcelModal('Add Excel file','Upload')"
                                type="button">Add Excel File</button>
                            <button class="btn btn-sm btn-primary" onclick="showModal('Add New User','Save')"
                                type="button">Add New</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 my-3">
                            <form method="post" id="form-filter">
                                <div class="row">
                                    <x-textbox col="col-md-3" labelName="Name" name="name"
                                        placeholder="Enter your name" />
                                    <x-textbox col="col-md-3" type="email" labelName="Email" name="email"
                                        placeholder="Enter your email" />
                                    <x-textbox col="col-md-3" labelName="Mobile Number" name="mobile_no"
                                        placeholder="Enter your mobile number" />
                                    <x-selectbox col="col-md-3" labelName="District" name="district_id"
                                        onchange="upazilaList(this.value,'form-filter')">
                                        @if ($districts)
                                        @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->location_name }}</option>
                                        @endforeach
                                        @endif
                                    </x-selectbox>
                                    <x-selectbox col="col-md-3" labelName="Upazila" name="upazila_id" />
                                    <x-selectbox col="col-md-3" labelName="Role" name="role_id">
                                        @if ($roles)
                                        @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                        @endforeach
                                        @endif
                                    </x-selectbox>
                                    <x-selectbox col="col-md-3" labelName="Status" name="status">
                                        <option value="1">Active</option>
                                        <option value="2">In Active</option>
                                    </x-selectbox>
                                    <div class="form-group col-md-3" style="padding-top: 22px">
                                        <button type="button" class="btn btn-success" id="btn-filter">Filter</button>
                                        <button type="reset" class="btn btn-secondary" id="btn-reset">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-bordered" id="dataTable">
                                <thead>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Disctrict</th>
                                    <th>Upazila</th>
                                    <th>Postal Code</th>
                                    <th>Verified Email</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('modal.modal-xl')
@include('modal.modal-user-view')
@include('modal.modal-excel')
@endsection

@push('style')
{{-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.css" /> --}}
<link rel="stylesheet" href="{{ asset('css/datatables.bundle7.0.8.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="{{ asset('css/dropify.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.7/dist/sweetalert2.min.css">
<style>
    .required label:first-child::after {
        content: "* ";
        color: red;
        font-weight: bold;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #5cb85c;
    }

    input:not(:checked)+.slider {
        background-color: #d9534f;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #5cb85c;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

</style>
@endpush

@push('script')
{{-- <script src="{{ asset('js/datatables.bundle7.0.8.js')}}"></script> --}}
<script src="https://kit.fontawesome.com/92da958448.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('js/dropify.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.7/dist/sweetalert2.min.js"></script>

<script>
    let _token = "{{ csrf_token() }}";

    var table;

    $(document).ready(function () {
        table = $('#dataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "responsive": true,
            "bInfo": true,
            "bFilter": false,
            "lengthMenu": [
                [5, 10, 15, 25, 50, 100, 1000, 10000, -1],
                [5, 10, 15, 25, 50, 100, 1000, 10000, "All"]
            ],
            "pageLength": 5,
            "language": {
                processing: `<img src="{{ asset('storage/svg/Swing-Preloader.svg') }}" alt="loading icon" />`,
                emptyTable: '<strong class="text-danger>No data Found</strong>',
                infoEmpty: '',
                zeroRecords: '<strong class="text-danger>No data Found</strong>'
            },
            "ajax": {
                "url": "{{ route('user.list') }}",
                "type": "POST",
                "data": function (data) {
                    data._token = _token;
                    data.name = $('#form-filter #name').val();
                    data.email = $('#form-filter #email').val();
                    data.mobile_no = $('#form-filter #mobile_no').val();
                    data.district_id = $('#form-filter #district_id').val();
                    data.upazila_id = $('#form-filter #upazila_id').val();
                    data.role_id = $('#form-filter #role_id').val();
                    data.status = $('#form-filter #status').val();
                }
            }
        });
    });

    $('#btn-filter').click(function () {
        table.ajax.reload();
    });

    $('#btn-reset').click(function () {
        $('#form-filter')[0].reset();
        table.ajax.reload();
    });


    $('.dropify').dropify();

    function showModal(title, save) {
        $('#storeForm')[0].reset();
        $('.password').parent().removeClass('d-none');
        $('.password_confirmation').parent().removeClass('d-none');
        $('#storeForm').find('.is-invalid').removeClass('is-invalid');
        $('#storeForm').find('.error').remove();
        $('.dropify-clear').trigger('click');

        $("#saveDataModal").modal('toggle', {
            keyboard: false,
            backdrop: 'static',
        });
        $("#saveDataModal .modal-title").text(title);
        $("#saveDataModal #save-btn").text(save);
    };

    $(document).on('click', '#save-btn', function () {
        let storeForm = document.getElementById('storeForm');
        let formData = new FormData(storeForm);
        let url = "{{ route('user.store') }}";
        let id = $('#update_id').val();
        let method;
        if (id) {
            method = 'update';
        } else {
            method = 'add';
        }
        store_form_data(table, method, url, formData);
    });

    function store_form_data(table, method, url, formData) {
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            dataType: "JSON",
            contentType: false,
            processData: false,
            cache: false,
            success: function (data) {
                $('#storeForm').find('.is-invalid').removeClass('is-invalid');
                $('#storeForm').find('.error').remove();
                if (data.status == false) {
                    $.each(data.errors, function (key, value) {
                        $('#storeForm #' + key).addClass('is-invalid');
                        $('#storeForm #' + key).parent().append(
                            '<div class="alert alert-danger mt-1 error">' + value + '</div>');
                    });
                } else {
                    flashMessage(data.status, data.message);
                    if (data.status == 'success') {
                        if (method == 'update') {
                            table.ajax.reload(null, false);
                        } else {
                            table.ajax.reload();
                        }
                        $("#saveDataModal").modal('hide');
                    }
                }
            },
            error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
            }
        });
    }

    $(document).on('click', '.data_edit', function () {
        let id = $(this).data('id');
        if (id) {
            $.ajax({
                url: "{{ route('user.edit') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: _token
                },
                dataType: "JSON",
                success: function (data) {
                    $('.password').parent().addClass('d-none');
                    $('.password_confirmation').parent().addClass('d-none');
                    $('#storeForm #update_id').val(data.id);
                    $('#storeForm #name').val(data.name);
                    $('#storeForm #email').val(data.email);
                    $('#storeForm #mobile_no').val(data.mobile_no);
                    $('#storeForm #mobile_no').val(data.mobile_no);
                    $('#storeForm #district_id').val(data.district_id);
                    upazilaList(data.district_id, 'storeForm');
                    setTimeout(() => {
                        $('#storeForm #upazila_id').val(data.upazila_id);
                    }, 1000);
                    $('#storeForm #postal_code').val(data.postal_code);
                    $('#storeForm #address').val(data.address);
                    $('#storeForm #role_id').val(data.role_id);
                    if (data.avatar) {
                        let avatar = "{{ asset('storage/User') }}/" + data.avatar;
                        $('#storeForm .dropify-preview').css('display', 'block');
                        $('#storeForm .dropify-render').html('<img src="' + avatar + '"/>');
                        $('#storeForm #old_avatar').val(data.avatar);
                    }
                    $("#saveDataModal").modal('toggle', {
                        keyboard: false,
                        backdrop: 'static',
                    });
                    $("#saveDataModal .modal-title").html(
                        '<i class="fa-solid fa-pen-to-square"></i><span> Edit ' + data.name +
                        '\'s data</span>');
                    $("#saveDataModal #save-btn").text('Update');
                },
                error: function (xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                }
            });
        };
    });

    $(document).on('click', '.data_view', function () {
        let id = $(this).data('id');
        if (id) {
            $.ajax({
                url: "{{ route('user.show') }}",
                type: "POST",
                data: {
                    id: id,
                    _token: _token
                },
                dataType: "JSON",
                success: function (data) {
                    $('#view_data').html('');
                    $('#view_data').html(data.user_view);

                    $("#viewDataModal").modal('toggle', {
                        keyboard: false,
                        backdrop: 'static',
                    });
                    $("#viewDataModal .modal-title").html('<i class="fa-solid fa-eye"></i><span> ' +
                        data.name + '\'s data</span>');
                },
                error: function (xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                }
            });
        };
    });

    $(document).on('change', '.change_status', function () {
        let id = $(this).data('id');
        let status;
        if ($(this).is(':checked')) {
            status = 1;
        } else {
            status = 2;
        }
        if (id && status) {
            $.ajax({
                url: "{{ route('user.change.status') }}",
                type: "POST",
                data: {
                    id: id,
                    status: status,
                    _token: _token
                },
                dataType: "JSON",
                success: function (data) {
                    flashMessage(data.status, data.message);
                    if (data.status == 'success') {
                        table.ajax.reload(null, false);
                    }
                },
                error: function (xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                }
            });
        };
    });

    $(document).on('click', '.data_delete', function () {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let url = "{{ route('user.delete') }}"
        let row = table.row($(this).parent('tr'));
        delete_data(id, url, table, row, name);
    });

    function delete_data(id, url, table, row, name) {
        Swal.fire({
            title: 'Are you sure to delete ' + name + '\'s data?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        id: id,
                        _token: _token
                    },
                    dataType: "JSON",
                }).done(function (response) {
                    if (response.status == 'success') {
                        Swal.fire('Deleted', response.message).then(function () {
                            table.row(row).remove().draw(false);
                        });
                    }
                }).fail(function (response) {
                    Swal.fire('Oopss...', 'Something went wrong', 'error');
                })
            }
        })
    }

    function upazilaList(district_id, form) {
        if (district_id) {
            $.ajax({
                url: "{{ route('upazila.list') }}",
                type: "POST",
                data: {
                    district_id: district_id,
                    _token: _token
                },
                dataType: "JSON",
                success: function (data) {
                    $('#' + form + ' #upazila_id').html('');
                    $('#' + form + ' #upazila_id').html(data);
                },
                error: function (xhr, ajaxOption, thrownError) {
                    console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
                }
            });
        };
    };

    function showExcelModal(title, save) {
        $('#storeExcelForm')[0].reset();
        $('#storeExcelForm').find('.is-invalid').removeClass('is-invalid');
        $('#storeExcelForm').find('.error').remove();
        $('.dropify-clear').trigger('click');

        $("#saveExcelModal").modal('toggle', {
            keyboard: false,
            backdrop: 'static',
        });
        $("#saveExcelModal .modal-title").text(title);
        $("#saveExcelModal #upload-btn").text(save);
    };


    $(document).on('click', '#upload-btn', function () {
        let storeExcelForm = document.getElementById('storeExcelForm');
        let formData = new FormData(storeExcelForm);
        let url = "{{ route('excel.file.upload') }}";
        store_excel_form_data(table, url, formData);
    });

    function store_excel_form_data(table, url, formData) {
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            dataType: "JSON",
            contentType: false,
            processData: false,
            cache: false,
            success: function (data) {
                $('#storeExcelForm').find('.is-invalid').removeClass('is-invalid');
                $('#storeExcelForm').find('.error').remove();
                if (data.status == false) {
                    $.each(data.errors, function (key, value) {
                        $('#storeExcelForm #' + key).addClass('is-invalid');
                        $('#storeExcelForm #' + key).parent().append(
                            '<div class="alert alert-danger mt-1 error">' + value + '</div>');
                    });
                } else {
                    flashMessage(data.status, data.message);
                    if (data.status == 'success') {
                        table.ajax.reload();
                        $("#saveExcelModal").modal('hide');
                    }
                }
            },
            error: function (xhr, ajaxOption, thrownError) {
                console.log(thrownError + '\r\n' + xhr.statusText + '\r\n' + xhr.responseText);
            }
        });
    }

    $(document).on('click','#export-btn',function(){
        let column = '';
        let dir = '';
        let start = table.page.info().start;
        let length = table.page.len();
        if(table.order().length == 1){
            column = table.order()[0][0];
            dir = table.order()[0][1];
        }
        let query = {start:start, length:length, column:column, dir:dir}
        // console.log(query);
        let url = "{{ route('excel.file.export') }}?"+$.param(query);
        window.open(url,'_blank');
    });

    function flashMessage(status, message) {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        switch (status) {
            case 'success':
                toastr.success(message, 'SUCCESS');
                break;
            case 'error':
                toastr.error(message, 'ERROR');
                break;
            case 'info':
                toastr.info(message, 'INFO');
                break;
            case 'warning':
                toastr.warning(message, 'WARNING');
                break;
        }
    };

</script>
@endpush
