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
                <PanelBody title={__("Team Post Additions Settings", "bigbluebox")}>
                    <SelectControl
                        label={__("Contributor", "bigbluebox")}
                        value={userId}
                        options={[
                            { label: __("Select a user", "bigbluebox"), value: 0 },
                            ...options,
                        ]}
                        onChange={(val) =>
                            setAttributes({ userId: parseInt(val, 10) })
                        }
                    />
                </PanelBody>
            </InspectorControls>

            <TextareaControl
                label={__("Contribution", "bigbluebox")}
                value={content}
                onChange={(val) => setAttributes({ content: val })}
                placeholder={__("Add your thoughts here…", "bigbluebox")}
            />
        </div>
    );
}
