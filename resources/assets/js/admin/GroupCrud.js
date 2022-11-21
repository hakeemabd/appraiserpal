CJMA.GroupCrud = function (exports, di) {
    var $ = di.get('jq'),
        app = di.get('app'),
        form = di.get('form'),
        datagrid = di.get('datagrid'),
        defaultConfig = {
            route: 'workerGroup/\\d+/assignedUsers',
            assignForm: '#assign-modal .group-assignment',
            assignEditForm: '#assign-edit-modal .group-assignment',
            assignModal: '#assign-modal',
            assignEditModal: '#assign-edit-modal',
            assignBtn: '#assign-modal .modal-action',
            assignEditBtn: '#assign-edit-modal .modal-action',
            tableSelector: ".assigned-users",
            editActionSelector: ".assigned-users [data-action=edit]",
            deleteActionSelector: ".assigned-users [data-action=delete]"
        },
        config = {},
        datatable = null;

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
        if (app.routeMatches(config.route) && true) {
            initHandlers();
            initDataTable();
        }
    };

    function initHandlers() {
        form.addForm({
            form: config.assignForm,
            afterDone: afterDone,
            successUrl: window.location.toString()
        });
        form.addForm({
            form: config.assignEditForm,
            afterDone: afterDone,
            successUrl: window.location.toString()
        });
        $(config.assignBtn).click(function () {
            $(config.assignForm).submit();
        });
        $(config.assignEditBtn).click(function () {
            $(config.assignEditForm).submit();
        });
    }

    function onEditClick($btn, data) {
        var $editModal = $(config.assignEditModal);

        $("[name=user_id]", $editModal).val(data.id);
        $("[name=fee]", $editModal).val(data.fee);
        $("[name=second_fee]", $editModal).val(data.second_fee);
        $("[name=first_turnaround]", $editModal).val(data.first_turnaround);
        $("[name=next_turnaround]", $editModal).val(data.next_turnaround);
        $("label", $editModal).addClass("active");
        $("h4 span", $editModal).html(data.fullName);
        $editModal.openModal();
    }

    function initDataTable() {
        datatable = datagrid.addGrid({
            success: {
                'delete': 'User is removed from group'
            },
            error: {
                'delete': 'Error removing user from group'
            },
            editHandler: onEditClick
        })[0];
    }

    function afterDone() {
        $(this).parents('.modal').closeModal();
        datatable.draw();
        return false;
    }

    return exports;
};