@component('mail::message')
    <h2>Chào {{ $name }},</h2>
    <p>Bạn có thể xem video timelapse bằng cách nhấp vào đường dẫn dưới đây:</p>

    @component('mail::button', ['url' => config('app.url_front') . '?r=timelapse'])
        Link video
    @endcomponent

    <p>Nếu bạn có bất kỳ câu hỏi hoặc ý kiến nào về video này, hãy cho mình biết nhé!<br></p>

    Thanks,<br>
    {{ config('app.name') }}<br>
@endcomponent
