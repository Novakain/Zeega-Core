<?php

namespace Zeega\ExtensionsBundle\Parser\Flickr;

use Zeega\CoreBundle\Parser\Base\ParserAbstract;
use Zeega\DataBundle\Entity\Item;

use \DateTime;

class ParserFlickrPhoto extends ParserAbstract
{
	private static $license=array('','Attribution-NonCommercial-ShareAlike Creative Commons','Attribution-NonCommercial Creative 		
			Commons','Attribution-NonCommercial-NoDerivs Creative Commons','Attribution Creative Commons',
			'Attribution-ShareAlike Creative Commons','Attribution-NoDerivs Creative Commons','No known copyright restrictions');
	
	public function load($url, $parameters = null)
    {
        require_once('../vendor/phpflickr/lib/Phpflickr/Phpflickr.php');
        if(isset($parameters["photo_id"]))
        {
            $itemId = $parameters["photo_id"];
        }
        else
        {
            $regexMatches = $parameters["regex_matches"];
            $itemId = $regexMatches[1]; // bam
        }
	    
	    
		$f = new \Phpflickr_Phpflickr('97ac5e379fbf4df38a357f9c0943e140');
		$info = $f->photos_getInfo($itemId);
		$size = $f->photos_getSizes($itemId);

		if(is_array($info)&&is_array($size)) // why?
		{
			$item = new Item();
			$tags = array();

			$item->setAttributionUri($info['urls']['url'][0]['_content']);
            
			if($info['tags'])
			{
			    $tags = array();
				foreach($info['tags']['tag'] as $t)
				{
				    array_push($tags, $t["raw"]);
				}
				$item->setTags($tags);
			}
            
			foreach ($size as $s)
			{
				$sizes[$s['label']]=array('width'=>$s['width'],'height'=>$s['height'],'source'=>$s['source']);
			}	
			//return $sizes;
			$item->setThumbnailUrl($sizes['Square']['source']);

			$attr = array('farm'=>$info['farm'],'server'=>$info['server'],'id'=>$info['id'],'secret'=>$info['secret']);
			if(isset($sizes['Original'])) $attr['originalsecret']=$info['originalsecret'];

			if(isset($sizes['Large']))$itemSize='Large';
			elseif(isset($sizes['Original'])) $itemSize='Original';
			elseif(isset($sizes['Medium'])) $itemSize='Medium';
			else $itemSize='Small';

			if($info['dates']['taken']) $item->setMediaDateCreated(new DateTime($info['dates']['taken']));

			$item->setUri($sizes[$itemSize]['source']);
			$item->setChildItemsCount(0);

			$attr['sizes']=$sizes;
			$item->setDescription($info['description']);

			if($info['license'])$item->setLicense(self::$license[$info['license']]);
			else $item->setLicense('All Rights Reserved');

			if($info['owner']['username']) $item->setMediaCreatorUsername($info['owner']['username']);
			else $item->setMediaCreatorUsername($info['owner']['realname']);
			if($info['owner']['realname']) $item->setMediaCreatorRealname($info['owner']['realname']);
			else $item->setMediaCreatorRealname( $item->getMediaCreatorUsername());
			
			
			$attr['creator_nsid']=$info['owner']['nsid'];
			$item->setTitle($info['title']);

			if(array_key_exists ('location',$info)){
				if($info['location']['latitude']){
					$item->setMediaGeoLatitude($info['location']['latitude']);
					$item->setMediaGeoLongitude($info['location']['longitude']);
				}
			}

			$item->setArchive('Flickr'); 
			$item->setMediaType('Image');
			$item->setLayerType('Image');

			return $this->returnResponse($item, true, false);
		}
		else{
			return $this->returnResponse(null, false, false);
		}
	}
}
