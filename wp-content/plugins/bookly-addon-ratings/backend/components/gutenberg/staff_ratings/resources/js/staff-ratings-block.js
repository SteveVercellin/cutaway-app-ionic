(function (wp) {
    var el = wp.element.createElement,
        components        = wp.components,
        blockControls     = wp.editor.BlockControls,
        inspectorControls = wp.editor.InspectorControls
    ;


    wp.blocks.registerBlockType('bookly/staff-ratings', {
        title: BooklyStaffRatingsL10n.block.title,
        description: BooklyStaffRatingsL10n.block.description,
        icon: el('svg', { width: '20', height: '20', viewBox: "0 0 64 64" },
            el('path', {style: {fill: "rgb(0, 0, 0)"}, d: "M 8 0 H 56 A 8 8 0 0 1 64 8 V 22 H 0 V 8 A 8 8 0 0 1 8 0 Z"}),
            el('path', {style: {fill: "rgb(244, 102, 47)"}, d: "M 0 22 H 64 V 56 A 8 8 0 0 1 56 64 H 8 A 8 8 0 0 1 0 56 V 22 Z"}),
            el('rect', {style: {fill: "rgb(98, 86, 86)"}, x: 6, y: 6, width: 52, height: 10}),
            el('rect', {style: {fill: "rgb(242, 227, 227)"}, x: 12, y: 30, width: 40, height: 24}),
            el('path', {style: {fill: "rgb(124, 252, 0)", stroke: 'rgb(0, 0, 0)'}, d: "M 44.49 33.766 L 48.23 44.525 L 59.618 44.757 L 50.541 51.639 L 53.84 62.542 L 44.49 56.036 L 35.14 62.542 L 38.439 51.639 L 29.362 44.757 L 40.75 44.525 Z"}),
        ),
        category: 'bookly-blocks',
        keywords: [
            'bookly',
            'ratings',
        ],
        supports: {
            customClassName: false,
            html: false
        },
        attributes: {
            short_code: {
                type: 'string',
                default: '[bookly-staff-rating]'
            },
            hide_comment: {
                type: 'boolean',
                default: false
            },
        },
        edit: function (props) {
            var inspectorElements = [],
                attributes   = props.attributes
            ;

            function getShortCode(props, attributes) {
                var short_code = '[bookly-staff-rating';

                if (attributes.hide_comment) {
                    short_code += '  hide="comment"';
                }

                short_code += ']';

                props.setAttributes({short_code: short_code});

                return short_code;
            }

            inspectorElements.push(el(components.PanelRow,
                {},
                el('label', {htmlFor: 'bookly-js-hide-comment'}, BooklyStaffRatingsL10n.comment),
                el(components.FormToggle, {
                    id: 'bookly-js-hide-comment',
                    checked: attributes.hide_comment,
                    onChange: function () {
                        return props.setAttributes({hide_comment: !props.attributes.hide_comment});
                    },
                })
            ));

            return [
                el(blockControls, {key: 'controls'}),
                el(inspectorControls, {key: 'inspector'},
                    el(components.PanelBody, {initialOpen: true},
                        inspectorElements
                    )
                ),
                el('div', {},
                    getShortCode(props, props.attributes)
                )
            ]
        },

        save: function (props) {
            return (
                el('div', {},
                    props.attributes.short_code
                )
            )
        }
    })
})(
  window.wp
);