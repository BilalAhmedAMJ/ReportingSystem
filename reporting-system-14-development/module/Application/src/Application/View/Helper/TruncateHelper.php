<?php
/**
 * Truncate without breaking text / html
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Navigation\AbstractContainer;

use Application\Service\ConfigService;

/**
 * Adds more functions to ease usage of naviations and menus 
 *
 * @author Ben Youngblood <bx.youngblood@gmail.com>
 */
class TruncateHelper extends AbstractHelper
{

    /**
     * @var Authorize
     */
    protected $pluginManager;
    /**
     * @param Authorize $authorizeService
     */
    public function __construct($pluginManager)
    {
        $this->pluginManager=$pluginManager;
    }


    public function __invoke($text,$length=120,$options=array()){
        return $this->truncateText($text,$length,$options);
    }
    

    /**
     * Copied from  http://stackoverflow.com/questions/16583676/shorten-text-without-splitting-words-or-breaking-html-tags
     */
    /**
    * Truncates text.
    *
    * Cuts a string to the length of $length and replaces the last characters
    * with the ending if the text is longer than length.
    *
    * ### Options:
    *
    * - `ending` Will be used as Ending and appended to the trimmed string
    * - `exact` If false, $text will not be cut mid-word
    * - `html` If true, HTML tags would be handled correctly
    *
    * @param string  $text String to truncate.
    * @param integer $length Length of returned string, including ellipsis.
    * @param array $options An array of html attributes and options.
    * @return string Trimmed string.
    * @access public
    * @link http://book.cakephp.org/view/1469/Text#truncate-1625
    */
    public function truncateText($text, $length = 120, $options = array()) {
        $default = array(
            'ending' => '...', 'exact' => true, 'html' => true
        );
        $options = array_merge($default, $options);
        extract($options);
    
        if ($html) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            $totalLength = mb_strlen(strip_tags($ending));
            $openTags = array();
            $truncate = '';
    
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);
                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];
    
                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }
    
                    $truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }
                if ($totalLength >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
            }
        }
        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');
            if (isset($spacepos)) {
                if ($html) {
                    $bits = mb_substr($truncate, $spacepos);
                    preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
                    if (!empty($droppedTags)) {
                        foreach ($droppedTags as $closingTag) {
                            if (!in_array($closingTag[1], $openTags)) {
                                array_unshift($openTags, $closingTag[1]);
                            }
                        }
                    }
                }
                $truncate = mb_substr($truncate, 0, $spacepos);
            }
        }
        $truncate .= $ending;
    
        if ($html) {
            foreach ($openTags as $tag) {
                $truncate .= '</'.$tag.'>';
            }
        }
    
        return $truncate;
    }
}

