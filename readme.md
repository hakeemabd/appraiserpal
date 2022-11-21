# Installation

- Install composer.
- Install the framework `$ composer install`. This *installs* dependencies. `composer update` actually updates them and
overwrites the `composer.lock` file. If you do update, commit it back.
- Install node.js (need version 0.12.7)
- Install gulp `$ npm install` (need version 3.5.2)
- node/npm  troubleshooting

Extra steps to configure on homestead (for tmake dev only):

$ homestead make
$ vagrant up
$ vagrant ssh
$ curl https://raw.githubusercontent.com/fideloper/Vaprobash/master/scripts/rvm.sh -O rvm.sh
$ chmod +x rvm.sh
$ ./rvm.sh
$ source /home/vagrant/.rvm/scripts/rvm
$ gem install bundler
$ bundler install
$ gulp

```
$ sudo npm install n -g
$ sudo n 0.12.7
$ sudo n (chose node version 0.12.7)
!! restart bash (new terminal session)
$ sudo npm install npm -g
$ npm install
```
- Create .env file

```
APP_ENV=local
APP_DEBUG=true
APP_KEY=wte5W6t4XDckqysxwSMRb11m2F1OyioM

DB_HOST=127.0.0.1
DB_DATABASE=ap-sys-la
DB_USERNAME=ap-system
DB_PASSWORD=ap-system

CACHE_DRIVER=file
SESSION_DRIVER=database
QUEUE_DRIVER=sync

REDIS_HOST=localhost
REDIS_PASSWORD=null
REDIS_PORT=6379

QUEUE_DRIVER=sqs
SQS_KEY=AKIAJUS32DZ4VS5ZJMQA
SQS_SECRET=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
SQS_QUEUE=https://sqs.us-east-1.amazonaws.com/100181786919/appraiser-dev
SQS_REGION=us-east-1

MAIL_DRIVER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=AKIAJDCFNXBJ3SPOX6VA
MAIL_PASSWORD=AsGpJP+LR88UW8zwBryuSR7k7TSdrKeYMSUdz0DD0zq/

CUSTOMER_HOST=ap-la.local
ADMIN_HOST=ap-la-admin.local
WORKER_HOST=ap-la-worker.local
```

- Run DB migration `$ php artisan migrate`
- Run seeder `php artisan db:seed --class=RolesSeeder`. If you get errors that class is not found, run `composer dumpautoload`
- Set up local hosts that correspond to the customer, admin and worker hosts. Hosts should point to `public` folder
- Compile assets `$ gulp`
- Run `php artisan queue:listen` in background.

# Run it

- Run `php artisan migrate:refresh`. If it fails, remove all tables from the db and run again.
- Run `php artisan db:seed`. This outputs the list of newly added CUSTOMERS. Log in with password=1234.

# Authentication

There is no auth &amp; auth at the moment :-)

# Security

All requests pass through the CSRF filter. CSRF token is rendered into a meta tag like this:

```
<meta name="csrf-token" content="{{ csrf_token() }}"/>
```

Use it to set up default AJAX header. With jQuery it is done this way:

```
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```
If you use customer's, admin's or worker's parts JS located in `resources/assets/js/common` and boot the app like here:
`resources/assets/js/admin/boot.js`, you are already all set. If there is an issue with CSRF validation, check if there is
a meta tag in the view.

# Views

View are located in `resources/views`. Place your assets into `resources/assets`.

Views for each site - admin, worker and customer reside in the correspondent subfolders.

Here's how we set view path:

* Environment detection (what site we use and load site-specific configs) is performed in `bootstrap/environment.php`
* `bootstrap/environment.php` is included in bootstrap/app.php
* We use this env variable in `config/view.php` to set the path

This approach works fine when we detect environment based on the HTTP host which is available when user views the pages.
When we run app as a console command, we don't have this variable, so we can't determine the view path reliably.
So we just go with the default one.
This presents a problem to any CLI command that is trying to use view. For example, for the background email processing.

# File storage

Since we don't need to manipulate files most of the time, just store them and show, we can upload them directly to S3
and provide users with the links to download them securely.
Here's how it works:

 * We create an S3 bucket. This bucket should have CORS headers enabled for our domains (customer, admin and worker sites)
 CORS config looks like:

 ```
 <?xml version="1.0" encoding="UTF-8"?>
 <CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
     <CORSRule>
         <AllowedOrigin>https://angular-file-upload.appspot.com</AllowedOrigin>
         <AllowedMethod>POST</AllowedMethod>
         <AllowedMethod>GET</AllowedMethod>
         <AllowedMethod>HEAD</AllowedMethod>
         <MaxAgeSeconds>3000</MaxAgeSeconds>
         <AllowedHeader>*</AllowedHeader>
     </CORSRule>
 </CORSConfiguration>
 ```

 * We have a special user that can upload files to S3. He performs it using https://github.com/danialfarid/ng-file-upload#s3
   On a backend we could use some other plugin to do it or use this one as well.
 * We add this user to the bucket policy:

 ```
 {
     "Version": "2012-10-17",
     "Statement": [
         {
             "Sid": "UploadFile",
             "Effect": "Allow",
             "Principal": {
                 "AWS": "arn:aws:iam::100181786919:user/appraiser-s3-uploader"
             },
             "Action": "s3:PutObject",
             "Resource": "arn:aws:s3:::appraiserpal/*"
         },
         {
             "Sid": "RetrieveFile",
             "Effect": "Allow",
             "Principal": {
                 "AWS": "arn:aws:iam::100181786919:user/appraiser-s3-downloader"
             },
             "Action": "s3:GetObject",
             "Resource": "arn:aws:s3:::appraiserpal/*"
         },
         {
             "Sid": "crossdomainAccess",
             "Effect": "Allow",
             "Principal": "*",
             "Action": "s3:GetObject",
             "Resource": "arn:aws:s3:::appraiserpal/crossdomain.xml"
         }
     ]
 }
 ```

 * We define policy for uploading files to the bucket that looks like this:

 ```
 {
     "expiration": "2020-01-01T00:00:00Z",
     "conditions": [
         {"bucket": "angular-file-upload"},
         ["starts-with", "$key", ""],
         {"acl": "private"},
         ["starts-with", "$Content-Type", ""],
         ["starts-with", "$filename", ""],
         ["content-length-range", 0, 524288000]
     ]
 }
 ```

 We sign this policy using our secret key for the user who can upload files. We do this on the server side when we
 generate the page.
 We could include all our file type and file size validation in these policies.
 This information for all possible file types is produced by `AttachmentRepository::getUploadConfig`.
 `ManagerController::create` renders this info into the view in a `FILE_UPLOAD_CONFIG` JS variable. Angular frontend
 uses info from there to configure the file upload control. Structure of this variable is described in the comment to
 the `AttachmentRepository::getUploadConfig` method.
 * When file is uploaded, we pass it's Amazon S3 key to the backend when we submit the step. Files passed to the backend
 are named after the file type. E.g. `data_file_mobile` field should contain S3 key. When editing, `data_file_mobile_id`
 should contain attachment's id (it is passed from the server)
 * We create another user for downloading files. We add him to the policy to the s3 bucket with download-only permission.
 * On the server-side when we need to show an image, we generate the time-bund link (e.g. 20 mins or 1h) that is specific
 for this user and this file. This is done in the following way:

 ```
 $s3Client = new S3Client([
    'region'  => 'us-east-1',
    'version' => 'latest',
    'credentials' => [
        'key' => 'downloader-user-key',
        'secret' => 'downloder-user-secret'
    ]
 ]);

 $cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => 'appraiserpal',
    'Key'    => 'file-name'
 ]);
 $request = $s3Client->createPresignedRequest($cmd, '+20 minutes'); //timeout
 echo $presignedUrl = (string) $request->getUri()."\n";
 ```

 * This is actually automated by `App\Components\S3StorableTrait`. Model uses it and should define `getS3Key` method that
 returns the key. It is used by the trait (which uses `App\Components\AwsS3Policy` internally, which uses S3Client) to
 generate the public link. Trait provides `getS3Url` method that returns public link with 20 ins lifetime. One may pass
 different lifetime.
 This way we can show all images and links to files securely without actually serving the files ourselves.
 If we need to upload files from backend, it works simply: `$s3Client->upload('appraiserpal', 'test.jpg', file_get_contents('/Users/konst/Pictures/IQRIA-wallpaper-nsfw_rev3_FullHD.jpg'));`

 Same applies to downloading etc.
 For image resizing we just use Amazon Lambda

# Coding

* [https://bosnadev.com/2015/03/07/using-repository-pattern-in-laravel-5/]()

## Mailing

We do all emailing by adding the `MailJob` to queue. This job accepts the same arguments as `Mail::queue` and works in the
same way. The only point to use it is to solve the problem with views that we have because of the 3 sites on one
framework installation.

```
$this->dispatch(new MailJob('emails.recoverPass', compact('user', 'reminder'), function ($m) use ($user) {
    $m->to($user->email)->subject('Appraiser Pal password recovery');
}));
```

**IMPORTANT** In order for mailing to work, run `php artisan queue:listen`

## Non-angular JS structure

All JS is located here: `resources/assets/js`. Scripts specific for admin, customer and worker sites are in the
respective views.
There is a set of base modules located here: `resources/assets/js/common`:

* **App** sets up interface and initializes controls. It can be customized by providing selectors. selector=null disables
initialization. See default config here: `resources/assets/js/common/App.js`
* **Loader** shows loader. Allows substantial change to the default loader, see loader initialization here:
`resources/assets/js/customer/init.js`. Here's how it works:
 * Add class to element's parent (to set position:relative etc)
 * Adds mask element
 * Places loader HTML into the mask
 * Shows and hides this where appropriate. When showing/hiding it adds pre-configured activateClass to the loader itself.
* **Form** handles form submission, validation errors and messages. It is possible to add a number of forms to be handled.
 When adding a form, you should specify the following config (all fields are optional):
 * `form`. Defaults to `".ajax-form,.entity-form"`. Anything that `$()` accepts: selector, DOM element or other jQuery object.
 * `baseUrl`. Defaults to form's `action` attribute. URL for submission. Will be used to generate RESTful URL if modelId is passed.
 * `modelId`. Defaults to `null`. ID of the model (if applies). Used for generating submission URL and method (POST/PUT)
 * `method`. Defaults to `POST` (or `PUT` if modelId is defined). HTTP method to use with the query
 * `successUrl`. Defaults to `baseUrl`. Where user should go after successful submission. If you pass {redirect:'your_url'},
  user will be redirected there.
 * `errorMessage`. Defaults to `"An error occurred"`. Default error message to be shown in a Material toast or similar place.
* **DI** (dependency injection container) handles dependency resolution. A very very basic container for existing
modules and dependencies that allows configuring modules in a flexible way. `add` method

These modules should be tied together in a booting process. It happens in the following way:

1. DI is already defined, so we just use it.
1. Add core dependencies to the DI
1. Create app and add to DI
1. Use Loader and Form to add these modules as app.loader and app.form.
1. Add them to the DI

When everything is booted, we should initialize modules. Typically modules expose `init()` method that accepts configs
that alter module behavior. In the basic scenario you should just run them one by one.
DI container may run it for you if you set `autoInit` property to true as shown in the `resources/assets/js/admin/boot.js`.
In this case you don't need init script. This does not fit if you need to pass options to `init()`

### How can I use forms?

Easy!

1. Define form. See example here: `resources/views/admin/auth/recoverPassword.blade.php`
1. Add form to the `Form` module for processing like this:

```
@push('scripts')
<script type="text/javascript">
    CJMA.DI.get('form').addForm({
        form: '.auth-form',
        errorMessage: 'Can\'t recover your password. Please check your email.'
    });
</script>
@endpush
```

### How to define own modules?

* Follow this structure:

```
CJMA.YourModule = function (exports, di) {
    var defaultConfig = {
        //your defaults here
        },
        config,
        $ = di.get('jq'),
        other = di.get('otherModule');

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        //other initialization logic
    };

    exports.publicMethod = function() {
        privateMethod();
        other.otherPublicMethod();
    }

    exports.publicMethod2 = function () {
        privateMethod();
    }

    function privateMethod() {

    }

    return exports;
};
```

* Add this to the `gulpfile.js` build
* Include it in the boot `di.add('mymodule', CJMA.YourModule({}, di))`

* Use it in the page: `CJMA.DI.get('mymodule').publicMethod();`