import Edit from "./edit.js";
import metadata from "./block.json";
import { registerBlockType } from "@wordpress/blocks";

registerBlockType(metadata.name, {
    edit: Edit,
    save: () => null,
});
