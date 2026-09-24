@push('css')
    <style>

        .cookie{
            width: 98%;
            height: 100px;
            position: fixed;
            bottom: 30%;
            border-radius: 10px;
            left: 1%;
            padding: 10px 20px;
            z-index: 9999;
            cursor: pointer;
        }

        .cookie .accept {
            background-color: #40CC79;
            color: #fff !important;
            border-radius: 32px;
            padding: 3px 23px;
            /* align-self: center; */
            font-size: 19px;
            margin-top: 2.5%;
            margin-left: 3%;

        }
        .cookie .accept:hover {
            background-color: #30b867;
        }

        .cookie-btn-container{
            position: absolute;
            float: left;
            z-index: 1;
            margin-left: -2%;
            top: 40%;
            transform: translateY(-50%);
        }

        .cookie .cookie_detail{
            margin-left:5% !important;
            margin-right:5% !important;
            margin-bottom:0%;
            font-size: 15pt;
        }

        @media only screen and (max-width:767px){
            .cookie-btn-container{ display: none;}
        }

    </style>
@endpush

@push('js')
    
<section class="cookie">
    <div class="col-md-12">
        <div class="container">
            <div class="card white-box">
                <div class="card-body">
                    <div class="row">
                        <div class="cookie-btn-container">
                            <img src="{!! asset('icon/cookie.png') !!}" width="60" class="img-circle">
                        </div>
                        <div class="cookie_detail">
                            สำนักงานมาตรฐานผลิตภัณฑ์อุตสาหกรรมมีการใช้งานคุกกี้ (Cookies) เพื่อจัดการข้อมูลส่วนบุคคลและช่วยเพิ่มประสิทธิภาพการใช้งานเว็บไซต์ คุณสามารถอ่านข้อมูลเพิ่มเติมได้ที่
                            <a href="https://www.tisi.go.th/data/service/pdpa/6.pdf" target="_blank">นโยบายการใช้คุกกี้</a>
                        </div>

                        <div class="cookie_detail">
                            <button type="button" class="btn btn-outline btn-rounded btn-success pull-right acceptcookies">
                                ยอมรับ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <script type="text/javascript">

        $(document).ready(function() {

            $('.acceptcookies').click(function (e) {

                var expDate = new Date();

                var Time = (1440 * 60 * 1000) * 365;
                expDate.setTime(expDate.getTime() + Time ); // add 15 minutes

                expires = "; expires=" + expDate.toUTCString();

                document.cookie = 'active_cookie' + "=" + 'active'  + expires + ';path=/';

                console.log( 'active_cookie' + "=" + 'active'  + expires + ';path=/');

                $('.cookie').remove();

            });

        });

    </script>
@endpush
