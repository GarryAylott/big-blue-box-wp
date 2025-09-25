import { __ } from "@wordpress/i18n";
import { useSelect } from "@wordpress/data";
import {
    PanelBody,
    SelectControl,
    TextareaControl,
} from "@wordpress/components";
import { InspectorControls, useBlockProps } from "@wordpress/block-editor";

export default function Edit({ attributes, setAttributes }) {
    const { userId, content } = attributes;

    const users =
        useSelect(
            (select) =>
                select("core").getUsers({ roles: ["author", "editor"] }),
            []
        ) || [];

    const options = users.map((user) => ({
        label: user.name,
        value: user.id,
    }));

    return (
        <div {...useBlockProps()}>
            <InspectorControls>
                <PanelBody title={__("Team Post Additions Settings", "bbb")}>
                    <SelectControl
                        label={__("Contributor", "bbb")}
                        value={userId}
                        options={[
                            { label: __("Select a user", "bbb"), value: 0 },
                            ...options,
                        ]}
                        onChange={(val) =>
                            setAttributes({ userId: parseInt(val, 10) })
                        }
                    />
                </PanelBody>
            </InspectorControls>

            <TextareaControl
                label={__("Contribution", "bbb")}
                value={content}
                onChange={(val) => setAttributes({ content: val })}
                placeholder={__("Add your thoughts here…", "bbb")}
            />
        </div>
    );
}
