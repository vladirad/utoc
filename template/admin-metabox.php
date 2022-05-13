<?php
global $post;
$post_content = apply_filters('the_content', $post->post_content);
$parser = $this->parser;

$items = $parser->parse($post_content);

$utoc_position = $this->get_setting('utoc_position');
$utoc_title = $this->get_setting('utoc_title');
$utoc_level = $this->get_setting('utoc_level');
$utoc_style = $this->get_setting('utoc_style');

$levels = $this::$levels;
$styles = $this::$styles;
?>

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
            <label>Title</label>
        </th>
        <td>
            <input name="utoc_title" value="<?php echo $utoc_title ?: __('Summary', 'utoc'); ?>" />
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

</table>

<div class="utoc-remove-on-ajax">
    <?php
    if ($utoc_position !== 'disabled')
        $parser->render();
    ?>
</div>

<?php
$parser->render_admin_fields();
?>