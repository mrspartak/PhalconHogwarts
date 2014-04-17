<?
namespace Prepare;

class String {
	
	private $_string = '';
	
	public function __construct($string) {
		$this->_string = (string) $string;	
	}
	
	public function trim() {
		$this->_string = trim($this->_string, chr(0xC2).chr(0xA0));
		return $this;
	}
	
	public function get() {
		return $this->_string;
	}
}