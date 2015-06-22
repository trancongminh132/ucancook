<?php
class My_Zend_Broadcast
{
   const template_shop_created     = 'email_shop_created.htm';
   const template_shop_actived_sms = 'email_shop_actived_sms.htm';
   const template_shop_approved    = 'email_shop_approved.htm';
   const template_shop_verified    = 'email_shop_verified.htm';
   const template_shop_locked      = 'email_shop_locked.htm';
   const template_shop_rejected    = 'email_shop_rejected.htm';
   const template_shop_deleted     = 'email_shop_deleted.htm';
   const template_topic_deleted    = 'email_topic_deleted.htm';   
   
  /**
   * 
   * ShopCreated
   * @param int $sender_id
   * @param int $receiver_id
   */
  public static function ShopCreated($sender_id, $receiver_id)
  {
    $path     = realpath(dirname(__FILE__).'/../../../content');
    $template = $path.'/'.self::template_shop_created;
    $shop     = Shopsearch::selectShop($receiver_id);
        
    $content  = array(
                      '{{username}}'       => $shop['username'],                      
                      '{{base_url}}'       => BASE_URL, 
                      '{{image_url}}'      => IMAGE_URL  
                );
    
    
    $subject    = 'Thông báo từ 123mua.vn: Đã nhận được đơn đăng ký của bạn';
    $body       = self::getBody(array_keys($content), array_values($content), $template);
    if ($body)
    {
      $data     = array(
                        'sender_id'	    => $sender_id,
        				'receiver_id'	=> $receiver_id,
        				'subject'	    => $subject,
        				'content'	    => $body,
        				'sent_date'	    => time(),
        				'is_readed'	    => 0        
                  );
      return $msg_id = Inbox::insertInbox($data);  
    }                                   
    return false;  
  }  
   
  /**
   * 
   * ShopActivedSMS
   * @param int $sender_id
   * @param int $receiver_id
   */
  public static function ShopActivedSMS($sender_id, $receiver_id)
  {
    $path     = realpath(dirname(__FILE__).'/../../../content');
    $template = $path.'/'.self::template_shop_actived_sms;
    $shop     = Shopsearch::selectShop($receiver_id);
        
    $content  = array(
                      '{{username}}'       => $shop['username'], 
                      '{{shopname}}'       => $shop['shop_name'], 
                      '{{productcreate}}'  => BASE_URL.'/myproduct/productcreate',
                      '{{base_url}}'       => BASE_URL, 
                      '{{image_url}}'      => IMAGE_URL, 
                      '{{content_url}}'    => CONTENT_URL  
                );
    
    
    $subject    = 'Thông báo từ 123mua.vn: Kích hoạt SMS thành công';
    $body       = self::getBody(array_keys($content), array_values($content), $template);
    if ($body)
    {
      $data     = array(
                        'sender_id'	    => $sender_id,
        				'receiver_id'	=> $receiver_id,
        				'subject'	    => $subject,
        				'content'	    => $body,
        				'sent_date'	    => time(),
        				'is_readed'	    => 0        
                  );
      return $msg_id = Inbox::insertInbox($data);  
    }                                   
    return false;  
  }
  
  /**
   * 
   * ShopApproved
   * @param int $sender_id
   * @param int $receiver_id
   */
  public static function ShopApproved($sender_id, $receiver_id)
  {
    $path     = realpath(dirname(__FILE__).'/../../../content');
    $template = $path.'/'.self::template_shop_approved;
    $shop     = Shopsearch::selectShop($receiver_id);
        
    $content  = array(
                      '{{username}}'       => $shop['username'], 
                      '{{shopname}}'       => $shop['shop_name'], 
                      '{{productcreate}}'  => BASE_URL.'/myproduct/productcreate',
                      '{{base_url}}'       => BASE_URL, 
                      '{{image_url}}'      => IMAGE_URL, 
                      '{{content_url}}'    => CONTENT_URL  
                );
    
    
    $subject    = 'Thông báo từ 123mua.vn: Đơn đăng ký tạo shop đã được duyệt';
    $body       = self::getBody(array_keys($content), array_values($content), $template);
    if ($body)
    {
      $data     = array(
                        'sender_id'	    => $sender_id,
        				'receiver_id'	=> $receiver_id,
        				'subject'	    => $subject,
        				'content'	    => $body,
        				'sent_date'	    => time(),
        				'is_readed'	    => 0        
                  );
      return $msg_id = Inbox::insertInbox($data);  
    }                                   
    return false;     
  }
  
  /**
   * 
   * ShopLocked
   * @param int $sender_id
   * @param int $receiver_id
   * @param string $reason
   */
  public static function ShopLocked($sender_id, $receiver_id, $reason)
  {
    $path     = realpath(dirname(__FILE__).'/../../../content');
    $template = $path.'/'.self::template_shop_locked;
    $shop     = Shopsearch::selectShop($receiver_id);
        
    $content  = array(
                      '{{username}}'       => $shop['username'], 
                      '{{shopname}}'       => $shop['shop_name'], 
                      '{{reason}}'         => $reason,
                      '{{base_url}}'       => BASE_URL, 
                      '{{image_url}}'      => IMAGE_URL, 
                      '{{content_url}}'    => CONTENT_URL  
                );
    
    
    $subject    = 'Thông báo từ 123mua.vn: Khóa shop thạm thời';
    $body       = self::getBody(array_keys($content), array_values($content), $template);
    if ($body)
    {
      $data     = array(
                        'sender_id'	    => $sender_id,
        				'receiver_id'	=> $receiver_id,
        				'subject'	    => $subject,
        				'content'	    => $body,
        				'sent_date'	    => time(),
        				'is_readed'	    => 0        
                  );
      return $msg_id = Inbox::insertInbox($data);  
    }                                   
    return false; 
  } 
  
  /**
   * 
   * ShopRejected
   * @param int $sender_id
   * @param int $receiver_id
   * @param string $reason
   */
  public static function ShopRejected($sender_id, $receiver_id, $reason)
  {
    $path     = realpath(dirname(__FILE__).'/../../../content');
    $template = $path.'/'.self::template_shop_rejected;
    $shop     = Shopsearch::selectShop($receiver_id);
        
    $content  = array(
                      '{{username}}'       => $shop['username'], 
                      '{{shopname}}'       => $shop['shop_name'], 
                      '{{reason}}'         => $reason,
                      '{{base_url}}'       => BASE_URL, 
                      '{{image_url}}'      => IMAGE_URL, 
                      '{{content_url}}'    => CONTENT_URL  
                );
    
    
    $subject    = 'Thông báo từ 123mua.vn: shop bị từ chối';
    $body       = self::getBody(array_keys($content), array_values($content), $template);
    if ($body)
    {
      $data     = array(
                        'sender_id'	    => $sender_id,
        				'receiver_id'	=> $receiver_id,
        				'subject'	    => $subject,
        				'content'	    => $body,
        				'sent_date'	    => time(),
        				'is_readed'	    => 0        
                  );
      return $msg_id = Inbox::insertInbox($data);  
    }                                   
    return false; 
  }

  /**
   * 
   * ShopDeleted
   * @param int $sender_id
   * @param int $receiver_id
   * @param string $reason
   */
  public static function ShopDeleted($sender_id, $receiver_id, $reason)
  {
    $path     = realpath(dirname(__FILE__).'/../../../content');
    $template = $path.'/'.self::template_shop_deleted;
    $shop     = Shopsearch::selectShop($receiver_id);
        
    $content  = array(
                      '{{username}}'       => $shop['username'], 
                      '{{shopname}}'       => $shop['shop_name'], 
                      '{{reason}}'         => $reason,
                      '{{base_url}}'       => BASE_URL, 
                      '{{image_url}}'      => IMAGE_URL, 
                      '{{content_url}}'    => CONTENT_URL  
                );
    
    
    $subject    = 'Thông báo từ 123mua.vn: Khóa shop vĩnh viễn';
    $body       = self::getBody(array_keys($content), array_values($content), $template);
    if ($body)
    {
      $data     = array(
                        'sender_id'	    => $sender_id,
        				'receiver_id'	=> $receiver_id,
        				'subject'	    => $subject,
        				'content'	    => $body,
        				'sent_date'	    => time(),
        				'is_readed'	    => 0        
                  );
      return $msg_id = Inbox::insertInbox($data);  
    }                                   
    return false; 
  }
  
  /**
   * 
   * TopicDeleted
   * @param int $sender_id
   * @param int $receiver_id
   * @param int $topic_id
   * @param string $reason
   */
  public static function TopicDeleted($sender_id, $receiver_id, $topic_id, $reason)
  {
    $path     = realpath(dirname(__FILE__).'/../../../content');
    $template = $path.'/'.self::template_topic_deleted;
    $shop     = Shopsearch::selectShop($receiver_id);
    $topic    = Topicsearch::selectTopic($topic_id);
        
    $content  = array(
                      '{{username}}'       => $shop['username'], 
                      '{{shopname}}'       => $shop['shop_name'],
                      '{{topicname}}'      => $topic['topic_name'],  
                      '{{reason}}'         => $reason,
                      '{{base_url}}'       => BASE_URL, 
                      '{{image_url}}'      => IMAGE_URL, 
                      '{{content_url}}'    => CONTENT_URL  
                );
    
    
    $subject    = 'Thông báo từ 123mua.vn: Xóa sản phẩm';
    $body       = self::getBody(array_keys($content), array_values($content), $template);
    if ($body)
    {
      $data     = array(
                        'sender_id'	    => $sender_id,
        				'receiver_id'	=> $receiver_id,
        				'subject'	    => $subject,
        				'content'	    => $body,
        				'sent_date'	    => time(),
        				'is_readed'	    => 0        
                  );
      return $msg_id = Inbox::insertInbox($data);  
    }                                   
    return false; 
  }
  
  /**
   * 
   * getBody
   * @param array $keys
   * @param array $values
   * @param string $template
   */
  private static function getBody($keys, $values, $template)
  {
    $body = file_get_contents($template);           
    return str_replace($keys, $values, $body);    
  }
}