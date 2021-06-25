<?php namespace Clavel\TranslatorManager;

use Clavel\TranslatorManager\Models\TranslationGroup;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Clavel\TranslatorManager\Models\Translation;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Manager
{
    const JSON_GROUP = '_json';

    /** @var \Illuminate\Contracts\Foundation\Application */
    protected $app;

    /** @var \Illuminate\Filesystem\Filesystem */
    protected $files;

    /** @var \Illuminate\Contracts\Events\Dispatcher */
    protected $events;

    protected $config;

    /** @var array  */
    protected $locales;
    protected $ignoreLocales;
    protected $ignoreFilePath;

    public function __construct(Application $app, Filesystem $files, Dispatcher $events)
    {
        $this->app = $app;
        $this->files = $files;
        $this->events = $events;
        $this->config = $app['config']['translator-manager'];
        $this->ignoreFilePath = storage_path('.ignore_locales');
        $this->locales = [];
        $this->ignoreLocales = $this->getIgnoredLocales();
    }

    protected function getIgnoredLocales()
    {
        if (! $this->files->exists($this->ignoreFilePath)) {
            return [];
        }
        $result = json_decode($this->files->get($this->ignoreFilePath));
        return ($result && is_array($result)) ? $result : [];
    }

    /**
     * Importamos las traducciones de /resources/lang iterando a traves de sus directorios
     *
     * @param  bool $replace
     * @param  string $base
     * @param  bool $import_group
     * @return void
     */
    public function importTranslations($replace = false, $base = null, $import_group = false)
    {
        $counter = 0;
        // Vemos si analizamos la carpeta de idiomas por defecto /resources/lang
        $vendor = true;
        if ($base == null) {
            $base = $this->app['path.lang'];
            $vendor = false;
        }

        // Recorreos todos los directorios que hay dentro de la carpeta de idiomas
        foreach ($this->files->directories($base) as $langPath) {
            // Obtenemos el idioma de la carpeta que sera el nombre de la carpeta agrupadora
            // Por ejemplo:
            // /ca/
            // /es/
            // /en/
            $locale = basename($langPath);

            // Importamos los ficheros de idiomas
            if ($locale == 'vendor') {
                foreach ($this->files->directories($langPath) as $vendor) {
                    $counter += $this->importTranslations($replace, $vendor);
                }
                continue;
            }

            $vendorName = $this->files->name($this->files->dirname($langPath));
            foreach ($this->files->allfiles($langPath) as $file) {
                $info = pathinfo($file);
                $group = $info['filename'];
                if ($import_group) {
                    if ($import_group !== $group) {
                        continue;
                    }
                }
                if (in_array($group, $this->config['exclude_groups'])) {
                    continue;
                }
                $subLangPath = str_replace($langPath.DIRECTORY_SEPARATOR, '', $info['dirname']);
                $subLangPath = str_replace(DIRECTORY_SEPARATOR, '/', $subLangPath);
                $langPath = str_replace(DIRECTORY_SEPARATOR, '/', $langPath);
                if ($subLangPath != $langPath) {
                    $group = $subLangPath.'/'.$group;
                }

                // Leemos el fichero de traducciones y lo ponemos en un array
                if (! $vendor) {
                    $translations = Lang::getLoader()->load($locale, $group);
                } else {
                    $translations = include $file;
                    $group = 'vendor/'.$vendorName;
                }

                // Grabamos el grupo para su posterior procesado
                $groupTranslation = TranslationGroup::where('group', $group)->first();
                if (empty($groupTranslation)) {
                    $groupTranslation = new TranslationGroup();
                    $groupTranslation->group = $group;
                    $groupTranslation->path = $base;
                    $groupTranslation->save();
                } else {
                    $groupTranslation->group = $group;
                    $groupTranslation->path = $base;
                    $groupTranslation->save();
                }

                // Importamos las traducciones
                if ($translations && is_array($translations)) {
                    foreach (Arr::dot($translations) as $key => $value) {
                        $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
                        $counter += $importedTranslation ? 1 : 0;
                    }
                }
            }
        }

        foreach ($this->files->files($this->app['path.lang']) as $jsonTranslationFile) {
            if (strpos($jsonTranslationFile, '.json') === false) {
                continue;
            }
            $locale = basename($jsonTranslationFile, '.json');
            $group = self::JSON_GROUP;
            $translations =
                Lang::getLoader()->load($locale, '*', '*'); // Retrieves JSON entries of the given locale only
            if ($translations && is_array($translations)) {
                foreach ($translations as $key => $value) {
                    $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace);
                    $counter += $importedTranslation ? 1 : 0;
                }
            }
        }
        return $counter;
    }

    public function importTranslationsCustom($replace = false, $base = null)
    {
        if ($base == null) {
            return 0;
        }

        $counter = 0;
        foreach ($this->files->directories($base) as $langPath) {
            $locale = basename($langPath);

            foreach ($this->files->allfiles($langPath) as $file) {
                $info = pathinfo($file);
                $group = $info['filename'];

                if (in_array($group, $this->config['exclude_groups'])) {
                    continue;
                }
                $subLangPath = str_replace($langPath.DIRECTORY_SEPARATOR, '', $info['dirname']);
                $subLangPath = str_replace(DIRECTORY_SEPARATOR, '/', $subLangPath);
                $langPath = str_replace(DIRECTORY_SEPARATOR, '/', $langPath);
                if ($subLangPath != $langPath) {
                    $group = $subLangPath.'/'.$group;
                }

                $fullGroup = str_replace(base_path().DIRECTORY_SEPARATOR, '', $base);
                $group = $fullGroup.'/'.$group;

                $groupTranslation = TranslationGroup::where('group', $group)->first();
                if (empty($groupTranslation)) {
                    $groupTranslation = new TranslationGroup();
                    $groupTranslation->group = $group;
                    $groupTranslation->path = $base;
                    $groupTranslation->save();
                } else {
                    $groupTranslation->group = $group;
                    $groupTranslation->path = $base;
                    $groupTranslation->save();
                }

                $translations = include $file;

                if ($translations && is_array($translations)) {
                    foreach (Arr::dot($translations) as $key => $value) {
                        $importedTranslation = $this->importTranslation($key, $value, $locale, $group, $replace, true);
                        $counter += $importedTranslation ? 1 : 0;
                    }
                }
            }
        }

        return $counter;
    }

    /**
     * Importamos la clave valor de idioma pasada por parametro dentro del grupo
     *
     * @param  string $key
     * @param  string $value
     * @param  string $locale
     * @param  string $group
     * @param  bool $replace
     * @param  bool $custom
     * @return void
     */
    public function importTranslation($key, $value, $locale, $group, $replace = false, $custom = false)
    {
        // process only string values
        if (is_array($value)) {
            return false;
        }
        $value = (string) $value;
        $translation = Translation::firstOrNew([
            'locale' => $locale,
            'group'  => $group,
            'key'    => $key,
            'custom'    => $custom,
        ]);
        // Check if the database is different then the files
        $newStatus = $translation->value === $value ? Translation::STATUS_SAVED : Translation::STATUS_CHANGED;
        if ($newStatus !== (int) $translation->status) {
            $translation->status = $newStatus;
        }
        // Only replace when empty, or explicitly told so
        if ($replace || ! $translation->value) {
            $translation->value = $value;
        }
        $translation->save();
        return true;
    }


    public function findTranslations($path = null)
    {
        $path = $path ?: base_path();
        $groupKeys = [];
        $stringKeys = [];
        $functions = $this->config['trans_functions'];

        $groupPattern =                          // See https://regex101.com/r/WEJqdL/6
            "[^\w|>]".                          // Must not have an alphanum or _ or > before real method
            '('.implode('|', $functions).')'.  // Must start with one of the functions
            "\(".                               // Match opening parenthesis
            "[\'\"]".                           // Match " or '
            '('.                                // Start a new group to match:
            '[a-zA-Z0-9_-]+'.               // Must start with group
            "([.](?! )[^\1)]+)+".             // Be followed by one or more items/keys
            ')'.                                // Close group
            "[\'\"]".                           // Closing quote
            "[\),]";                            // Close parentheses or new parameter
        $stringPattern =
            "[^\w]".                                     // Must not have an alphanum before real method
            '('.implode('|', $functions).')'.             // Must start with one of the functions
            "\(".                                          // Match opening parenthesis
            "(?P<quote>['\"])".                            // Match " or ' and store in {quote}
            "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)". // Match any string that can be {quote} escaped
            "\k{quote}".                                   // Match " or ' previously matched
            "[\),]";                                       // Close parentheses or new parameter

        // Find all PHP + Twig files in the app folder, except for storage
        $finder = new Finder();
        $finder->in($path)
            ->exclude('storage')
            ->exclude('vendor')
            ->name('*.php')
            ->name('*.twig')
            ->name('*.vue')
            ->files();

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            // Search the current file for the pattern
            if (preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                // Get all matches
                foreach ($matches[2] as $key) {
                    $groupKeys[] = $key;
                }
            }
            if (preg_match_all("/$stringPattern/siU", $file->getContents(), $matches)) {
                foreach ($matches['string'] as $key) {
                    if (preg_match("/(^[a-zA-Z0-9_-]+([.][^\1)\ ]+)+$)/siU", $key, $groupMatches)) {
                        // group{.group}.key format, already in $groupKeys but also matched here
                        // do nothing, it has to be treated as a group
                        continue;
                    }
                    //TODO: This can probably be done in the regex, but I couldn't do it.
                    //skip keys which contain namespacing characters, unless they also contain a
                    //space, which makes it JSON.
                    if (! (str_contains($key, '::') && str_contains($key, '.'))
                        || str_contains($key, ' ')) {
                        $stringKeys[] = $key;
                    }
                }
            }
        }

        // Remove duplicates
        $groupKeys = array_unique($groupKeys);
        $stringKeys = array_unique($stringKeys);
        // Add the translations to the database, if not existing.
        foreach ($groupKeys as $key) {
            // Split the group and item
            list($group, $item) = explode('.', $key, 2);
            $this->missingKey('', $group, $item);
        }
        foreach ($stringKeys as $key) {
            $group = self::JSON_GROUP;
            $item = $key;
            $this->missingKey('', $group, $item);
        }
        // Return the number of found translations
        return count($groupKeys + $stringKeys);
    }

    public function missingKey($namespace, $group, $key)
    {
        if (! in_array($group, $this->config['exclude_groups'])) {
            Translation::firstOrCreate([
                'locale' => $this->app['config']['app.locale'],
                'group'  => $group,
                'key'    => $key,
            ]);
        }
    }

    public function exportTranslations($group = null, $json = false, $onlyLocale = null)
    {
        $basePath  = $this->app['path.lang'];

        if (! is_null($group) && ! $json) {
            if (! in_array($group, $this->config['exclude_groups'])) {
                $vendor = false;
                if ($group == '*') {
                    return $this->exportAllTranslations();
                } else {
                    if (Str::startsWith($group, 'vendor')) {
                        $vendor = true;
                    }
                }

                // Preparamos la consulta de traducciones
                $translations = Translation::ofTranslatedGroup($group);
                // Si solo queremos un idioma lo filtramos
                if (!empty($onlyLocale)) {
                    $translations = $translations->where('locale', $onlyLocale);
                }
                // Leemos las traducciones
                $tree = $this->makeTree($translations
                    ->orderByGroupKeys(Arr::get($this->config, 'sort_keys', false))
                    ->get());
                foreach ($tree as $locale => $groups) {
                    if (isset($groups[$group])) {
                        $translations = $groups[$group];
                        $path = $this->app['path.lang'];
                        $locale_path = $locale.DIRECTORY_SEPARATOR.$group;
                        if ($vendor) {
                            $path = $basePath.'/'.$group.'/'.$locale;
                            $locale_path = Str::after($group, '/');
                        }
                        $subfolders = explode(DIRECTORY_SEPARATOR, $locale_path);
                        array_pop($subfolders);
                        $subfolder_level = '';
                        foreach ($subfolders as $subfolder) {
                            $subfolder_level = $subfolder_level.$subfolder.DIRECTORY_SEPARATOR;
                            $temp_path = rtrim($path.DIRECTORY_SEPARATOR.$subfolder_level, DIRECTORY_SEPARATOR);
                            if (! is_dir($temp_path)) {
                                mkdir($temp_path, 0777, true);
                            }
                        }
                        $path = $path.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.$group.'.php';
                        $output = "<?php\n\nreturn ".var_export($translations, true).';'.\PHP_EOL;
                        $this->files->put($path, $output);
                    }
                }
                Translation::ofTranslatedGroup($group)->update(['status' => Translation::STATUS_SAVED]);
            }
        }

        if ($json) {
            $tree = $this->makeTree(Translation::ofTranslatedGroup(self::JSON_GROUP)
                ->orderByGroupKeys(Arr::get($this->config, 'sort_keys', false))
                ->get(), true);
            foreach ($tree as $locale => $groups) {
                if (isset($groups[self::JSON_GROUP])) {
                    $translations = $groups[self::JSON_GROUP];
                    $path = $this->app['path.lang'].'/'.$locale.'.json';
                    $output = json_encode($translations, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE);
                    $this->files->put($path, $output);
                }
            }
            Translation::ofTranslatedGroup(self::JSON_GROUP)->update(['status' => Translation::STATUS_SAVED]);
        }
        //$this->events->dispatch(new TranslationsExportedEvent());
    }

    public function exportTranslationsCustom($group, $basePath = null, $onlyLocale = null)
    {
        if (! in_array($group, $this->config['exclude_groups'])) {
            if ($group == '*') {
                return $this->exportAllTranslationsCustom();
            } else {
                if (empty($basePath)) {
                    return;
                }
            }


            // Preparamos la consulta de traducciones
            $translations = Translation::ofTranslatedGroup($group)
                ->where('custom', true);
            // Si solo queremos un idioma lo filtramos
            if (!empty($onlyLocale)) {
                $translations = $translations->where('locale', $onlyLocale);
            }
            // Leemos las traducciones
            $tree = $this->makeTree($translations
                ->orderByGroupKeys(Arr::get($this->config, 'sort_keys', false))
                ->get());

            foreach ($tree as $locale => $groups) {
                if (isset($groups[$group])) {
                    $translations = $groups[$group];

                    $fullGroup = str_replace(base_path().DIRECTORY_SEPARATOR, '', $basePath);
                    $locale_path = str_replace($fullGroup.DIRECTORY_SEPARATOR, '', $group);
                    $path = $basePath.DIRECTORY_SEPARATOR.$locale;

                    $subfolders = explode(DIRECTORY_SEPARATOR, $locale_path);
                    array_pop($subfolders);
                    $subfolder_level = '';
                    foreach ($subfolders as $subfolder) {
                        $subfolder_level = $subfolder_level.$subfolder.DIRECTORY_SEPARATOR;
                        $temp_path = rtrim($path.DIRECTORY_SEPARATOR.$subfolder_level, DIRECTORY_SEPARATOR);
                        if (! is_dir($temp_path)) {
                            mkdir($temp_path, 0777, true);
                        }
                    }

                    if (! is_dir($path)) {
                        mkdir($path, 0777, true);
                    }

                    $path = $basePath.DIRECTORY_SEPARATOR.$locale.DIRECTORY_SEPARATOR.$locale_path.'.php';
                    $output = "<?php\n\nreturn ".var_export($translations, true).';'.\PHP_EOL;
                    $this->files->put($path, $output);
                }
            }
            Translation::ofTranslatedGroup($group)->update(['status' => Translation::STATUS_SAVED]);
        }



        //$this->events->dispatch(new TranslationsExportedEvent());
    }

    public function exportAllTranslations()
    {
        $groups = Translation::whereNotNull('value')->selectDistinctGroup()
            ->where('custom', false)
            ->get('group');
        foreach ($groups as $group) {
            if ($group->group == self::JSON_GROUP) {
                $this->exportTranslations(null, true);
            } else {
                $this->exportTranslations($group->group);
            }
        }
        //$this->events->dispatch(new TranslationsExportedEvent());
    }

    public function exportAllTranslationsCustom()
    {
        $groups = Translation::whereNotNull('value')->selectDistinctGroup()
            ->where('custom', true)
            ->get('group');
        foreach ($groups as $group) {
            $groupTranslation = TranslationGroup::where('group', $group->group)->first();
            if (!empty($groupTranslation)) {
                $this->exportTranslationsCustom($group->group, $groupTranslation->path);
            }
        }
        //$this->events->dispatch(new TranslationsExportedEvent());
    }

    protected function makeTree($translations, $json = false)
    {
        $array = [];
        foreach ($translations as $translation) {
            if ($json) {
                $this->jsonSet(
                    $array[$translation->locale][$translation->group],
                    $translation->key,
                    $translation->value
                );
            } else {
                Arr::set(
                    $array[$translation->locale][$translation->group],
                    $translation->key,
                    $translation->value
                );
            }
        }
        return $array;
    }

    public function jsonSet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }
        $array[$key] = $value;
        return $array;
    }

    public function cleanTranslations()
    {
        Translation::whereNull('value')->delete();
    }

    public function truncateTranslations()
    {
        Translation::truncate();
    }

    /**
     * Leemos los idiomas que hay dentro de resources/lang. Es independiente de los idiomas de la Web
     *
     * @return array
     */
    public function getLocales()
    {
        if (empty($this->locales)) {
            $locales = array_merge(
                [config('app.locale')],
                Translation::groupBy('locale')->pluck('locale')->toArray()
            );
            foreach ($this->files->directories($this->app->langPath()) as $localeDir) {
                if (($name = $this->files->name($localeDir)) != 'vendor') {
                    $locales[] = $name;
                }
            }
            $this->locales = array_unique($locales);
            sort($this->locales);
        }
        return array_diff($this->locales, $this->ignoreLocales);
    }

    /**
     * Crea la carpeta vacia del nuevo idioma dentro de /resources/lang
     *
     * @param  mixed $locale
     * @return void
     */
    public function addLocale($locale)
    {
        $localeDir = $this->app->langPath().'/'.$locale;
        $this->ignoreLocales = array_diff($this->ignoreLocales, [$locale]);
        $this->saveIgnoredLocales();
        $this->ignoreLocales = $this->getIgnoredLocales();
        if (! $this->files->exists($localeDir) || ! $this->files->isDirectory($localeDir)) {
            return $this->files->makeDirectory($localeDir);
        }
        return true;
    }
    protected function saveIgnoredLocales()
    {
        return $this->files->put($this->ignoreFilePath, json_encode($this->ignoreLocales));
    }

    /**
     * Eliminamos el idioma de la base de datos
     *
     * @param  mixed $locale
     * @return void
     */
    public function removeLocale($locale)
    {
        // Comprobamos que sea un idioma
        if (! $locale) {
            return false;
        }

        $this->ignoreLocales = array_merge($this->ignoreLocales, [$locale]);
        $this->saveIgnoredLocales();
        $this->ignoreLocales = $this->getIgnoredLocales();
        Translation::where('locale', $locale)->delete();
    }

    public function getConfig($key = null)
    {
        if ($key == null) {
            return $this->config;
        } else {
            return $this->config[$key];
        }
    }
}
