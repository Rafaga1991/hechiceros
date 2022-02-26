<?php

namespace core;

class Html
{
    private static $styles = [];
    private static $script = [];
    private static $meta = [];
    private static $link = [];

    private static $icon = '';
    private static $title = '';
    private static $body = '';
    private static $body_param = '';

    private static $header = '';
    private static $footer = '';

    private static $variable = [];

    private static function __convertParam(array $param): string
    {
        foreach ($param as $name => &$value) $value = "$name=\"$value\"";
        return join(' ', $param);
    }

    private static function __variable(string $content)
    {
        foreach (self::$variable as $name => $value) $content = str_replace("{!!$name!!}", $value, $content);
        $content = str_replace('{!!!!}', '', $content);

        $token = !Session::get('__token') ? md5(microtime()) : Session::get('__token');
        $content = str_replace(['</form>', '{!!TOKEN!!}'], ["<input type='hidden' name='__token' value='$token' /></form>", $token], $content, $cant);

        if ($cant) Session::set('__token', $token);
        return $content;
    }

    public static function addStyle(array $param)
    {
        self::$styles[] = '<link ' . self::__convertParam($param) . ' rel="stylesheet">';
    }

    public static function addVariable(string $name, $value)
    {
        self::$variable[$name] = $value;
    }

    public static function addVariables(array $variables)
    {
        self::$variable = array_merge(self::$variable, $variables);
    }

    public static function addScript(array $param)
    {
        self::$script[] = '<script ' . self::__convertParam($param) . '></script>';
    }

    public static function addLink(array $param)
    {
        self::$link[] = '<link ' . self::__convertParam($param) . '>';
    }

    public static function addMeta(array $param)
    {
        self::$meta[] = '<meta ' . self::__convertParam($param) . '>';
    }

    public static function setBody(string $view, array $param = [])
    {
        if (empty(self::$body)) {
            self::$body = $view;
            self::$body_param = self::__convertParam($param);
        }
    }

    public static function setHeader(string $view)
    {
        self::$header = $view;
    }

    public static function setFooter(string $view)
    {
        self::$footer = $view;
    }

    public static function setTitle(string $title)
    {
        if (empty(self::$title)) self::$title = "<title>$title</title>";
    }

    public static function setIcon(string $path)
    {
        if (empty(self::$icon)) self::$icon = "<link rel=\"shortcut icon\" href=\"$path\" type=\"image/x-icon\">";
    }

    public static function getTitle(): string
    {
        return self::$title;
    }

    public static function OutPut()
    {
        $html = implode('', [
            '<!DOCTYPE html>',
            '<html lang="es">',
            '<head>',
            implode('', self::$meta),
            self::$title,
            self::$icon,
            implode('', self::$link),
            implode('', self::$styles),
            '</head>',
            '<body' . (!empty(self::$body_param) ? ' ' . self::$body_param : '') . '>',
            self::$header,
            self::$body,
            self::$footer,
            implode('', self::$script),
            '</body>',
            '</html>'
        ]);


        $html = self::__variable($html);
        echo Functions::replace($html, '{!!', '!!}');
    }
}
