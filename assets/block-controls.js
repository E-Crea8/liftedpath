/* LiftedPath block visibility and insert defaults in the editor. */
(function (wp) {
	if (!wp || !wp.hooks || !wp.element || !wp.blockEditor || !wp.components || !wp.compose) {
		return;
	}

	var el = wp.element.createElement;
	var addFilter = wp.hooks.addFilter;
	var createHigherOrderComponent = wp.compose.createHigherOrderComponent;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var ToggleControl = wp.components.ToggleControl;
	var __ = wp.i18n && wp.i18n.__ ? wp.i18n.__ : function (text) { return text; };

	function isLiftedPath(name) {
		return typeof name === "string" && name.indexOf("liftedpath/") === 0;
	}

	/**
	 * Use each block's example payload as the default attributes on insert,
	 * so the canvas is filled instead of an empty placeholder.
	 */
	addFilter("blocks.registerBlockType", "liftedpath/example-defaults", function (settings, name) {
		if (!isLiftedPath(name) || !settings || !settings.example || !settings.example.attributes) {
			return settings;
		}

		var example = settings.example.attributes;
		var attributes = Object.assign({}, settings.attributes || {});

		if (example.data && typeof example.data === "object") {
			attributes.data = Object.assign({}, attributes.data || {}, {
				type: "object",
				default: example.data,
			});
		}

		if (typeof example.mode === "string") {
			attributes.mode = Object.assign({}, attributes.mode || {}, {
				type: "string",
				default: example.mode,
			});
		}

		settings.attributes = attributes;
		return settings;
	});

	var withVisibility = createHigherOrderComponent(function (BlockEdit) {
		return function (props) {
			if (isLiftedPath(props.name)) {
				return el(BlockEdit, props);
			}

			var shown = props.attributes.liftedpathShow !== false;

			return el(
				wp.element.Fragment,
				null,
				el(BlockEdit, props),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __("Visibility", "liftedpath"), initialOpen: true },
						el(ToggleControl, {
							label: __("Show on the site", "liftedpath"),
							help: __("Hide removes this block from the public site. It stays in the editor.", "liftedpath"),
							checked: shown,
							onChange: function (value) {
								props.setAttributes({ liftedpathShow: value });
							},
						})
					)
				)
			);
		};
	}, "withLiftedPathVisibility");

	addFilter("editor.BlockEdit", "liftedpath/visibility", withVisibility);

	var withHiddenClass = createHigherOrderComponent(function (BlockListBlock) {
		return function (props) {
			if (isLiftedPath(props.name) || props.attributes.liftedpathShow !== false) {
				return el(BlockListBlock, props);
			}

			var className = [props.className, "liftedpath-is-hidden"].filter(Boolean).join(" ");
			return el(BlockListBlock, Object.assign({}, props, { className: className }));
		};
	}, "withLiftedPathHiddenClass");

	addFilter("editor.BlockListBlock", "liftedpath/visibility-class", withHiddenClass);
})(window.wp);
