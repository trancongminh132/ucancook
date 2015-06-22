<?php
class My_Helper_Pagingadmin
{
	/**
	 * View instance
	 *
	 * @var Zend_View_Instance
	 */
	public $view;
	
	/**
	 * Paging
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @param array  $params
	 * @param int    $total
	 * @param int    $offset
	 * @param int    $limit
	 * @param int    $page_size
	 * @param string $style
	 */
	public function pagingadmin($module, $controller, $action, $params, $total=0, $offset=0, $limit=30, $page_size=8, $style='page_item')
	{
		//Start paging
		$query = '';
		$querystring = '';
		$paging = '';
	
		$total = intval($total);
		$limit = intval($limit);
		$page_size = intval($page_size);
	
		if(!empty($params) && is_array($params))
		{
			//Add query
			foreach($params as $key => $value)
			{
				$querystring .= '/'.$key.'/'.urlencode($value);
			}
		}
	
		if(!empty($action)){
			$action = '/' . $action;
		}
	
		//Check total
		if($total>0)
		{
			//Current page number
			$total_pages = ceil($total/$limit);
			$start   = max($offset-intval($page_size/2), 1);
			$end     = ($total>$limit)? $start + $page_size -1 : $total;
	
			$from_rs = ($offset-1)*$limit+1;
			$to_rs   = $from_rs+$limit-1;
			$to_rs   = ($to_rs>$total) ? $total : $to_rs;
			//Add total
			$paging  = '<div class="paging grey '.$style.'"><div class="wrap">';
			$paging  .= '<div class="total">Có <b>'.$total.'</b> kết quả thích hợp </div>';
			$paging  .= '<div class="pageOn"><ul>';
			if($total>$limit)
			{
				//Start
				if($start > 1)
				{
					$i = 1;
					$query = BASE_URL.'/adm/'.$controller.$action.'/page/'.$i.$querystring;
					$paging .= '<li class="first"><a href="'.$query.'">'.$i.'</a></li>';
				}
	
				//Previous
				if($offset > 1)
				{
					$i = $offset-1;
					$query = BASE_URL.'/adm/'.$controller.$action.'/page/'.$i.$querystring;
					$paging .= '<li class="prev"><a href="'.$query.'"></a></li>';
				}
	
				//Loop
				for($i = $start; $i <= $end && $i <= $total_pages; $i++)
				{
				 
				if($i == $offset)
					{
						$paging .= '<li class="active"><a>'.$i.'</a></li>';
						}
							else
								{
									$query = BASE_URL.'/adm/'.$controller.$action.'/page/'.$i.$querystring;
									$paging .= '<li><a href="'.$query.'">'.$i.'</a></li>';
									}
									}
	
										//Check total pages
										if($offset < $total_pages)
										{
										$i = $offset + 1;
												$query = BASE_URL.'/adm/'.$controller.$action.'/page/'.$i.$querystring;
										$paging .= '<li class="next"><a href="'.$query.'"></a></li>';
			}
	
			//Check total pages
			if($total_pages > $end)
			{
				$i = $total_pages;
				$query = BASE_URL.'/adm/'.$controller.$action.'/page/'.$i.$querystring;
				$paging .= '<li class="last"><a href="'.$query.'">'.$i.'</a></li>';
			}
		}
        $paging .= '</ul></div></div></div>';
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