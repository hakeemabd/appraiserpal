const baseURL = $('meta[name="base_url"]').attr('content');
const csrfToken = $('meta[name="csrf_token"]').attr('content');

$(document).on('submit', '#ajax-form', async function (e) {

    e.preventDefault();
    let url = $(this).attr('action');
    let method = $(this).attr('method');
    let data = $(this).serialize();
    let table = $(this).attr('data-table');
    let notification = $(this).attr('data-notification');
    let file = $(this).attr('data-file');
    if (file == 'true') {
        data = new FormData(this);
    } else {
        file == 'false';
    }

    let btn = $(this).find('button[type=submit]');
    let originalText = btn.html();

    $('div#message-area').html('');
    try {
        let response = await doAjaxPost(url, method, data, file);

        let formModal = $(this).closest('.modal');

        if (formModal.length != 0) {
            formModal.modal('hide');
        }

        let message = showMsg(response.message, '', response.result);
        if (notification == 'div') {
            $('div#message-area').html(message);
        } else {


            notify(response.message, response.result);
        }


        $('table#' + table).DataTable().ajax.reload();
        resetButton(btn, originalText);
    } catch (err) {
        let message = showMsg(err, '', 'danger');
        if (notification == 'div') {
            $('div#message-area').html(message);
        } else {
            notify(err, 'error');
        }
        resetButton(btn, originalText);
    }
});

$(document).on('submit', 'form', function () {

    let btn = $(this).find('button[type=submit]');
    let loadingText = btn.attr('data-loading-text');
    let loaderNeeded = btn.attr('data-loader-needed');

    if (loaderNeeded != 'false')
        loadButton(btn, loadingText);
});

$(document).on('click', '.load-modal', function () {
    let url = $(this).attr('data-url');
    console.log(url);
    $('#modal').load(url, function () {
        let modal = new bootstrap.Modal(document.getElementById('modal'));
        modal.show();
    });
});

$(document).on('shown.bs.modal', '.modal', function () {
    let $this = $(this);
    $(this).find('.select2').select2({
        dropdownParent: $this
    });
});

function initDataTable(table, columns, formId, aaSorting = [],
    columnDefs = [], pageLength = 5,
    lengthMenu = [
        [5, 50, 100, 500, 1000, -1],
        [5, 50, 100, 500, 1000, 'All']
    ]) {
    let url = table.attr('data-url');

    return new Promise(function (resolve, reject) {
        let t = table.DataTable({
            processing: true,
            serverSide: true,
            retrieve: true,
            columnDefs: columnDefs,
            ajax: {
                url: url,
                data: function (d) {
                    d.form_data = $('#' + formId).serialize()
                }
            },
            pageLength: pageLength,
            lengthMenu: lengthMenu,
            aaSorting: aaSorting,
            columns: columns,
            responsive: true,
            processing: false,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_ items/page',
            },
            initComplete: function () {

                $('.dataTables_length select').select2({
                    minimumResultsForSearch: Infinity
                });
                resolve(t);
            },
            fnDrawCallback: function () {
                if (table.find('[data-toggle="tooltip"]').length > 0) {
                    table.find('[data-toggle="tooltip"]').tooltip();
                }
            }
        });
    });

}

function doAjaxPost(url, method, data, file) {

    let encType = '';
    let contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
    let processData = '';
    if (file == 'true') {
        encType = 'multipart/form-data';
        contentType = false;
        processData = false;
    }

    return new Promise(function (resolve, reject) {
        $.ajax({
            type: method || "POST",
            url: url,
            dataType: 'json',
            contentType: contentType,
            enctype: encType,
            processData: processData,
            data: data || {},
            success: function (data) {
                resolve(data);
            },
            error: function (jqXHR, exception) {
                let msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.Verify Network.';
                    console.log('Not connect.Verify Network.');
                } else if (jqXHR.status === 404) {
                    console.log('Requested page not found. [404]');
                    msg = 'Requested page not found';
                } else if (jqXHR.status === 422) {
                    console.log('Requested page status. [422]');
                    if (jqXHR.hasOwnProperty('responseJSON')) {
                        msg += jqXHR.responseJSON.message;
                        let errors = jqXHR.responseJSON.hasOwnProperty('errors') ? jqXHR.responseJSON.errors : null;
                        if (!isEmpty(errors)) {
                            let i = 0;
                            for (let key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    msg += "<br/>\u2022" + errors[key];
                                }
                                i++;
                            }
                        }
                    }
                } else if (jqXHR.status === 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }

                reject(msg);
            },
        });
    })
}

let showMsg = (msg, bold, classType) => {
    let alert = `
        <div class="alert alert-${classType}" role="alert">`;

    if (bold != '') {
        alert += `<strong class="d-block d-sm-inline-block-force">${bold}!</strong>&nbsp`;
    }

    alert += `${msg}
            </div>
    `;

    return alert;
};

let loadModal = function (modalId, url) {
    $('#' + modalId).load(url, function () {
        $('#' + modalId).modal('show');
    });
};

let notify = function (text, type, title) {
    let color = 'rgba(0,0,255,0.3)';
    if (type == 'success') {
        color = 'rgba(0,255,0,0.3)';
    } else if (type == 'error') {
        color = 'rgba(255,0,0,0.3)';
    } else if (type == 'warning') {
        color = 'rgba(255,255,0,0.3)';
    }

    $.NotificationApp.send(title, text, "bottom-right", color, type)
}

let isEmpty = str => {
    return (!str || str.length === 0 || str === '' || str.length === 0 || typeof str === undefined || str === null);
};

let loadButton = (btn, loadingText = 'Submitting...') => {
    var loadingText = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ' + loadingText;
    btn.prop('disabled', true);
    btn.html(loadingText);
}

let resetButton = (btn, originalText = 'Submit') => {
    btn.prop('disabled', false);
    btn.html(originalText);
}

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
