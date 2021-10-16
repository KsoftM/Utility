<?php

namespace ksoftm\system\utils\html;

use ksoftm\system\utils\Cookie;

abstract class Mixer
{

    /**
     * randers the comment tag
     * 
     * @example link https://www.phpliveregex.com/p/BMQ#tab-preg-replace
     * @WebsiteAuthor Maintained by Philip Bjorge
     * @WebsiteAccessDate 04 september 2021
     *
     * @param string $tagName
     * @param string $data
     *
     * @return string|false
     */
    protected static function commentTag(string $data): string|false
    {
        return preg_replace(
            '/<!--([^-]*.*?)[^-]*-->/miU',
            '<!-- commented -->',
            $data
        );
    }

    /**
     * randers the singleline tag
     * 
     * @example link https://www.phpliveregex.com/p/BML#tab-preg-match-all
     * @WebsiteAuthor Maintained by Philip Bjorge
     * @WebsiteAccessDate 04 september 2021
     *
     * @param string $tagName
     * @param string $data
     *
     * @return array|false
     */
    protected static function singleTag(string $tagName, string $data, string $attribute = 'name'): array|false
    {
        if (
            preg_match_all(
                '/<' . $tagName . '[^>]+' . $attribute . '=[\'"]([^"]*[\s|\w|\W]*)[\'"][ \/]*>/miU',
                $data,
                $extendsData
            )
        ) {
            [$t, $n] = $extendsData;

            $extendsData = array_map(
                function ($template, $name) use ($extendsData, $attribute) {
                    return $extendsData[$name] = ($attribute == 'src')
                        ? new MixResult(
                            template: $template,
                            src: $name
                        ) : new MixResult(
                            template: $template,
                            name: $name
                        );
                },
                $t,
                $n
            );

            return $extendsData;
        }

        return false;
    }

    /**
     * Render the multiline tag
     * 
     * @example link https://www.phpliveregex.com/p/BMO#tab-preg-match-all
     * @WebsiteAuthor Maintained by Philip Bjorge
     * @WebsiteAccessDate 04 september 2021
     *
     * @param string $tagName
     * @param string $data
     *
     * @return array|false
     */
    protected static function MultiLineTag(
        string $tagName,
        string $data
    ): array|false {
        if (
            preg_match_all(
                '/<' . $tagName .
                    '[^>]+name=[\'"]([^"]*[\s|\S|\w|\W]*)[\'"]>([\s\w|\W]*)<\/' . $tagName . '>/miU',
                $data,
                $extendsData
            )
        ) {
            [$t, $p, $d] = $extendsData;

            $extendsData = array_map(
                function ($template, $path, $data) {
                    return $extendsData[$path] = new MixResult(
                        template: $template,
                        name: $path,
                        data: $data
                    );
                },
                $t,
                $p,
                $d
            );
            return $extendsData;
        }
        return false;
    }

    /**
     * Render the extend tag
     *
     * @param string $data
     *
     * @return MixResult|string|false [ 'template','name','data' ]
     */
    protected static function extend(string $data): MixResult|string|false
    {
        $tag = Mixer::MultiLineTag('extend', $data, true);

        if (is_array($tag)) {
            if (count($tag) != 1) {
                $data = false;
            } else {
                $data = $tag[0];
            }
        }

        return $data;
    }


    /**
     * Render var tag
     * 
     * @param string $data
     *
     * @return array [ 'template','name' ]
     */
    protected static function var(string $data): array|false
    {
        return Mixer::singleTag('var', $data);
    }


    /**
     * Render var string tag
     * 
     * @param string $data
     *
     * @return array [ 'template','name' ]
     */
    protected static function varString(string $data): array|false
    {
        if (preg_match_all('/[\'" ]var::([^"]*[\s|\S|\w|\W]*)[\'"]/miU', $data, $varStrings)) {
            [$t, $n] = $varStrings;

            $varStrings = array_map(
                function ($template, $name) {
                    return new MixResult(
                        template: $template,
                        name: $name
                    );
                },
                $t,
                $n
            );

            return $varStrings;
        }
        return false;
    }


    /**
     * Render var tag
     * 
     * @param string $data
     *
     * @return array [ 'template','src' ]
     */
    protected static function lang(string $data): array|false
    {
        $lang = Cookie::make('lang')->get('lang');

        return [$lang, Mixer::singleTag('lang', $data, 'src')];
    }


    /**
     * Render yield tag
     * 
     * @param string $data
     *
     * @return array [ 'template','name' ]
     */
    protected static function yield(string $data): array|false
    {
        return Mixer::singleTag('yield', $data);
    }


    /**
     * render section tag
     *
     * @param string $data
     *
     * @return array [ 'template','name','data' ]
     */
    protected static function section(string $data): array|false
    {
        return Mixer::MultiLineTag('section', $data, true);
    }
}
