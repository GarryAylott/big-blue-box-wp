import Edit from "./edit.js";
import metadata from "./block.json";
import { registerBlockType } from "@wordpress/blocks";

/**
 * Register the Thoughts from the Team block.
 *
 * This block uses a dynamic render callback defined in PHP, so
 * the save function returns null. All front-end markup is
 * generated server-side via the `render.php` file.
 */
registerBlockType(metadata.name, {
    edit: Edit,
    save: () => null,
});
