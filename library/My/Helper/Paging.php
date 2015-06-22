<?php
class My_Helper_Paging
{
    /**
     * View instance
     *
     * @var Zend_View_Instance
     */
    public $view;
    
    /**
     * Paging   
     * @param string $uri
     * @param array  $params
     * @param int    $total
     * @param int    $page
     * @param int    $limit
     * @param int    $page_size
     * @param string $style
     */
    public function paging($uri, $params=array(), $total=0, $page=0, $limit=30, $page_size=8, $style='page_item')
    {
        //Start paging
        $query = '';
        $querystring = '?';
        $paging = '';
        $url = BASE_URL . $uri;
        $total = intval($total);
        $limit = intval($limit);
        $page_size = intval($page_size);
        
        if(strpos($uri, 'http://') !== false)
        {
        	$url = $uri;
        }
      
        if(!empty($params) && is_array($params))
        {
        	unset($params['page']);
        	
        	ksort($params);
            //Add query
            foreach($params as $key => $value)
            {            	
                $querystring .= $key.'='.urlencode($value) .'&';
            }                        
        }               
        
        //Check total
        if($total>0)
        {
            //Current page number
            $total_pages = ceil($total / $limit);
            $start   = max($page - intval($page_size/2), 1);
            $end     = ($total > $limit) ? $start + $page_size -1 : $total;
            
            $from_rs = ($page - 1) * $limit + 1;
            $to_rs   = $from_rs + $limit - 1;
            $to_rs   = ($to_rs>$total) ? $total : $to_rs;
            $paging = '';
            
            if($total > $limit)
            {
            	//Add total
            	$paging  .= '<div class="top-rc clearfix"><span class="minfo fl">Hiển thị '.$from_rs.'-'.($to_rs).' của '.$total.' kết quả thích hợp</span><div class="wrap-pagination"><ul class="pagination clearfix">';
                   	
            	//Previous
            	if($page > 1)
            	{
            		$i = $page-1;
            		$query = $url .$querystring .'page='. $i;
            		$paging .= '<li><a title="Trang trước" href="'.$query.'">&laquo;</a></li>';
            	}
            	                                                    
                //Loop
                for($i = $start; $i <= $end && $i <= $total_pages; $i++)
                {
                	
                    if($i == $page)
                    {                    	
                        $paging .= '<li class="active"><a>'.$i.'</a></li>';
                    }
                    else
                    {
                        $query = $url . $querystring .'page='. $i;
                        $paging .= '<li><a href="'.$query.'">'.$i.'</a></li>';                        
                    }
                }
                                              
                //Check total pages
                if($page < $total_pages)
                {
                	$i = $page + 1;
                	$query = $url . $querystring .'page='. $i;
                	$paging .= '<li><a title="Trang sau" href="'.$query.'">&raquo;</a></li>';
                }
                
                //$paging .= '<p class="right">Có <b>'. $total .'</b> kết quả phù hợp</p>';
                $paging .= '</ul></div><div class="clear"></div></div>';
            }            
        }
        
        return $paging;
    }
    
    /**
     * Sets the view instance.
     *
     * @param  Zend_View_Interface $view View instance
     * @return Zend_View_Helper_Paging
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }
  
}