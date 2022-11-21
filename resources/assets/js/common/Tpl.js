CJMA.Tpl = function (exports, di) {
    var $ = di.get('jq'),
        defaultConfig = {},
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
    };

    exports.modalTpl = _.template([
        '<div class="modal fade <%= cls %>" tabindex="-1" role="dialog">',
        '    <div class="modal-dialog">',
        '       <div class="modal-content">',
        '           <div class="modal-header">',
        '               <button type="button" class="close" data-dismiss="modal" aria-label="Close">',
        '                   <span aria-hidden="true">&times;</span>',
        '                </button>',
        '               <h4><%= title %></h4>',
        '           </div>',
        '           <div class="modal-body">',
        '               <%= content %>',
        '           </div>',
        '           <% if (footer != false) { %>',
        '              <div class="modal-footer">',
        '                   <%= footer %>',
        '               </div>',
        '           <% } %>',
        '       </div>',
        '   </div>',
        '</div>',
    ].join(''));

    return exports;
};
