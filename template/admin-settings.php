<?php

$utoc_position = $this->get_setting('utoc_position');
$utoc_level = $this->get_setting('utoc_level');
$utoc_title = $this->get_setting('utoc_title');
$utoc_style = $this->get_setting('utoc_style', 'default');
$utoc_screen = $this->get_setting('utoc_screen', ['post']);
$utoc_chevron_level = $this->get_setting('utoc_chevron_level', 0);
$utoc_css = strip_tags($this->get_setting('utoc_css'), ['svg']);

$levels = $this::$levels;
$chevron_levels = $this::$chevron_levels;
$styles = $this::$styles;

?>

<h1>UTOC Settings</h1>

<form method="POST" action="options.php" class="utoc-settings-table">
    <table class="form-table">
        <tr>
            <th>
                <label><?php _e('Positon:', 'utoc'); ?></label>
            </th>
            <td>
                <select name="utoc_position">
                    <option value="disabled">Hidden</option>
                    <option value="before-content" <?php echo $utoc_position === 'before-content' ? ' selected' : ''; ?>>Before content</option>
                    <option value="after-content" <?php echo $utoc_position === 'after-content' ? ' selected' : ''; ?>>After content</option>
                    <option value="after-first-paragraph" <?php echo $utoc_position === 'after-first-paragraph' ? ' selected' : ''; ?>>After fist paragraph</option>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label><?php _e('Title:', 'utoc'); ?></label>
            </th>

            <td>
                <input type="text" name="utoc_title" value="<?php echo $utoc_title ?: 'Summary'; ?>">
            </td>
        </tr>

        <tr>
            <th>
                <label><?php _e('Levels:', 'utoc'); ?></label>

            </th>
            <td>
                <?php foreach ($levels as $level) : ?>
                    <input type="checkbox" name="utoc_level[]" value="<?php echo $level; ?>" <?php echo in_array($level, $utoc_level) ? ' checked' : ''; ?>>H<?php echo $level; ?>
                <?php endforeach; ?>
            </td>
        </tr>

        <!-- <tr>
            <th>
                <label><?php _e('Chevron level:', 'utoc'); ?></label>

            </th>
            <td>
                <select name="utoc_chevron_level">
                    <?php foreach ($chevron_levels as $level) : ?>
                        <option value="<?php echo $level; ?>" <?php echo $utoc_chevron_level == $level ? ' selected' : ''; ?>><?php echo $level ? $level : 'Disabled'; ?></option>
                    <?php endforeach; ?>
                </select>

            </td>
        </tr> -->

        <tr>
            <th>
                <label>Post types:</label>
            </th>

            <td>
                <?php foreach (get_post_types(['public' => true]) as $type) : if ($type === 'attachment') continue; ?>
                    <input type="checkbox" name="utoc_screen[]" value="<?php echo $type; ?>" <?php echo in_array($type, $utoc_screen) ? ' checked' : ''; ?>><?php echo $type; ?>
                <?php endforeach; ?>
            </td>
        </tr>

        <tr>
            <th>
                <label><?php _e('Style:', 'utoc'); ?></label>
            </th>

            <td>
                <select name="utoc_style">
                    <?php foreach ($styles as $style) : ?>
                        <option value="<?php echo $style; ?>" <?php echo $utoc_style === $style ? ' selected' : ''; ?>><?php echo ucfirst($style); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label>Custom CSS <span class="utoc-info">i</span></label>
            </th>

            <td>
                <div class="utoc-row">
                    <div class="utoc-col">
                        <textarea name="utoc_css" style="min-width: 400px" rows="10"><?php echo $utoc_css ?: ''; ?></textarea>

                    </div>
                    <div class="utoc-col">
                        <div class="utoc-help">
                            <pre>
.utoc {
    /* utoc container */
}

.utoc.is-sticky {
    /* utoc container + sticky */
}

.utoc-title {
    /* utoc title */
}

.utoc-level {
    /* all toc levels */
}

.utoc-level-0 {
    /* specific toc levels (0-5) */
}

.utoc-level > li {
    /* list item container */
}

.utoc-level > li.is-active {
    /* list item when item is clicked/active */
}

.utoc-level > li.is-current {
    /* list item when scolled to content */
}

.utoc-level > li span {
    /* list item text */
}
    </pre>
                        </div>
                    </div>
                </div>

            </td>


        </tr>

    </table>

    <div class="utoc-row">
        <div class="utoc-col">
            <?php submit_button(); ?>
        </div>
        <!-- <div class="utoc-col">
            <?php submit_button('Save Changes & Override Post Data', 'danger', 'utoc-save-and-clear'); ?>
        </div> -->
    </div>



    <?php
    settings_fields('utoc');
    ?>
</form>