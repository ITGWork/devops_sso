@push('css')
  <style>
    .container-save {
      display: flex;
      /* justify-content: center; */
      align-items: center;
      flex-direction: column;
      height: 600px;
      gap: 2rem;
    }
    .container-save > h1 {
      margin-top: 100px;
    }
  </style>
@endpush

<div class="container-save">

    <h1>ขอบคุณสำหรับข้อเสนอแนะ ครับ/ค่ะ</h1>
    <a class="btn btn-primary" href="{{url('/')}}">
      </i> กลับไปหน้าหลัก
    </a>

</div>