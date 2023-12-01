<?php

namespace App\Helpers;

use Twig;

use ErrorException;


/**
 * This class provides static methods for rendering templates.
 */
class Template {

    protected static array $data = [];
    protected static $template;

    /**
     * Run Template thorugh its initial configuration.
     *
     * @param   array   $options    The options
     */
    public static function setup(array $options) : void {
        self::$data = array_replace_recursive([
            'views_dir' => __DIR__ . '/views',
            'cache_dir' => null,
            'model'     => [],
            'filters'   => [],
            'twig'      => [],
        ], $options);


        $loader = new \Twig\Loader\FilesystemLoader(self::config('views_dir'));

        self::$template = new \Twig\Environment($loader, self::config('twig'));

        foreach (self::config('filters') as $key => $cb) {
            self::$template->addFilter(new \Twig\TwigFilter($key, $cb));
        }
    }



    /**
     * Returns the given config value for a specific setting path
     *
     * @param   string      $key The key
     * @throws  Exception   If $key path does not exist, alert developer instead of defaulting to nullish value
     * @return  any         If $key is null/undefined, returns the whole config array
     * @return  any         The stored value
     */
    public static function config(string $key = null) {
        if ($key === null) {
            return self::$data;
        }

        // setup memoization to trivialize future lookups
        static $memocache = [];

        if (isset($memocache[$key])) {
            return $memocache[$key];
        }

        $parts = explode('.', $key);
        $value = self::$data;

        foreach ($parts as $part) {
            if (!isset($value[$part])) {
                throw new ErrorException("key path does not exist ($key)");
            }

            $value = $value[$part];
        }

        $memocache[$key] = $value;

        return $value;
    }



    /**
     * Render a given file located in the configured views_dir
     *
     * @param      string  $name   The name
     * @param      array   $model  The model
     *
     * @return     string  the rendered template string
     */
    public static function render(string $name, array $model=[]) : string {
        // $filepath = self::config('views_dir') . '/' . trim($name, '/');
        // $src = file_get_contents($filepath);
        $model = array_replace_recursive(self::config('model'), $model);

        $filename = $name . self::config('ext');

        return self::$template->render($filename, $model);
    }


}
