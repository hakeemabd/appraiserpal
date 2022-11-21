CJMA.TplMaterial = function (exports, di) {
    var $ = di.get('jq'),
        defaultConfig = {},
        config = {};

    exports.init = function (options) {
        config = $.extend({}, defaultConfig, options);
    };

    exports.modalTpl = _.template([
        '<div class="modal <%= cls %>">',
        '       <div class="modal-content">',
        '           <div class="modal-header">',
        '               <a href="#" class="modal-action modal-close">',
        '                   <span aria-hidden="true">&times;</span>',
        '               </a>',
        '               <h4><%= title %></h4>',
        '           </div>',
        '           <div class="modal-body">',
        '               <%= content %>',
        '           </div>',
        '       </div>',
        '       <% if (footer != false) { %>',
        '       <div class="modal-footer">',
        '           <%= footer %>',
        '       </div>',
        '       <% } %>',
        '</div>',
    ].join(''));

    return exports;
};
