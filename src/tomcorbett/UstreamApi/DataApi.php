<?php
namespace tomcorbett\UstreamApi;

use GuzzleHttp\Client;

/**
 * This class has been created to help developers adapt the UStream Services to their sites
 * 
 * @author Klederson Bueno <klederson@klederson.com>
 * @version 0.1a
 * @license GPL
 */
class DataApi
{
	/**
     * @var string
     */
	protected $apiUrl = 'http://api.ustream.tv'; //complete url reference http://api.ustream.tv/[html|json|xml|php]/[subject]/[subjectUID|scope]/[command]/[otherparams]/?page=[n]&limit=[l]&key=[devkey]
	
	/**
	 * The main response method *** DO NOT CHANGE IT BECAUSE THE CLASS USES JSON TO COMMUNICATE ***
     * 
	 * @var string
	 */
	protected $responseMode = 'json'; //html, json, xml, php
	
	/**
	 * The main request mode you can change it using setRequestMode
     * 
	 * @var string
	 */
	protected $requestMode = 'channel'; //user, channel, stream, video, system
	
	/**
	 * The UStream API Key
     * 
	 * @var string
	 */
	protected $apiKey = null;
	
    /**
     * @var array
     */
	protected $cacheResult = null;
	
    /**
     * Request client - using Guzzle for HTTP requests
     * 
     * @var GuzzleHttp\Client
     */
    protected $client;

    /**
     * The default limit
     * 
     * @var integer
     */
    protected $limit = 20;
    
    /**
     * @var integer
     */
	protected $page = 1;
	
	public function __construct($apiKey, $requestMode = 'channel')
    {
		$this->apiKey       = $apiKey;
		$this->requestMode  = $requestMode;
		$this->cacheResult  = new stdClass();
        $this->client       = new GuzzleHttp\Client();
	}
	
	###########################################
	# Misc commands
	###########################################

	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @return stdObj
	 */
	public function getInfo($subject)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'getInfo');
		
		return $this->getResult($requestUrl);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @param String $property
	 * 
	 * @return stdClass
	 */
	public function getValueOf($subject, $property)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'getValueOf/'.$property);
				
		return $this->getResult($requestUrl);
	}
	
	/** 
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $scope
	 * @param String $where
	 * @param String $how
	 * @param String $what
	 * @param Number $page
	 * 
	 * @return stdClass
	 */
	public function search($scope, $where, $how, $what,$page = 1)
    {
		$fullCommand = sprintf("search/%s:%s:%s", $where, $how, $what);
		
		$requestUrl = $this->buildRequestUrl($scope, $fullCommand);
				
		return $this->getResult($requestUrl, $page);
	}
	
	###########################################
	# Stream commands
	###########################################
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @return stdClass
	 */
	public function getRecent()
    {
		$requestUrl = $this->buildRequestUrl('all', 'getRecent');
		
		return $this->getResult($requestUrl);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @return stdClass
	 */
	public function getMostViewers()
    {
		$requestUrl = $this->buildRequestUrl('all', 'getMostViewers');
		
		return $this->getResult($requestUrl);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @return stdClass
	 */
	public function getRandom()
    {
		$requestUrl = $this->buildRequestUrl('all', 'getRandom');
		
		return $this->getResult($requestUrl);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $page
	 * @return stdClass
	 */
	public function getAllNew($page = 1)
    {
		$requestUrl = $this->buildRequestUrl('all', 'getAllNew');
		
		return $this->getResult($requestUrl, $page);
	}
	
	###########################################
	# Video commands
	###########################################
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @return stdClass
	 */
	public function getTags($subject)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'getTags');
		
		return $this->getResult($requestUrl);
	}
	
	###########################################
	# Channel commands
	###########################################
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @return stdClass
	 */
	public function getEmbedTag($subject)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'getEmbedTag');
		
		return $this->getResult($requestUrl);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @param Number $width
	 * @param Number $height
	 * @param Boolean $autoPlay
	 * @param Boolean $mute
	 * 
	 * @return stdClass
	 */
	public function getCustomEmbedTag($subject,$width = 100, $height = 100, $autoPlay = false, $mute = false)
    {
		$parms['autoplay'] = $autoPlay;
		$parms['mute'] = $mute;
		$parms['width'] = $width;
		$parms['height'] = $height;
		
		$requestUrl = $this->buildRequestUrl($subject, 'getCustomEmbedTag', $parms);
				
		return $this->getResult($requestUrl);
	}
	
	###########################################
	# User commands
	###########################################
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * 
	 * @return stdClass
	 */
	public function getId($subject)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'getId');
		
		return $this->getResult($requestUrl);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @param Number $page
	 * 
	 * @return stdClass
	 */
	public function listAllChannels($subject, $page = 1)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'listAllChannels');
		
		return $this->getResult($requestUrl, $page);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @param Number $page
	 * 
	 * @return stdClass
	 */
	public function listAllVideos($subject, $page = 1)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'listAllVideos');
		
		return $this->getResult($requestUrl, $page);
	}
	
	/**
	 * @see http://developer.ustream.tv/data_api/docs
	 * 
	 * @param String $subject
	 * @param Number $page
	 * 
	 * @return stdClass
	 */
	public function getComments($subject, $page = 1)
    {
		$requestUrl = $this->buildRequestUrl($subject, 'getComments');
		
		return $this->getResult($requestUrl, $page);
	}
	
	###########################################
	# Util methods
	###########################################	
	
	/**
	 * isLive checks if the channel is broadcasting live at the moment
	 * 
	 * @param String $channel
	 * @return Boolean
	 */
	public function isLive($channel)
    {
		$channelData = $this->getInfo($channel);
		
		if($channelData->results->status == 'live') {
			return true;
		} else {
			false;
		}
	}
	
	/**
	 * This method builds the request url to call the UStream API this builds all requests to UStream
	 * 
	 * @param String $subject
	 * @param String $command
	 * @param Array $parms
	 * @return String
	 */
	private function buildRequestUrl($subject, $command, array $parms = array())
    {
		foreach($parms as $index => $value) {
			$trueParms = !empty($trueParms) && $trueParms != null ? $trueParms .';' : $trueParms;
			$trueParms .= sprintf("%s:%s",$index,$value);
		}
		
		$trueParms = !empty($trueParms) && $trueParms != null ? '&params=' . $trueParms: $trueParms;
		
		return $this->calledUrl = sprintf("%s/%s/%s/%s/%s?key=%s%s", $this->apiUrl, $this->responseMode, $this->requestMode, $subject, $command, $this->apiKey, $trueParms);
	}
	
	###########################################
	# Class methods
	###########################################
	
	/**
	 * Perform the call based in a requestUrl and than add the parm limit (see setLimit) and page as parm
	 * 
	 * @param String $requestUrl
	 * @param Number $page
	 * 
	 * @return stdClass
	 */
	public function getResult($requestUrl, $page = 1)
    {
		$requestUrl = sprintf("$requestUrl&limit=%s&page=%s",$this->limit,$this->page);
		
        $this->cacheResult = json_decode($this->client->get($requestUrl)->getBody());
		
		if($this->error() == true) {
			return false;
		} else {
			return $this->cacheResult;
		}
	}
	
	/**
	 * It checks if after the getResult call there are some errors
	 * 
	 * @return Boolean
	 */
	private function error()
    {
		if($this->cacheResult->error != null) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * This method gets the error message and code
	 * 
	 * @return Array
	 */
	public function getError()
    {
		$return['code'] = $this->cacheResult->error;
		$return['message'] = $this->cacheResult->msg;
		
		return $return;
	}
	
	/**
	 * Set the mode of response from UStream API
	 * 
     * @param string $responseMode
     * @return \tomcorbett\UstreamApi\DataApi
     */
	public function setResponseMode($responseMode)
    {
		$this->responseMode = $responseMode;
        
        return $this;
	}
	
	/**
	 * Set the request mode: channel, user, system, video, stream
	 * 
     * @param string $requestMode
     * @return \tomcorbett\UstreamApi\DataApi
     */
	public function setRequestMode($requestMode)
    {
		$this->requestMode = $requestMode;
        
        return $this;
	}
	
	/**
     * @param string $apiKey
     * @return \tomcorbett\UstreamApi\DataApi
     */
	public function setApiKey($apiKey)
    {
		$this->$apiKey = $apiKey;
        
        return $this;
	}
    
    /**
     * @return \GuzzleHttp\Client 
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * @param \GuzzleHttp\Client $client
     * @return \tomcorbett\UstreamApi\DataApi
     */
    public function setClient(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
        
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * @param integer $limit
     * @return \tomcorbett\UstreamApi\DataApi
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        
        return $this;
    }
    
    /**
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * @param integer $page
     * @return \tomcorbett\UstreamApi\DataApi
     */
    public function setPage($page)
    {
        $this->page = $page;
        
        return $this;
    }
}

?>