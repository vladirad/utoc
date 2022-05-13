<?php

namespace Devstetic\Utoc;

class UltimateTableOfContentsParser
{
    use UltimateTableOfContentsSettings;

    public $data = [];
    public $admin_fields = [];

    public function parse($html, $depth = 1, $return = false, $allowed_levels = null)
    {
        $allowed_levels = ($allowed_levels ?: $this->get_setting_level()) ?: UltimateTableOfContentsSettings::$levels;

        if ($depth > 6)
            return [];

        // This will continue recursive call if we're skipping this level
        if (!in_array($depth, $allowed_levels))
            return $this->parse($html, $depth + 1, $return, $allowed_levels);

        $headlines = explode('<h' . $depth, $html);

        unset($headlines[0]);

        if (count($headlines) == 0)
            return [];

        $toc = [];

        foreach ($headlines as $headline) {
            list($hl_info, $temp) = explode('>', $headline, 2);
            list($hl_text, $sub_content) = explode('</h' . $depth . '>', $temp, 2);

            $id = '';

            if (strlen($hl_info) > 0 && ($id_tag_pos = stripos($hl_info, 'id')) !== false) {
                $id_start_pos = stripos($hl_info, '"', $id_tag_pos) + 1;
                $id_end_pos = stripos($hl_info, '"', $id_start_pos);
                $id = substr($hl_info, $id_start_pos, $id_end_pos - $id_start_pos);
            }

            if ($hl_text)
                $toc[] = [
                    'id' => $id,
                    'text' => $hl_text,
                    'items' => $this->parse($sub_content, $depth + 1, true, $allowed_levels)
                ];
        }

        if ($return)
            return $toc;
        else
            $this->data = $toc;
    }

    public function get_data()
    {
        return $this->data;
    }

    public function render($items = null, $level = 0, $return = false, $admin = true)
    {
        ob_start();

        $is_admin = is_admin() && $admin;
        $items = $items ?: $this->data;
        $utoc_text = $this->get_setting('utoc_text');
        $utoc_visible = $this->get_setting('utoc_visible');

        $count = $this->count($items) > 0 || $is_admin;

        if (!$count) {
            return '';
        }

        if ($level === 0) {
            $isSticky = $this->get_setting_style() === 'default-sticky';
            echo '<div class="utoc' . ($is_admin ? ' is-active is-admin' : '') . ($isSticky ? ' is-sticky' : '') . '" data-parse="' . implode(",", array_map(function ($item) {
                return 'h' . $item;
            }, $this->get_setting_level())) . '">';
            echo '<span class="utoc-title">' . $this->get_setting_title() . '</span>';
        }


        echo '<ul class="utoc-level utoc-level-' . $level . '">';
        foreach ($items as $item) {
            $id = $item['id'];
            $text = ($utoc_text[$id] ?? null) ?: $item['text'];
            $visible = $utoc_visible[$id] ?? true;

            if (!$is_admin && !$visible)
                continue;

            $child_count = $this->count($item['items'] ?? []);

            echo '<li class="utoc-item' . ((!$visible) ? ' utoc-hidden' : '') . ($child_count ? ' utoc-has-children' : '') . ' utoc-' . $id . '">';

            echo '<span>';
            echo '<a href="javascript:void(0)" data-utoc="' . $id . '">';
            echo strip_tags($text);
            echo '</a>';

            if ($is_admin)
                $this->admin_actions($id, $text, $visible, $item['text']);

            echo '</span>';

            if ($child_count)
                $this->render($item['items'], $level + 1, false, $admin);

            echo '</li>';
        }
        echo '</ul>';

        if ($level === 0) {
            echo '</div>';
        }

        $content = ob_get_contents();

        ob_end_clean();

        if ($count === 0) $content = '';

        if ($return)
            return $content;
        else
            echo $content;
    }

    public function get_html($admin = true)
    {
        return $this->render(null, 0, true, $admin);
    }

    private function admin_actions($id = '', $text, $visible, $old_text)
    {
        echo '<div class="utoc-buttons" data-for="' . addslashes($id) . '">';
        echo '<a class="handle-visible">
            <svg class="is-active" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M942.2 486.2C847.4 286.5 704.1 186 512 186c-192.2 0-335.4 100.5-430.2 300.3a60.3 60.3 0 0 0 0 51.5C176.6 737.5 319.9 838 512 838c192.2 0 335.4-100.5 430.2-300.3 7.7-16.2 7.7-35 0-51.5zM512 766c-161.3 0-279.4-81.8-362.7-254C232.6 339.8 350.7 258 512 258c161.3 0 279.4 81.8 362.7 254C791.5 684.2 673.4 766 512 766zm-4-430c-97.2 0-176 78.8-176 176s78.8 176 176 176 176-78.8 176-176-78.8-176-176-176zm0 288c-61.9 0-112-50.1-112-112s50.1-112 112-112 112 50.1 112 112-50.1 112-112 112z"></path></svg>
            
            <svg class="is-inactive" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M942.2 486.2Q889.47 375.11 816.7 305l-50.88 50.88C807.31 395.53 843.45 447.4 874.7 512 791.5 684.2 673.4 766 512 766q-72.67 0-133.87-22.38L323 798.75Q408 838 512 838q288.3 0 430.2-300.3a60.29 60.29 0 0 0 0-51.5zm-63.57-320.64L836 122.88a8 8 0 0 0-11.32 0L715.31 232.2Q624.86 186 512 186q-288.3 0-430.2 300.3a60.3 60.3 0 0 0 0 51.5q56.69 119.4 136.5 191.41L112.48 835a8 8 0 0 0 0 11.31L155.17 889a8 8 0 0 0 11.31 0l712.15-712.12a8 8 0 0 0 0-11.32zM149.3 512C232.6 339.8 350.7 258 512 258c54.54 0 104.13 9.36 149.12 28.39l-70.3 70.3a176 176 0 0 0-238.13 238.13l-83.42 83.42C223.1 637.49 183.3 582.28 149.3 512zm246.7 0a112.11 112.11 0 0 1 146.2-106.69L401.31 546.2A112 112 0 0 1 396 512z"></path><path d="M508 624c-3.46 0-6.87-.16-10.25-.47l-52.82 52.82a176.09 176.09 0 0 0 227.42-227.42l-52.82 52.82c.31 3.38.47 6.79.47 10.25a111.94 111.94 0 0 1-112 112z"></path></svg>
        </a>';
        echo '<a class="handle-edit"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 1024 1024" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path d="M257.7 752c2 0 4-.2 6-.5L431.9 722c2-.4 3.9-1.3 5.3-2.8l423.9-423.9a9.96 9.96 0 0 0 0-14.1L694.9 114.9c-1.9-1.9-4.4-2.9-7.1-2.9s-5.2 1-7.1 2.9L256.8 538.8c-1.5 1.5-2.4 3.3-2.8 5.3l-29.5 168.2a33.5 33.5 0 0 0 9.4 29.8c6.6 6.4 14.9 9.9 23.8 9.9zm67.4-174.4L687.8 215l73.3 73.3-362.7 362.6-88.9 15.7 15.6-89zM880 836H144c-17.7 0-32 14.3-32 32v36c0 4.4 3.6 8 8 8h784c4.4 0 8-3.6 8-8v-36c0-17.7-14.3-32-32-32z"></path></svg></a>';
        echo '</div>';
    }

    public function render_admin_fields($items = null, $echo = true)
    {
        $items = $items ?: $this->data;
        $utoc_text = $this->get_setting('utoc_text');
        $utoc_visible = $this->get_setting('utoc_visible');

        if ($items) {
            foreach ($items as $item) {
                $id = $item["id"];
                $text = ($utoc_text[$id] ?? null) ?: $item['text'];

                $this->admin_fields[] = '<input data-for="' . addslashes($id) . '" type="hidden" class="utoc-visible-input" name="utoc_visible[' . $id . ']" value="' . addslashes($utoc_visible[$id] ?? '1') . '"/>';
                $this->admin_fields[] =  '<input data-for="' . addslashes($id) . '" type="hidden" class="utoc-text-input" name="utoc_text[' . $id . ']" value="' . ($text !== $item['text'] ? addslashes($text) : '') . '"/>';

                if ($item['items'] ?? false)
                    $this->render_admin_fields($item['items'], false);
            }
        }

        if ($echo)
            echo implode('', $this->admin_fields);
    }

    private function count($items)
    {
        $count = 0;
        $utoc_visible = $this->get_setting('utoc_visible');


        foreach ($items as $item) {
            $id = $item['id'];
            $visible = $utoc_visible[$id] ?? true;

            if ($visible)
                $count++;
        }

        return $count;
    }
}
