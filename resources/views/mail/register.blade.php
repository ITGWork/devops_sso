

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   <style>
       #style{
            /* width: 50%; */
            padding: 5px;
            border: 5px solid gray;
            margin: 0;

       }
       .address{
            /* width: 50%; */
            padding: 5px;
            border: 1px solid gray;
            margin: 0;

       }
        #table_th th{
        text-align: right;
        }
        .indent50 {
        text-indent: 50px;
        }
        .indent100 {
        text-indent: 100px;
        }
   </style>
</head>
<body>
   <div id="style">
        <p>
           <b>เรียน {{@$name}}</b>
        </p>
        <p>
            <b>เรื่อง แจ้งข้อมูลการลงทะเบียนระบบบริการอิเล็กทรอนิกส์ สมอ.</b>
        </p>
        <p class="indent50">
            ตามที่ {{@$name}}   ได้ลงทะเบียนในระบบ เมื่อ    {{ HP::formatDateThai(date('Y-m-d')) }}
        </p>
        <p>ขณะนี้ระบบได้รับข้อมูลการลงทะเบียนของท่านแล้ว กรุณาคลิกลิงก์เพื่อยืนยันตัวตน</p>
        @if(!is_null($username))
            <p>username ของท่านสำหรับใช้ลงชื่อเข้าใช้งาน: <b>{{$username}}</b></p>
        @endif
        <p>  {!! str_repeat('&nbsp;', '50') !!}
            <a href="{{ @$link  }}"
            style="display:inline-block;background:#007bff;color:#ffffff;font-family:Arial;font-size:14px;font-style:normal;font-weight:normal;line-height:100%;margin:0;text-decoration:none;text-transform:none;padding:8px 15px;border-radius:5px"
            target="_blank" >คลิกเพื่อยืนยันตัวตน</a>
        </p>

     @if ($check_api != 1)
      <p>เมื่อท่านยืนยันตัวตนผ่านทางอีเมลแล้ว กรุณารอเจ้าหน้าที่ตรวจสอบข้อมูลและเปิดใช้งานบัญชีผู้ใช้งานของท่านอีกครั้ง</p>
     @endif

        <p>ท่านสามารถใช้ username และ password ของท่าน (ตามที่ท่านลงทะเบียนไว้ในระบบ)</p>
        <br>
        <p>จึงเรียนมาเพื่อโปรดรับทราบ</p>

    </div>
</body>
</html>
