<?php
define('PURIFIER_CACHE_CONFIG_PATH', DOCUMENT_ROOT. '/../cache/htmlpurifier');
if (!defined('HTMLPURIFIER_PREFIX')) {
	define('HTMLPURIFIER_PREFIX', realpath(dirname(__FILE__) . '/../..'));
}
class My_Zend_PurifyHtml
{   
    private static $defaultOptions = array(
            'HTML.Doctype'  => 'XHTML 1.0 Transitional',
            'HTML.SafeEmbed'    => true,
            'HTML.SafeObject'   => true,
    		'HTML.Strict'		=> true,		
            'Output.FlashCompat'    => true,          
            'Attr.DefaultInvalidImageAlt'=> 'CamNangLamMe.vn',
            //'Attr.DefaultImageAlt'=> '',
            'AutoFormat.RemoveEmpty'=> false,
    		'AutoFormat.RemoveEmpty.RemoveNbsp'	=> true,
    		'AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions'	=> array('td'),
            'AutoFormat.RemoveSpansWithoutAttributes'=> true,
    		'AutoFormat.AutoParagraph'   => true,
            'Attr.EnableID'=> false,   		
            'URI.Base'  =>  '',
            'URI.MakeAbsolute'  => false,
    		//'URI.DisableExternal' => true,
    		//'URI.DisableExternalResources' =>true, //xóa hết hình kể cả link owner
    		'AutoFormat.Linkify' => true,
            'CSS.AllowImportant'=> false,
    		'AttrTransform.TargetBlank' => true,   
            'HTML.Nofollow' => 'true',    		
    		'HTML.TargetBlank' => 'true',
           /// 'Attr.AllowedRel'=> array('nofollow'),
    		//'Attr.AllowedFrameTargets'=> array('_blank'),
    
    		//'Cache.SerializerPath'	=> PURIFIER_CACHE_CONFIG_PATH
    );

    public static function cleanup($c)
    {
        $c = str_replace(array('﻿', ' '), ' ', $c);

        $regEx = array(
                '#(\s*<\bstrong\b>\s*){2,}#im'					=>	'<strong>',
                '#(\s*<\/\bstrong\b>\s*){2,}#im'				=>	'</strong>',
                '#(<\bstrong\b>\s*<\/\bstrong\b>)#im'			=>	'',
                '#<span[^>]*(?:/>|>(?:\s|&nbsp;)*</span>)#im'	=>	'',
                //'#<p[^>]*(?:/>|>(?:\s|&nbsp;)*</p>)#im'			=>	'<br />',
        		'#<script(.*?)>(.*?)</script>#im'				=>  '',
        		'#<embed(.*?)>(.*?)<\/embed>#im'				=>  '',
        		'#<object(.*?)>(.*?)<\/object>#im'				=>  '',
        		'#<p>(\s|&nbsp;|</?\s?br\s?/?>)*</?p>#'			=> 	''
        );

        while (list($key, $value) = each($regEx))
        {
            $restart = false;
            while (preg_match($key, $c, $matches))
            {
                $c = preg_replace($key, $value, $c);
                $restart = true;
            }
            if ($restart)
            {
                reset($regEx);
            }
        }

        return $c;
    }


    private static function getServerUrl()
    {
        $cls = new Zend_View_Helper_ServerUrl();
        return $cls->serverUrl();
    }

    public static function purify($html, $options = array())
    {       
        $options = array_merge(self::$defaultOptions, $options);
		
        if ($options['URI.MakeAbsolute'] && !$options['URI.Base'])
        {
            $options['URI.Base'] = self::getServerUrl();
            //$options['URI.Base'] = '.';
        }
		
        $config = HTMLPurifier_Config::create($options);
        
        foreach ($options as $key => $value)
        {
            $config->set($key, $value);
        }
                        
		$config->set('Filter.Custom', array(new HTMLPurifier_Filter_Iframe()));
		$config->set('Filter.Custom', array( new HTMLPurifier_Filter_YoutubeIframe())); 
        
		//$config->set('HTML.Allowed', 'object[width|height|data],param[name|value],
        	//					embed[src|type|allowscriptaccess|allowfullscreen
        	//					|width|height|wmode]');
		
		$config->set('HTML.AllowedElements', array('b', 'strong', 'ul', 'ol', 'li',
			'em', 'hr', 'blockquote', 'a', 'br', 'p', 'span', 'h1', 'h2', 'h3', 'h4',
			'h5', 'h6', 'i', 'cite', 'dl', 'dt', 'dd', 'q', 'img','table',
			'del', 'sub', 'sup','div', 'tt', 'big', 'caption', 'code', 'small', 'strike','table','tr','td'));
		
		$config->set('HTML.AllowedAttributes', array('td.rowspan', 'td.colspan', 'a.href','a.target','img.src','img.style', 'img.alt', 'span.style','div.align', 'div.style','p.style','p.data-src'));
		$config->set('HTML.DefinitionID', 'enduser-customize.html tutorial');
		$config->set('HTML.DefinitionRev', 2);
		$def = $config->getHTMLDefinition(true);
		$def->addAttribute('p', 'data-src','CDATA');
		
        $purifier = new HTMLPurifier($config);

        $html = $purifier->purify($html);
        $html = self::cleanup($html);
        
        return $html;
    }
}