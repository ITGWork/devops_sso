@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
          {{-- {{ config('app.name') }} --}}
          ระบบบริการอิเล็กทรอนิกส์ สมอ.
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            {{-- &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved. --}}
            © {{ date('Y') .' ระบบบริการอิเล็กทรอนิกส์ สมอ. All rights reserved.'}} 
        @endcomponent
    @endslot
@endcomponent
