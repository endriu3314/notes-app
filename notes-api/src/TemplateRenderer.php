<?php

namespace NotesApi;

class TemplateRenderer
{
    public static string $viewsPath = __DIR__.'/../views';

    public static function renderTemplate(string $template, array $data = [], string $layout = 'app'): string
    {
        $templatePath = self::$viewsPath.'/'.$template.'.php';
        $layoutPath = self::$viewsPath.'/layouts/'.$layout.'.php';

        if (! file_exists($layoutPath)) {
            http_response_code(404);
            echo "Layout not found: $layout";

            return '';
        }

        if (! file_exists($templatePath)) {
            http_response_code(404);
            echo "Template not found: $template";

            return '';
        }

        $data['content'] = $templatePath;
        extract($data);

        ob_start();
        include $layoutPath;
        $content = ob_get_clean();

        return $content;
    }
}
