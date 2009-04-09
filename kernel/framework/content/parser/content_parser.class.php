<?php
/*##################################################
 *                           content_parser.class.php
 *                            -------------------
 *   begin                : August 10, 2008
 *   copyright            : (C) 2008 Benoit Sautel
 *   email                : ben.popeye@phpboost.com
 *
 *
 ###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

import('content/parser/parser');

/**
 * @package parser
 * @author Beno�t Sautel <ben.popeye@phpboost.com>
 * @desc This class is abstract. It contains tools that are usefull for implement a content parser.
 */
class ContentParser extends Parser
{
    ######## Public #######
    /**
    * @desc Buils a ContentParser object.
    */
    function ContentParser()
    {
        global $CONFIG;
        //We build the parent object
        parent::Parser();
        $this->html_auth =& $CONFIG['html_auth'];
        $this->forbidden_tags =& $CONFIG['forbidden_tags'];
    }

    /**
     * @desc Parses the content of the parser
     * @return void You will find the result by using the get_content method
     */
    function parse()
    {
        import('util/url');
        $this->content = Url::html_convert_absolutes2relatives($this->content, $this->path_to_root, $this->page_path);
    }

    /**
     * @desc Sets the tags which mustn't be parsed.
     * @param string[] $forbidden_tags list of the name of the tags which mustn't be parsed.
     */
    function set_forbidden_tags($forbidden_tags)
    {
        if (is_array($forbidden_tags))
        {
            $this->forbidden_tags = $forbidden_tags;
        }
    }

    /**
     * @desc Gets the forbidden tags.
     * @return string[] List of the forbidden tags
     */
    function get_forbidden_tags()
    {
        return $this->forbidden_tags;
    }

    /**
     * @desc Sets the required authorizations that are necessary to post some HTML code which
     * will be displayed by the web browser.
     * @param mixed[] $array_auth authorization array
     */
    function set_html_auth($array_auth)
    {
        global $CONFIG;
        if (is_array($array_auth))
        {
            $this->auth = $array_auth;
        }
        else
        {
            $this->auth =& $CONFIG['html_auth'];
        }
    }

    ## Private ##
    /**
     * @desc Splits a string accorting to a tag name.
     * Works also with nested tags.
     * @param string $content Content to split, will be converted in a string[] variable containing the following pattern:
     * <ul>
     *  <li>The content between two tags (or at the begening or the end of the content)</li>
     *  <li>The parameter of the tag</li>
     *  <li>The content of the tag. If it contains a nested tag, it will be parsed according to the same pattern.</li>
     * </ul>
     * @param string $tag Tag name
     * @param string $attributes Regular expression of the attribute form
     */
    function _split_imbricated_tag(&$content, $tag, $attributes)
    {
        $content = ContentParser::_preg_split_safe_recurse($content, $tag, $attributes);
        //1 �l�ment repr�sente les inter tag, un les attributs tag et l'autre le contenu
        $nbr_occur = count($content);
        for ($i = 0; $i < $nbr_occur; $i++)
        {
            //C'est le contenu d'un tag, il contient un sous tag donc on �clate
            if (($i % 3) === 2 && preg_match('`\[' . $tag . '(?:' . $attributes . ')?\].+\[/' . $tag . '\]`s', $content[$i]))
            {
                ContentParser::_split_imbricated_tag($content[$i], $tag, $attributes);
            }
        }
    }

    /**
     * @static
     * @desc Splits a string according to a regular expression. The matched pattern can be nested and must follow the BBCode syntax,
     * i.e matching [tag=args]content of the tag[/tag].
     * It returns an array
     * For example, il you have this: $my_str = '[tag=1]test1[/tag]test2[tag=2]test3[tag=3]test4[/tag]test5[/tag]?est6';
     * You call it like that: ContentParser::_preg_split_safe_recurse($my_str, 'tag', '[0-9]');
     * It will return you array('', '1', 'test1', 'test2', '2', array('test3', '3', 'test4', 'test5'), 'test6').
     * @param $content string Content into which you want to search the pattern
     * @param $tag string BBCode tage name
     * @param $attributes string The regular expression (PCRE syntax) corresponding to the arguments which you want to match.
     * There mustn't be any matching parenthesis into that regular expression
     * @return string[] the split string
     */
    function _preg_split_safe_recurse($content, $tag, $attributes)
    {
        // D�finitions des index de position de d�but des tags valides
        $_index_tags = $this->_index_tags($content, $tag, $attributes);
        $size = count($_index_tags);
        $parsed = array();

        // Stockage de la cha�ne avant le premier tag dans le cas ou il y a au moins une balise ouvrante
        if ($size >= 1)
        {
            array_push($parsed, substr($content, 0, $_index_tags[0]));
        }
        else
        {
            array_push($parsed, $content);
        }

        for ($i = 0; $i < $size; $i++)
        {
            $current_index = $_index_tags[$i];
            // Calcul de la sous-cha�ne pour l'expression r�guli�re
            if ($i == ($size - 1))
            {
                $sub_str = substr($content, $current_index);
            }
            else
            {
                $sub_str = substr($content, $current_index, $_index_tags[$i + 1] - $current_index);
            }

            // Mise en place de l'�clatement de la sous-chaine
            $mask = '`\[' . $tag . '(' . $attributes . ')?\](.*)\[/' . $tag . '\](.+)?`s';
            $local_parsed = preg_split($mask, $sub_str, -1, PREG_SPLIT_DELIM_CAPTURE);

            if (count($local_parsed) == 1)
            {
                // Remplissage des r�sultats
                $parsed[count($parsed) - 1] .= $local_parsed[0]; // Ce n'est pas un tag
            }
            else
            {
                // Remplissage des r�sultats
                array_push($parsed, $local_parsed[1]);  // attributs du tag
                array_push($parsed, $local_parsed[2]);  // contenu du tag
            }

            // Chaine apr�s le tag
            if ($i < ($size - 1))
            {
                // On prend la chaine apr�s le tag de fermeture courant jusqu'au prochain tag d'ouverture
                $current_tag_len = strlen('[' . $tag . $local_parsed[1] . ']' . $local_parsed[2] . '[/' . $tag . ']');
                $end_pos = $_index_tags[$i + 1] - ($current_index + $current_tag_len);
                array_push($parsed, substr($local_parsed[3], 0, $end_pos ));
            }
            elseif (isset($local_parsed[3]))
            {   // c'est la fin, il n'y a pas d'autre tag ouvrant apr�s
                array_push($parsed, $local_parsed[3]);
            }
        }
        return $parsed;
    }

    /**
     * @desc Indexes the position of all the tags in the document. Returns the list of the positions of each tag.
     * @param $content string Content into which index the positions.
     * @param $tag string tag name
     * @param $attributes The regular expression matching the parameters of the tag (see the _preg_split_safe_recurse method).
     * @return int[] The positions of the opening tags.
     */
    function _index_tags($content, $tag, $attributes)
    {
        $pos = -1;
        $nb_open_tags = 0;
        $tag_pos = array();

        while (($pos = strpos($content, '[' . $tag, $pos + 1)) !== false)
        {
            // nombre de tags de fermeture d�j� rencontr�s
            $nb_close_tags = substr_count(substr($content, 0, ($pos + strlen('['.$tag))), '[/'.$tag.']');

            // Si on trouve un tag d'ouverture, on sauvegarde sa position uniquement si il y a autant + 1 de tags ferm�s avant et on it�re sur le suivant
            if ($nb_open_tags == $nb_close_tags)
            {
                $open_tag = substr($content, $pos, (strpos($content, ']', $pos + 1) + 1 - $pos));
                $match = preg_match('`\[' . $tag . '(' . $attributes . ')?\]`', $open_tag);
                if ($match == 1)
                {
                    $tag_pos[count($tag_pos)] = $pos;
                }
            }
            $nb_open_tags++;
        }
        return $tag_pos;
    }

    /**
     * @desc Removes the content of the tag $tag and replaces them by an identifying code. They will be reinserted in the content by the _reimplant_tags method.
     * It enables you to treat the whole string enough affecting the interior of some tags.
     * Example: $my_parser contains this content: 'test1[tag=1]test2[/tag]test3'
     * $my_parser->_pick_up_tag('tag', '[0-9]'); will replace the content of the parser by 'test1[CODE_TAG_1]test3'
     * @param $tag string The tag to isolate
     * @param $arguments string The regular expression matching the arguments syntax.
     */
    function _pick_up_tag($tag, $arguments = '')
    {
        //On �clate le contenu selon les tags (avec imbrication bien qu'on ne les g�rera pas => �a permettra de faire [code][code]du code[/code][/code])
        $split_code = $this->_preg_split_safe_recurse($this->content, $tag, $arguments);

        $num_codes = count($split_code);
        //Si on a des apparitions de la balise
        if ($num_codes > 1)
        {
            $this->content = '';
            $id_code = 0;
            //On balaye le tableau trouv�
            for ($i = 0; $i < $num_codes; $i++)
            {
                //Contenu inter tags
                if ($i % 3 == 0)
                {
                    $this->content .= $split_code[$i];
                    //Si on n'est pas apr�s la derni�re balise fermante, on met une balise de signalement de la position du tag
                    if ($i < $num_codes - 1)
                    {
                        $this->content .= '[' . strtoupper($tag) . '_TAG_' . $id_code++ . ']';
                    }
                }
                //Contenu des balises
                elseif ($i % 3 == 2)
                {
                    //Enregistrement dans le tableau du contenu des tags � isoler
                    $this->array_tags[$tag][] = '[' . $tag . $split_code[$i - 1] . ']' . $split_code[$i] . '[/' . $tag . ']';
                }
            }
        }
    }

    /**
     * @desc Reimplants the code which has been picked up by the _pick_up method.
     * @param $tag string tag to reimplant.
     * @return bool True if the reimplantation succed, otherwise false.
     */
    function _reimplant_tag($tag)
    {
        //Si cette balise a  �t� isol�e
        if (!array_key_exists($tag, $this->array_tags))
        return false;

        $num_code = count($this->array_tags[$tag]);

        //On r�injecte tous les contenus des balises
        for ($i = 0; $i < $num_code; $i++)
        {
            $this->content = str_replace('[' . strtoupper($tag) . '_TAG_' . $i . ']', $this->array_tags[$tag][$i], $this->content);
        }

        //On efface tout ce qu'on a pr�lev� du array
        $this->array_tags[$tag] = array();

        return true;
    }

    //Attributes
    /**
    @static
    @var string[] List of the BBCode supported tags
    */
    var $tag = array('b', 'i', 'u', 's', 'title', 'stitle', 'style', 'url',
    'img', 'quote', 'hide', 'list', 'color', 'bgcolor', 'font', 'size', 'align', 'float', 'sup',
    'sub', 'indent', 'pre', 'table', 'swf', 'movie', 'sound', 'code', 'math', 'anchor', 'acronym');
    /**
     * @var string[] Authorization of the HTML BBCode tag.
     */
    var $html_auth = array();
    /**
     * @var string[] List of the BBCode forbidden tags
     */
    var $forbidden_tags = array();
}
?>