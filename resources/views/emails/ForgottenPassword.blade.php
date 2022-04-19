@component('mail::message')
# Password reset on {{ config('app.name') }}!

You've requested a password reset on <strong>{{ config('app.name') }}</strong>. If you didn't make such a request, feel free to ignore this email.
If you wish to reset your password, you may do so by clicking the button bellow:

@component('mail::button', ['url' => config('app.url').'/forgot-password/'.$token])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
