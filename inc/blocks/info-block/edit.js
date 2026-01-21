import { __ } from "@wordpress/i18n";
import { useBlockProps, RichText } from "@wordpress/block-editor";

/**
 * Edit component for the Info Block.
 *
 * Provides UI for composing info/call-out text content with rich text formatting.
 */
export default function Edit({ attributes, setAttributes }) {
    const { content = "" } = attributes;

    const blockProps = useBlockProps({
        className: "info-block-editor",
    });

    const contentPlaceholder = __(
        "Enter your info or call-out text here…",
        "bigbluebox"
    );

    return (
        <div {...blockProps}>
            <h3 className="info-block-editor__title">
                {__("Info Block", "bigbluebox")}
            </h3>
            <div className="info-block-editor__content">
                <RichText
                    tagName="p"
                    className="info-block-editor__text"
                    value={content}
                    onChange={(val) => setAttributes({ content: val })}
                    placeholder={contentPlaceholder}
                    allowedFormats={["core/bold", "core/italic", "core/link"]}
                />
            </div>
        </div>
    );
}
