<?php
/**
  * @package ExternalData
  * @subpackage RSS
  */

if (!function_exists('xml_parser_create')) {
    die('XML Parser commands not available.');
}

/**
  */
includePackage('RSS');

/**
  * @package ExternalData
  * @subpackage RSS
  */
class RSSDataParser extends XMLDataParser
{
    protected $channel;
    protected $channelClass='RSSChannel';
    protected $itemClass='RSSItem';
    protected $imageClass='RSSImage';
    protected $enclosureClass='RSSEnclosure';
    protected $items=array();

    protected static $startElements=array(
        'RSS', 'RDF:RDF', 'CHANNEL', 'FEED', 'ITEM', 'ENTRY', 'ENCLOSURE', 'IMAGE');
    protected static $endElements=array(
        'CHANNEL', 'FEED', 'ITEM', 'ENTRY');
    
    public function items()
    {
        return $this->items;
    }
    
    public function getTitle() {
        return $this->channel->getTitle();
    }

    public function init($args)
    {
        parent::init($args);
        
        if (isset($args['CHANNEL_CLASS'])) {
            $this->setChannelClass($args['CHANNEL_CLASS']);
        }

        if (isset($args['ITEM_CLASS'])) {
            $this->setItemClass($args['ITEM_CLASS']);
        }

        if (isset($args['IMAGE_CLASS'])) {
            $this->setImageClass($args['IMAGE_CLASS']);
        }

        if (isset($args['ENCLOSURE_CLASS'])) {
            $this->setEnclosureClass($args['ENCLOSURE_CLASS']);
        }
    }

    protected function shouldHandleStartElement($name)
    {
        return in_array($name, self::$startElements);
    }

    protected function handleStartElement($name, $attribs)
    {
        switch ($name)
        {
            case 'RSS':
            case 'RDF:RDF':
                break;
            case 'CHANNEL':
            case 'FEED': //for atom feeds
                $this->elementStack[] = new $this->channelClass($attribs);
                break;
            case 'ITEM':
            case 'ENTRY': //for atom feeds
                $element = new $this->itemClass($attribs);
                $element->init($this->initArgs);
                $this->elementStack[] = $element;
                break;
            case 'ENCLOSURE':
            case 'MEDIA:CONTENT':
                $element = call_user_func(array($this->enclosureClass, 'factory'), $attribs);
                $this->elementStack[] = $element;
                break;
            case 'IMAGE':
                $this->elementStack[] = new $this->imageClass($attribs);
                break;
        }
    }

    protected function shouldHandleEndElement($name)
    {
        return in_array($name, self::$endElements);
    }

    protected function handleEndElement($name, $element, $parent)
    {
        switch ($name)
        {
            case 'FEED': //for atom feeds
            case 'CHANNEL':
                $this->channel = $element;
                break;
            case 'ITEM':
            case 'ENTRY': //for atom feeds
                $this->items[] = $element;
                break;
        }
    }

    public function setChannelClass($channelClass)
    {
    	if ($channelClass) {
    		if (!class_exists($channelClass)) {
    			throw new KurogoConfigurationException("Cannot load class $channelClass");
    		}
			$this->channelClass = $channelClass;
		}
    }

    public function setItemClass($itemClass)
    {
    	if ($itemClass) {
    		if (!class_exists($itemClass)) {
    			throw new KurogoConfigurationException("Cannot load class $itemClass");
    		}
			$this->itemClass = $itemClass;
		}
    }

    public function setEnclosureClass($enclosureClass)
    {
    	if ($enclosureClass) {
    		if (!class_exists($enclosureClass)) {
    			throw new KurogoConfigurationException("Cannot load class $enclosureClass");
    		}
			$this->enclosureClass = $enclosureClass;
		}
    }

    public function setImageClass($imageClass)
    {
    	if ($imageClass) {
    		if (!class_exists($imageClass)) {
    			throw new KurogoConfigurationException("Cannot load class $imageClass");
    		}
			$this->imageClass = $imageClass;
		}
    }
    
    protected function shouldStripTags($element)
    {
        $strip_tags = true;
        switch ($element->name())
        {
            case 'CONTENT:ENCODED':
            case 'CONTENT':
            case 'BODY':
                $strip_tags = false;
                break;
        }
        
        return $strip_tags;
    }
    
}

