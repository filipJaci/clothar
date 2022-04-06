@component('mail::message')
# Welcome to {{ config('app.name') }}!

Thank you for registering at <strong>{{ config('app.name') }}</strong>.
To continue, please verify your account by clicking the button bellow:

@component('mail::button', ['url' => config('app.url').'/verify/'.$token])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
