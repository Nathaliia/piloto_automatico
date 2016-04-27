<?php
/**
 * @version   $Id: EasyBlog.php 59009 2013-02-01 04:34:08Z steph $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_EasyBlog extends RokSprocket_Provider_AbstarctJoomlaBasedProvider
{
    /**
     * @static
     * @return bool
     */
    public static function isAvailable()
    {
        if (!class_exists('JFactory')) {
            return false;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.extension_id');
        $query->from('#__extensions AS a');
        $query->where('a.type = "component"');
        $query->where('a.element = "com_easyblog"');
        $query->where('a.enabled = 1');

        $db->setQuery($query);

        if ($db->loadResult()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param array $filters
     * @param array $sort_filters
     */
    public function __construct($filters = array(), $sort_filters = array())
    {
        parent::__construct('easyblog');
        $this->setFilterChoices($filters, $sort_filters);
    }

    /**
     * @param     $raw_item
     * @param int $dborder
     *
     * @return \RokSprocket_Item
     */
    protected function convertRawToItem($raw_item, $dborder = 0)
    {
        //$textfield = $this->params->get('easyblog_articletext_field', '');
        require_once(JPATH_ROOT.'/components/com_easyblog/helpers/helper.php');
        //suppress easyblog code errors
        $cfg = @EasyBlogHelper::getConfig();

        $item = new RokSprocket_Item();

        $item->setProvider($this->provider_name);
        $item->setId($raw_item->id);
        $item->setAlias(JApplication::stringURLSafe($raw_item->title));
        $item->setAuthor($raw_item->author_name);
        $item->setTitle($raw_item->title);
        $item->setDate($raw_item->created);
        $item->setPublished(($raw_item->published == 1) ? true : false);
        $item->setText($raw_item->intro);
        $item->setCategory($raw_item->category_title);
        $item->setHits($raw_item->hits);
        $item->setRating($raw_item->rating);
        $item->setMetaKey($raw_item->metakey);
        $item->setMetaDesc($raw_item->metadesc);
        $item->setMetaData(array("robots"=>"'.$raw_item->robots.'","author"=>"'.$raw_item->source.'","rights"=>"'.$raw_item->copyrights.'","xreference"=>""));
        $item->setPublishUp($raw_item->publish_up);
        $item->setPublishDown($raw_item->publish_down);

        $images = array();
        if (isset($raw_item->image) && !empty($raw_item->image)) {
            try {
                $raw_image = RokCommon_JSON::decode($raw_item->image);
                if (isset($raw_image) && !empty($raw_image)) {

                    $item_image = new RokSprocket_Item_Image();
                    $folder = explode(':', $raw_image->place);
                    $item_image->setSource(JPath::clean(JURI::root(true).$cfg->get( 'main_image_path' ).$folder[1].$raw_image->path));
                    $item_image->setIdentifier('image_item');
                    $item_image->setCaption(null);
                    $item_image->setAlttext(null);
                    $images[$item_image->getIdentifier()] = $item_image;
                    $item->setPrimaryImage($item_image);

                    //full image
                    $item_image = new RokSprocket_Item_Image();
                    $item_image->setSource(JPath::clean(str_replace(JURI::root(), JURI::root(true).'/', $raw_image->url)));
                    $item_image->setIdentifier('image_full');
                    $item_image->setCaption(null);
                    $item_image->setAlttext(null);
                    $images[$item_image->getIdentifier()] = $item_image;

                    //thumbnail
                    $item_image = new RokSprocket_Item_Image();
                    $item_image->setSource(JPath::clean(str_replace(JURI::root(), JURI::root(true).'/', $raw_image->thumbnail->url)));
                    $item_image->setIdentifier('image_thumb');
                    $item_image->setCaption(null);
                    $item_image->setAlttext(null);
                    $images[$item_image->getIdentifier()] = $item_image;

                    //icon
                    $item_image = new RokSprocket_Item_Image();
                    $item_image->setSource(JPath::clean(str_replace(JURI::root(), JURI::root(true).'/', $raw_image->icon->url)));
                    $item_image->setIdentifier('image_icon');
                    $item_image->setCaption(null);
                    $item_image->setAlttext(null);
                    $images[$item_image->getIdentifier()] = $item_image;
                }
            } catch (RokCommon_JSON_Exception $jse) {
                $this->container->roksprocket_logger->warning('Unable to decode image JSON for article ' . $item->getArticleId());
            }
            $item->setImages($images);
        }

        $primary_link = new RokSprocket_Item_Link();
        $primary_link->setUrl(JRoute::_( 'index.php?option=com_easyblog&view=entry&id='.$raw_item->id ));
        $primary_link->getIdentifier('article_link');
        $item->setPrimaryLink($primary_link);

        $item->setPrimaryLink($primary_link);

        $links = array();
        $link = new RokSprocket_Item_Link();
        $link->setUrl(JRoute::_( 'index.php?option=com_easyblog&view=entry&id='.$raw_item->id ));
        $link->setText('');
        $link->setIdentifier('article_link');
        $links[$link->getIdentifier()] = $link;
        $item->setLinks($links);

        $texts = array();
        $texts['text_content'] = $raw_item->content;
        $texts['text_intro'] = $raw_item->intro;
        $texts['text_excerpt'] = $raw_item->excerpt;
        $texts['text_metadesc'] = $raw_item->metadesc;
        $texts = $this->processPlugins($texts);
        $item->setTextFields($texts);
        $item->setText($texts['text_intro']);


        $item->setDbOrder($dborder);

        // unknown joomla items
        $item->setCommentCount($raw_item->comment_count);
        if (isset($raw_item->tags)) {
            $tags = (explode(',', $raw_item->tags)) ? explode(',', $raw_item->tags) : array();
            $item->setTags($tags);
        }
        return $item;
    }

    /**
     * @param $id
     *
     * @return string
     */
    protected function getArticleEditUrl($id)
    {
        return JURI::root(true) . '/administrator/index.php?option=com_easyblog&c=blogs&task=edit&blogid=' . $id;
    }

    /**
     * @return array the array of image type and label
     */
    public static function getImageTypes()
    {
        return array(
            'image_full' => array('group' => null, 'display' => 'Post Full Image'),
            'image_thumb' => array('group' => null, 'display' => 'Post Thumbnail Image'),
            'image_icon' => array('group' => null, 'display' => 'Post Icon Image'),
        );
    }

   	/**
   	 * @return array the array of link types and label
   	 */
   	public static function getLinkTypes()
   	{
           return array();
   	}

    /**
     * @return array the array of link types and label
     */
    public static function getTextTypes()
    {
        return array(
            'text_content' => array('group' => null, 'display' => 'Post Content'),
            'text_intro' => array('group' => null, 'display' => 'Post Intro Text'),
            'text_excerpt' => array('group' => null, 'display' => 'Post Excerpt'),
            'text_metadesc' => array('group' => null, 'display' => 'Post Meta Description'),
        );
    }
}