@extends('customer.emails.layout', ['hasAction' => false])

@section('body')
Your appraisal report is complete. Please see the deliverables attached in this email. Please
also sign in to your dashboard at <a class="text-primary" href="{{ $host }}" target="_blank">www.appraiserpal.com</a> to mark this appraisal as
complete or if needed, request revisions.
<br>
<br>
To mark this order as complete, mark the checkbox next to the order, under "my orders" of
your dashboard.
<br>
<br>
If you need corrections, you do not have to manually send the report back to us as we already
have the last version of the report. Simply click the left arrow next to your order and fill in the
instructions box with your revision request. If you need to add any photos or other
attachment, use the attachment link in the instruction box.
<br>
<br>
Please login into 
<a class="text-primary" href="{{ $host }}" target="_blank">appraiser pal</a> to view or manage your completed order.
<br>
<br>
Thank you for your continued business, we are here to serve your appraisal report needs!
<br>
<br>
<br>
Appraiser Pal
<br>
info@appraiserpal.com
@endsection

