/**
 * WordPress dependencies.
 */
import { registerBlockType } from "@wordpress/blocks";
// import { CheckboxControl, TextControl } from "@wordpress/components";
// import { useSelect } from "@wordpress/data";
// import { useEntityProp } from "@wordpress/core-data";
import { useBlockProps } from "@wordpress/block-editor";
import { useEffect, useState } from "@wordpress/element";

registerBlockType("btoc/preview-block", {
  edit: () => {
    const [utocHtml, setUtocHtml] = useState("");
    const blockProps = useBlockProps();
    // const postType = useSelect(
    //   (select) => select("core/editor").getCurrentPostType(),
    //   []
    // );

    // const [meta, setMeta] = useEntityProp("postType", postType, "meta");

    // const metaFieldValue = meta["utoc_title"];
    // const updateMetaValue = (newValue) => {
    //   setMeta({ ...meta, utoc_title: newValue });
    // };

    // const metaLevels = (meta["utoc_level"]
    //   ? meta["utoc_level"]
    //   : [1, 2, 3, 4, 5, 6]
    // ).map(Number);

    // const updateLevels = (level) => {
    //   level = +level;

    //   setMeta({
    //     ...meta,
    //     utoc_level: metaLevels.includes(level)
    //       ? metaLevels.filter((item) => item !== level)
    //       : [...metaLevels, level],
    //   });
    // };

    useEffect(() => {
      get_utoc_html().then((data) => {
        setUtocHtml(data);
        jQuery("select[name='utoc_position']").val("disabled");
        jQuery(".utoc-remove-on-ajax").remove();
      });
    }, []);

    return (
      <div {...blockProps}>
        <div
          dangerouslySetInnerHTML={{ __html: utocHtml }}
          className="utoc-block-html"
        />
      </div>
    );
  },

  // No information saved to the block.
  // Data is saved to post meta via the hook.
  save: () => {
    jQuery("select[name='utoc_position']").val("disabled");
    jQuery(".utoc-remove-on-ajax").remove();

    return `[btoc]`;
  },
});
