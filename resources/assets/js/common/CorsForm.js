CJMA.CorsForm = function (exports, di) {
    var $ = di.get('jq'),
        loader = di.get('loader'),
        app = di.get('app'),
        datagrid = di.get('datagrid'),
        defaultConfig = {
            validationDefaults: {
                errorClass: 'invalid',
                validClass: "valid",
                errorPlacement: function (error, element) {
                    var el = $(element),
                        elWrapper = $(el.parent());
                    el.closest("form").find("label[for='" + element.attr("id") + "']").attr('data-error', error.text());

                    elWrapper.addClass('field-error');
                }
            },
            formSelector: '.cors-form',
            s3Key: 'input[name="key"]',
            s3ContentType: 'input[name="Content-Type"]',
            s3FileName: '.file-path-wrapper [type="text"]'

        },
        config = {},
        forms = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        $.validator.setDefaults(config.validationDefaults);
    };

    exports.addForm = function (options) {
        initDataTable();
        var $form = options.form ? $(options.form) : $(config.formSelector),
            baseUrl = options.baseUrl || $form.attr('action'),
            modelId = options.modelId || null,
            method = options.method || (modelId === null ? 'POST' : 'PUT'),
            serverUrl = options.serverUrl || baseUrl,
            successUrl = options.successUrl || baseUrl,
            errorMessage = options.errorMessage || 'An error occurred',
            fileName,
            formOptions = {
                baseUrl: baseUrl,
                modelId: modelId,
                method: method,
                successUrl: successUrl,
                errorMessage: errorMessage,
                beforeDone: options.beforeDone || emptyFn,
                afterDone: options.afterDone || emptyFn,
                beforeError: options.beforeError || emptyFn,
                afterError: options.afterError || emptyFn,
                getSubmitParams: options.getSubmitParams || getSubmitParams
            },
            validator;
        $form.data('form-id', Math.random());
        if (typeof $.fn.validate == 'function') {
            validator = $form.validate();
        }
        else {
            console.warn('Form error messages depend on jQuery.validate plugin');
        }
        forms[$form.data('form-id')] = {
            form: $form,
            validator: validator,
            options: formOptions
        };

        $form.fileupload({
            url: baseUrl,
            type: method,
            autoUpload: true,
            dataType: 'xml',
            add: function (event, data) {
                fileName = options.folder
                    + Math.random().toString(36).slice(2) + "_"
                    + data.files[0].name;

                $form.find(defaultConfig.s3Key).val( fileName );
                $form.find(defaultConfig.s3ContentType).val(data.files[0].type);
                $form.find(defaultConfig.s3FileName).val(data.files[0].name);

                $form.off('submit').on('submit',function (e) {
                 
                    e.preventDefault();
                    if($form.valid() && $form.find('.file-path-wrapper [type="text"]').val() != '') {
                        data.submit();
                    }
                });
            },
            fail: function(e, data) {
                var formConfig = forms[$(this).data('form-id')];
                displayErrors(data.jqXHR.responseText, formConfig);
                loader.hide(formConfig.form);
            },
            send: function() {
                loader.show($form);
            },
            success: function(data) {
                var data = $form.serializeArray();
                for(var i in options.extraFields) {
                    data.push({
                        name: options.extraFields[i].name,
                        value: options.extraFields[i].value.val()
                    });
                }
                
                $.ajax(formOptions.getSubmitParams(serverUrl, modelId, method, $form, data)).done(onDone).fail(onFailure).always(onComplete);
            }

        });
    };

    function initDataTable() {
        datatables = datagrid.addGrid({ responsive: true });
    }

    function emptyFn() {
        return true;
    }

    function getSubmitParams(baseUrl, modelId, method, $form, data) {
        return {
            url: baseUrl + (modelId !== null ? '/' + modelId : ''),
            type: method,
            data: data,
            dataType: 'json',
            context: $form
        };
    }

    function onDone(response) {
        var formConfig = forms[this.data('form-id')];
        if (formConfig.options.beforeDone.apply(this, arguments) === false) {
            return false;
        }

        if (!response) {
            console.warn('Did not get any response');
        }
        if (response.success === false) {
            displayErrors(response, formConfig);
            formConfig.options.afterDone.apply(this, arguments);
        }
        else {
            onComplete();
            $('#upload_documents_datatable').DataTable().ajax.reload();
            $('#public_comment_datatable').DataTable().ajax.reload();
            $('#private_comment_datatable').DataTable().ajax.reload();
            formConfig.form[0].reset();
            if (document.getElementById("modal-upload")) {
                $('#modal-upload').closeModal();
            };
            if (document.getElementById("modal-comment")) {
                $('#modal-comment').closeModal();
            };
            if (document.getElementById("modal-comment-customer")) {
                $('#modal-comment-customer').modal('hide');
                location.reload();
            };
        }
    }

    function onFailure(xhr) {
        var formConfig = forms[this.data('form-id')];
        if (formConfig.options.beforeError.apply(this, arguments) === false) {
            return false;
        }
        if (!xhr.responseJSON) {
            app.msg('An error occurred');
            formConfig.options.afterError.apply(this, arguments);
            return;
        }
        displayErrors(xhr.responseJSON, forms[this.data('form-id')]);
        formConfig.options.afterError.apply(this, arguments);
    }

    function onComplete() {
        loader.hide(this);
    }

    function displayErrors(json, formConfig) {
        var errors = {},
            message = formConfig.options.errorMessage;
        if (json.errors) { //errors may be in the response.errors property or simply in response root object
            errors = json.errors;
            message = json.message || message;
        }
        else {
            errors = json;
        }
        if (typeof errors == 'string') {
            message = errors;
            errors = null;
        }
        if (errors) {
            if (formConfig &&
                formConfig.validator &&
                typeof formConfig.validator.showErrors == 'function') {

                formConfig.validator.showErrors(errors);
            }
            else {
                console.warn("Could not show existing errors for the form", this);
            }
        }
        app.msg(message);
    }
};