import { __ } from "@wordpress/i18n";
import { useEffect } from "@wordpress/element";
import { useSelect } from "@wordpress/data";
import { useBlockProps } from "@wordpress/block-editor";
import { Button, SelectControl, TextareaControl } from "@wordpress/components";

/**
 * Edit component for the Thoughts from the Team block.
 *
 * Provides UI for selecting team members and composing their contributions.
 */
export default function Edit({ attributes, setAttributes }) {
    const { entries = [] } = attributes;

    useEffect(() => {
        if (!entries || entries.length === 0) {
            setAttributes({ entries: [{ userId: 0, content: "" }] });
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const users =
        useSelect(
            (select) =>
                select("core").getUsers({ roles: ["author", "editor"] }),
            []
        ) || [];

    const userOptions = users.map((user) => ({
        label: user.name,
        value: user.id,
        avatar: user.avatar_urls && user.avatar_urls["48"],
    }));

    const updateEntry = (index, field, value) => {
        const newEntries = entries.map((entry, i) =>
            i === index ? { ...entry, [field]: value } : entry
        );
        setAttributes({ entries: newEntries });
    };

    const addEntry = () => {
        setAttributes({ entries: [...entries, { userId: 0, content: "" }] });
    };

    const removeEntry = (index) => {
        const newEntries = [...entries];
        newEntries.splice(index, 1);
        setAttributes({ entries: newEntries });
    };

    const firstName = (name) => {
        return name ? name.split(" ")[0] : "";
    };

    const blockProps = useBlockProps({
        className: "team-thoughts-editor",
    });

    const contributorLabel = __("Contributor", "bigbluebox");
    const contributorPlaceholder = __("Select a user", "bigbluebox");
    const contentLabel = __("Text from contributor", "bigbluebox");
    const contentPlaceholder = __("Add their thoughts here…", "bigbluebox");

    return (
        <div {...blockProps}>
            <h3 className="team-thoughts-editor__title">
                {__("Thoughts from the Team", "bigbluebox")}
            </h3>
            <div className="team-thoughts-editor__entries">
                {entries.map((entry, index) => {
                    const selectedUser = users.find(
                        (u) => u.id === entry.userId
                    );
                    const avatarUrl =
                        selectedUser?.avatar_urls?.["96"] ||
                        selectedUser?.avatar_urls?.["48"] ||
                        "";

                    return (
                        <div key={index} className="team-thought-entry">
                            {selectedUser && (
                                <div className="team-thought-entry__user">
                                    <img
                                        className="team-thought-entry__avatar"
                                        src={avatarUrl}
                                        alt={selectedUser.name}
                                        width="56"
                                        height="56"
                                    />
                                    <h4>{firstName(selectedUser.name)}</h4>
                                </div>
                            )}
                            <div className="team-thought-entry__contributor">
                                <span
                                    className="team-thought-entry__label"
                                    aria-hidden="true"
                                >
                                    {contributorLabel}
                                </span>
                                <SelectControl
                                    className="team-thought-entry__input"
                                    label={contributorLabel}
                                    hideLabelFromVision
                                    value={entry.userId}
                                    options={[
                                        {
                                            label: contributorPlaceholder,
                                            value: 0,
                                        },
                                        ...userOptions.map(
                                            ({ label, value }) => ({
                                                label,
                                                value,
                                            })
                                        ),
                                    ]}
                                    onChange={(val) =>
                                        updateEntry(
                                            index,
                                            "userId",
                                            parseInt(val, 10)
                                        )
                                    }
                                />
                            </div>
                            <div className="team-thought-entry__content">
                                <span
                                    className="team-thought-entry__label"
                                    aria-hidden="true"
                                >
                                    {contentLabel}
                                </span>
                                <TextareaControl
                                    className="team-thought-entry__input"
                                    label={contentLabel}
                                    hideLabelFromVision
                                    value={entry.content}
                                    onChange={(val) =>
                                        updateEntry(index, "content", val)
                                    }
                                    placeholder={contentPlaceholder}
                                />
                            </div>
                            {entries.length > 1 && (
                                <div className="team-thought-entry__actions">
                                    <Button
                                        className="team-thought-entry__remove"
                                        variant="secondary"
                                        isDestructive
                                        onClick={() => removeEntry(index)}
                                    >
                                        {__("Remove author", "bigbluebox")}
                                    </Button>
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>
            <Button
                className="team-thoughts-editor__add"
                variant="primary"
                onClick={addEntry}
            >
                {entries && entries.length > 0
                    ? __("Add another author", "bigbluebox")
                    : __("Add author", "bigbluebox")}
            </Button>
        </div>
    );
}
