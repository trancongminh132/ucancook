<?php
abstract class My_Zend_Session_Adapter_Abstract
{
	/**
	 * Open session
	 * @param string $save_path
	 * @param string $name
	 */
	abstract protected function open($save_path, $name);
	
	/**
	 * Close session
	 */
	abstract protected function close();
	
	/**
	 * Read session
	 * @param string $key
	 */
	abstract protected function read($key);
	
	/**
	 * Write session
	 * @param string $key
	 * @param string $value
	 */
	abstract protected function write($key, $value);
	
	/**
	 * Remove session
	 * @param string $key
	 */
	abstract protected function destroy($key);
	
	/**
	 * garbage collector
	 * @param string $maxlifetime
	 */
	abstract protected function gc($maxlifetime);
}