@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/croppie.css') }}">
@endpush

@section('content')

	<div class="container-fluid">

		<div class="row">

			<div class="col-sm-12">
                <div class="white-box">
					<h3 class="box-title m-b-0">เปลี่ยนภาพโปรไฟล์</h3>
                    <p class="text-muted font-13">เปลี่ยนภาพโปรไฟล์ประจำตัว</p>

					<hr>

					<div class="table-responsive">

						<div class="col-sm-12 text-center">

							<div id="upload-demo" class="col-sm-12"></div>

						</div>

						<div class="col-sm-12 text-center">

							<form id="upload-form" method="post" action="{{ url('profile/image-crop') }}" enctype="multipart/form-data">

								<span class="btn btn-default btn-file">
									<span class="fileinput-new"><i class="fa fa-camera"></i> เลือกภาพ</span>
									<span class="fileinput-exists"></span>
									<input id="upload" name="pic_file" type="file" class="form-control" accept=".jpg, .png, .jpeg"/>
								</span>

								<button class="btn btn-success" id="upload-result" type="button"><i class="fa fa-save"></i> บันทึก</button>

                            </form>

						</div>

				    </div>
			    </div>
		    </div>
	    </div>
    </div>
@endsection

@push('js')

    {{-- Crop Image --}}
	<script src="{{ asset('js/croppie.js') }}"></script>

	{{-- Alert --}}
	<script src="{{asset('plugins/components/toast-master/js/jquery.toast.js')}}"></script>
	<script src="{{asset('js/toastr.js')}}"></script>

    <script type="text/javascript">

        $(document).ready(function() {

            $.ajaxSetup({
    			headers: {
    				'X-CSRF-TOKEN': '{{ csrf_token() }}'
    			}
    		});

            //Setup กรอบพรีวิว
            $uploadCrop = $('#upload-demo').croppie({
    			enableExif: true,
    			viewport: {
    				width: 200,
    				height: 200,
    				type: 'circle'
    			},
    			boundary: {
    				width: 300,
    				height: 300
    			}
    		});

            //เมื่อเลือกภาพ
            $('#upload').change(function (){
				var file_name = $(this).val();
				var file_select_type = file_name.substring(file_name.lastIndexOf('.') + 1);
				    file_select_type = file_select_type.toLowerCase();

                if(file_select_type=='png' || file_select_type=='jpg' || file_select_type=='jpeg'){//ไฟล์ถูกต้อง

                    //แสดงผลในกรอบพรีวิว
                    var reader = new FileReader();

        			reader.onload = function (e) {

        				$uploadCrop.croppie('bind', {

        					url: e.target.result

        				}).then(function(){
        					console.log('jQuery bind complete');
        				});

        			}

        			reader.readAsDataURL(this.files[0]);

				}else{

                    $(this).val('');//Clear ค่า

					//Error Message
					$.toast({
						heading: 'แจ้งให้ทราบ!',
						position: 'top-center',
						text: 'คุณเลือกไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์ .png, .jpg หรือ .jpeg เท่านั้น',
						loaderBg: '#ff6849',
						icon: 'error',
						hideAfter: 3000,
						stack: 6
					});

				}

			});

            //เมื่อคลิกปุ่มบันทึก
            $("#upload-result").on('click', function() {
                uploadCropFile();
            });

        });

        //อัพโหลดไฟล์แนบที่ตัดแล้ว
		function uploadCropFile(){

			$uploadCrop.croppie('result', {

				type: 'canvas',

				size: 'viewport'

			}).then(function (resp) {

                var jqxhr = $.ajax({
                        method: "POST",
                        url: "{{ url('profile/image-crop') }}",
                        data: { "image":resp }
                    }).done(function(data) {

                        if(data.hasOwnProperty('status') && data.status==true){

                            var html = '<img src="' + data.image + '" alt="user-img" class="img-circle" />';

                			$("#profile-image").find('img:first').remove();
                			$("#profile-image").prepend(html);

                            //Success Message
                			$.toast({
                				heading: 'สำเร็จ!',
                				position: 'top-center',
                				text: 'อัพเดทภาพโปรไฟล์แล้ว',
                				loaderBg: '#ff6849',
                				icon: 'success',
                				hideAfter: 3000,
                				stack: 6
                			});

                        }else{
                            $.toast({
                               heading: 'ล้มเหลว!',
                               position: 'top-center',
                               text: 'อัพเดทภาพโปรไฟล์ล้มเหลว',
                               loaderBg: '#00ff00',
                               icon: 'error',
                               hideAfter: 3000,
                               stack: 6
                            });
                        }

                    }).fail(function() {
                        $.toast({
                           heading: 'ล้มเหลว!',
                           position: 'top-center',
                           text: 'อัพเดทภาพโปรไฟล์ล้มเหลว',
                           loaderBg: '#00ff00',
                           icon: 'error',
                           hideAfter: 3000,
                           stack: 6
                        });
                    });

			});
		}


	</script>
@endpush
