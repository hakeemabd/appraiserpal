@extends('emails.layout', ['hasAction' => true])

@section('body')
You are now going to experience an innovative approach to appraisal writing! I think we all
know that having an experienced appraiser to work on appraisal reports, at any given time, is
indispensable. Here are some questions that you may have when getting started:
<br>
<br>
How do I create a new order? Create your first order by logging into your account, going
under "My Orders" and clicking “Create Order”. You will be taken to the appraisal report order
Wizard, where you will submit information such as your data, field sheets, templates, photos,
etc. Try using Live Chat Support on the website if you have any questions when creating your
first order.
<br>
<br>
What do I do after I create my new order? : By viewing your dashboard under "My Orders";
you will be able to keep track of the time remaining before receiving your appraisal report
back. If you have any comments or additional information to add, write a comment in the
comments section of the order.
<br>
<br>
What if I forget something after I placed my order? Should we have any questions or
comments about the appraisal report, or if you have any questions or comments about the
appraisal report, you may see them from your email and respond to them from the order on
your dashboard. You can do everything on your mobile device!
<br>
<br>
What about my completed reports? You will receive completed orders to your dashboard
before the timer on your dashboard has ran out. You will also receive a notification email
when you appraisal reports are complete. You can send appraisal reports back by simply
clicking the left arrow next to your order, and providing us with revision questions.
<br>
<br>
We look forward to being your new virtual office team and creating a new way to love your
appraisal profession!
<br>
<br>
Thank you, we appreciate you!
<br>
<br>
<br>
Appraiser Pal
<br>
info@appraiserpal.com
@endsection
@section('action')
    <p>Please activate your account by visiting <a
                href="{{ route('customer:confirmSignup', ['email' => $user->email, 'code' => $activation->code]) }}"
                target="_blank">this
            link</a></p>
    <p>If you have troubles clicking the link, copy and paste this in your browser:<br/>
        {{ route('customer:confirmSignup', ['email' => $user->email, 'code' => $activation->code]) }}
    </p>
@endsection

