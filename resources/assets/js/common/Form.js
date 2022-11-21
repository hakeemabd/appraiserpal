CJMA.Form = function (exports, di) {
    var $ = di.get('jq'),
        loader = di.get('loader'),
        app = di.get('app'),
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
            formSelector: '.ajax-form,.entity-form'

        },
        config = {},
        forms = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        $.validator.setDefaults(config.validationDefaults);
    };

    exports.addForm = function (options) {
        var $form = options.form ? $(options.form) : $(config.formSelector),
            baseUrl = options.baseUrl || $form.attr('action'),
            modelId = options.modelId || null,
            method = options.method || (modelId === null ? 'POST' : 'PUT'),
            successUrl = options.successUrl || baseUrl,
            errorMessage = options.errorMessage || 'An error occurred',
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
        $form.submit(function (e) {
            e.preventDefault();

            if($form.valid()) {
                $.ajax(formOptions.getSubmitParams(baseUrl, modelId, method, $form)).done(onDone).fail(onFailure).always(onComplete);

                loader.show($form);
            }
        });
    };

    function emptyFn() {
        return true;
    }

    function getSubmitParams(baseUrl, modelId, method, $form) {
        return {
            url: baseUrl + (modelId !== null ? '/' + modelId : ''),
            type: method,
            data: $form.serialize(),
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
            if (formConfig.options.afterDone.apply(this, arguments) !== false) {
                app.navigate(response.redirect || formConfig.options.successUrl);
            }
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