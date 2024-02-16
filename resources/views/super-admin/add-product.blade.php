@extends('super-admin-layouts.app-master')
@section('title', 'Track Cert - Add Product')
@section('content')
<meta name="_token" content="{{csrf_token()}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/dropzone@5.9.2/dist/min/dropzone.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css">
<div class="body-main-content">
    <div class="d-flex justify-content-between">
        <div class="pmu-filter-section">
            <div class="pmu-filter-heading">
                <h2>Create Products</h2>
            </div>
        </div>
        <a href="{{ route('SA.Products') }}"><button class="product-list-btn"><i class="bbi bi-caret-left me-2"></i>Product List</button></a>
    </div>

    <div class="pmu-content-list mt-3">
        <div class="pmu-content">
            <form method="post" action="{{ route('SA.SubmitProduct') }}" id="AddProduct" enctype="multipart/form-data">@csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="product-item-card">
                            <div class="card-header">
                                <h2>Product Info</h2>
                            </div>
                            <div class="pmu-item-content">
                                <div class="mb-3 form-group">
                                    <label for="productname" class="form-label">Product Name <b class="text-danger">*</b> </label>
                                    <input type="text" placeholder="Product Name" name="name" class="form-control" id="productname" aria-describedby="productname" value="{{old('name')}}">
                                </div>
                                <div class="mb-3 form-group">
                                    <label for="description" class="form-label">Short description <b class="text-danger">*</b> </label>
                                    <textarea class="form-control" name="short_description" id="description" rows="3" placeholder="Short Description">{{old('short_description')}}</textarea>
                                </div>
                                <div class="mb-5">
                                    <label for="makeMeSummernote1" class="form-label">Full description</label>
                                    <textarea name="full_description" cols="30" rows="10" id="makeMeSummernote1" class="form-control full-description" placeholder="Full description">{{old('full_description')}}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="makeMeSummernote2" class="form-label">Refunds, Returns and Cancellation Policies</label>
                                    <textarea name="refund_policy" cols="30" rows="10" id="makeMeSummernote2" class="form-control full-description" placeholder="Refunds, Returns and Cancellation Policies">{{old('refund_policy')}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="product-item-card">
                            <div class="card-header form-group">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h2>Upload Product Image (jpg,jpeg,png only) <b class="text-danger">*</b></h2>
                                    <button class="file-upload">
                                        <input type="file" class="file-input" accept="image/png, image/jpg, image/jpeg" name="product_image"> upload image
                                    </button>
                                </div>
                            </div>
                            <div class="pmu-item-content">
                                <div class="row">
                                    <div class="small-12 large-4 columns">
                                        <div class="containers">
                                            <div class="imageWrapper">
                                                <img style="width: 120px !important; border-radius: 8px;" class="image" src="{{ assets('assets/superadmin-images/upload.svg') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-item-card">
                            <div class="card-header">
                                <h2>Product Weight or Volume <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" type="number" min="1" step="0.01" max="100" value="{{ old('product_weight') }}" placeholder="Product Weight or Volume" aria-label="default input example" name="product_weight">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="product_weight_unit">
                                            <option @if(old('product_weight_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('product_weight_unit') == "lbs") selected @endif value="lbs">Lbs (Pounds)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-item-card">
                            <div class="card-header">
                                <h2>Product Dimensions <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="label-p">Length</p>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" name="product_length" min="1" step="0.01" max="100" type="number" value="{{ old('product_length') }}" placeholder="Product Length" aria-label="default input example">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="product_length_unit">
                                            <option @if(old('product_length_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('product_length_unit') == "inch") selected @endif value="inch">Inch(Inches)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <p class="label-p">Width</p>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" name="product_width" type="number" min="1" step="0.01" max="100" value="{{ old('product_width') }}" placeholder="Product Width" aria-label="default input example">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="product_width_unit">
                                            <option @if(old('product_width_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('product_width_unit') == "inch") selected @endif value="inch">Inch(Inches)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <p class="label-p">Height</p>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" name="product_height" type="number" min="1" step="0.01" max="100" value="{{ old('product_height') }}" placeholder="Product Height" aria-label="default input example">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="product_height_unit">
                                            <option @if(old('product_height_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('product_height_unit') == "inch") selected @endif value="inch">Inch(Inches)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-item-card">
                            <div class="card-header">
                                <h2>Package Weight or Volume <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" min="1" step="0.01" max="100" name="package_weight" type="number" value="{{old('package_weight')}}" placeholder="Package Weight or Volume" aria-label="default input example">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="package_weight_unit">
                                            <option @if(old('package_weight_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('package_weight_unit') == "lbs") selected @endif value="lbs">Lbs (Pounds)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-item-card">
                            <div class="card-header">
                                <h2>Package Dimensions <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p class="label-p">Length</p>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" min="1" step="0.01" max="100" type="number" value="{{old('package_length')}}" placeholder="Package Length" aria-label="default input example" name="package_length">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="package_length_unit">
                                            <option @if(old('package_length_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('package_length_unit') == "inch") selected @endif value="inch">Inch(Inches)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <p class="label-p">Width</p>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" min="1" step="0.01" max="100" type="number" value="{{old('package_width')}}" placeholder="Package Width" aria-label="default input example" name="package_width">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="package_width_unit">
                                            <option @if(old('package_width_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('package_width_unit') == "inch") selected @endif value="inch">Inch(Inches)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mt-3">
                                        <p class="label-p">Height</p>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <input class="form-control" min="1" step="0.01" max="100" type="number" value="{{old('package_height')}}" placeholder="Package Height" aria-label="default input example" name="package_height">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <select class="form-select" aria-label="Default select example" name="package_height_unit">
                                            <option @if(old('package_height_unit') == "") selected @endif value="">Select Unit</option>
                                            <option @if(old('package_height_unit') == "inch") selected @endif value="inch">Inch(Inches)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>Product Category <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content search-select form-group">
                                <select id="SelExampl" class="form-select" name="category" placeholder="Select">
                                    <option @if(old('category') == "") selected @endif value="">Select Category</option>
                                    @foreach(getCategory(2) as $val)
                                        <option @if(old('category') == $val->id) selected @endif value="{{ $val->id }}">{{ $val->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>Product Tags <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content search-select form-group">
                                <select class="form-control livesearch p-3" name="tags[]" multiple="multiple">
                                    @foreach(getTags(2) as $val)
                                        <option value="{{ $val->id }}">{{ $val->tag_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>Product Status<b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content search-select form-group">
                                <select class="form-select" name="status" aria-label="Default select example">
                                    <option @if(old('status') == "") selected @endif value="">Select Status</option>
                                    <option @if(old('status') == "1") selected @endif value="1">Active</option>
                                    <option @if(old('status') == "0") selected @endif value="0">In-Active</option>
                                </select>
                            </div>
                        </div>
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>SKU Code <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content search-select form-group">
                                <input type="text" class="form-control percentage-input code" placeholder="Eg. 000-000-000" id="code" name="sku_code" value="{{ old('sku_code') }}">
                            </div>
                        </div>
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>Regular Pricing (USD). Please include prepackaging expenses <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content search-select form-group">
                                <input type="text" class="form-control percentage-input" placeholder="Regular Pricing (USD)" id="regular_price" name="regular_price" value="{{ old('regular_price') }}">
                            </div>
                        </div>
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>Sale Pricing (USD)</h2>
                            </div>
                            <div class="pmu-item-content search-select form-group">
                                <input type="text" class="form-control percentage-input" placeholder="Sale Pricing (USD)" id="sale_price" name="sale_price" value="{{ old('sale_price') }}">
                            </div>
                        </div>
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>Stock Quantity <b class="text-danger">*</b></h2>
                            </div>
                            <div class="pmu-item-content search-select form-group">
                                <input type="number" min="1" step="1" max="100000" class="form-control percentage-input" placeholder="Stock Quantity" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity') }}">
                                <input type="hidden" id="arrayOfImage" name="array_of_image" value="">
                            </div>
                        </div>
                        <div class="product-item-card right-card">
                            <div class="card-header">
                                <h2>Stock Availability</h2>
                            </div>
                            <div class="pmu-item-content search-select">
                                <select name="stock_avail" id="stock_avail" disabled class="w-100">
                                    <option value="1" selected>In Stock</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-item-card">
                    <div class="card-header form-group" style="border-bottom: none;">
                        <div class="d-flex flex-column justify-content-between">
                            <div class="product-gallery-parent-div">
                                <h2>Upload Product Multiple Image (jpg,jpeg,png only)</h2>
                            </div> 
                            <div class="dropzone m-3" id="multipleImage">
                                <div class="dz-default dz-message">
                                    <span>Click once inside the box to upload an image 
                                        <br>
                                        <small class="text-danger">Make sure the image size is less than 1 MB</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="product-item-card update-card">
                        <div class="pmu-item-content bg-white">
                            <button class="btn update-btn btn-sm" type="submit">Create</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Include jQuery Validation Plugin -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.0-beta3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="{{ assets('assets/superadmin-js/create-product.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.0/dropzone.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>

<!-- Style of h2 tag and error message  jQuery Validation -->
<style>
    .error {
        color: red;
    }

    h2 {
        color: white;
    }
</style>

<script type="text/javascript">
    let arrOfImg = [];

    Dropzone.options.multipleImage = {
        maxFilesize: 1,
        renameFile: function(file) {
            var dt = new Date();
            var time = dt.getTime();
        return time+file.name;
        },
        acceptedFiles: ".jpeg,.jpg,.png",
        timeout: 5000,
        addRemoveLinks: true,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        url: "{{ route('imageUpload') }}",
        removedfile: function(file) 
        {
            var name = file.upload.filename;
            $.ajax({
                headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                type: 'POST',
                url: '{{ route("imageDelete") }}',
                data: {filename: name},
                success: function (data){
                    if(data.status){
                        console.log("File deleted successfully!!");
                        if(data.key == 2){
                            const inde = arrOfImg.indexOf(data.file_name);
                            if (inde > -1){
                                arrOfImg.splice(inde, 1);
                                $("#arrayOfImage").val(JSON.stringify(arrOfImg));
                            }
                        }
                        let oplength = arrOfImg.length;
                        if(oplength>0){
                        $('.dz-default.dz-message').hide(); 
                        } else $('.dz-default.dz-message').show();
                    }else{
                        console.log("File not deleted!!");
                    }
                },
                error: function(e) {
                    console.log(e);
                }
            });
            var fileRef;
            return (fileRef = file.previewElement) != null ? 
            fileRef.parentNode.removeChild(file.previewElement) : void 0;
        },
        success: function(file, response) 
        {
            if(response.key == 1){
                arrOfImg.push(response.file_name);
                $("#arrayOfImage").val(JSON.stringify(arrOfImg));
                file.upload.filename = response.file_name;
                let oplength = arrOfImg.length;
                if(oplength>0){
                    $('.dz-default.dz-message').hide(); 
                } else $('.dz-default.dz-message').show();
            }
        },
        error: function(file, response)
        {
            let oplength = arrOfImg.length;
            if(oplength>0){
               $('.dz-default.dz-message').hide(); 
            } else $('.dz-default.dz-message').show();
            console.log(file.previewElement);
            var fileRef;
            return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file.previewElement) : null;
        }
    };
    console.log(arrOfImg);
</script>

<!-- Include jQuery Validation -->
<script>
    $('.livesearch').select2({
        placeholder: 'Select tags',
        tags: true,
    });

    
    $(document).ready(function() {
        $('.code').mask('000-000-000');

        $(".select2-container .selection .select2-selection .select2-search__field").addClass('form-control');

        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param * 1000000)
        }, 'File size must be less than {0} MB');

        $('#AddProduct').validate({
            rules: {
                name: {
                    required: true,
                },
                short_description: {
                    required: true,
                },
                product_image: {
                    filesize: 1,
                    required: true
                },
                product_weight: {
                    required: true,
                },
                product_length: {
                    required: true,
                },
                product_width: {
                    required: true,
                },
                product_height: {
                    required: true,
                },
                package_weight: {
                    required: true,
                },
                package_length: {
                    required: true,
                },
                package_width: {
                    required: true,
                },
                package_height: {
                    required: true,
                },
                product_weight_unit: {
                    required: true,
                },
                product_length_unit: {
                    required: true,
                },
                product_width_unit: {
                    required: true,
                },
                product_height_unit: {
                    required: true,
                },
                package_weight_unit: {
                    required: true,
                },
                package_length_unit: {
                    required: true,
                },
                package_width_unit: {
                    required: true,
                },
                package_height_unit: {
                    required: true,
                },
                category: {
                    required: true,
                },
                "tags[]": {
                    required: true,
                },
                status: {
                    required: true,
                },
                sku_code: {
                    required: true,
                    remote: {
                        type: 'get',
                        url: arkansasUrl + '/check_sku_code',
                        data: {
                            'sku_code': function () { return $("#code").val(); }
                        },
                        dataType: 'json'
                    }
                },
                regular_price: {
                    required: true,
                },
                stock_quantity: {
                    required: true,
                },
            },

            submitHandler: function(form) {
                // This function will be called when the form is valid and ready to be submitted
                form.submit();
            },
            errorElement: "span",
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);

            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass("is-invali");
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass("is-invali");
            },
        });
    });

</script>

@endsection