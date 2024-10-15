@component('mail::message')
    <h2>Hi {{ $name }},</h2>
    <p>You can watch the timelapse video by clicking on the link below:
    </p>

    @component('mail::button', ['url' => config('app.url_front') . '?r=timelapse'])
        Link video
    @endcomponent

    <p>If you have any questions or comments about this video, let me know!<br></p>

    Thanks,<br>
    {{ config('app.name') }}<br>
@endcomponent
