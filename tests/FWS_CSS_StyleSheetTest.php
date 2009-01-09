<?php
/**
 * Contains the FWS_CSS_StyleSheet test
 *
 * @version			$Id$
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 * @copyright		2003-2008 Nils Asmussen
 * @link				http://www.script-solution.de
 */

/**
 * FWS_CSS_StyleSheet test case.
 * 
 * @package			FrameWorkSolution
 * @subpackage	tests
 * @author			Nils Asmussen <nils@script-solution.de>
 */
class FWS_CSS_StyleSheetTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var FWS_CSS_StyleSheet
	 */
	private $css;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$css = <<<CSS
@import   url(  "my\"file.css"  )  ;
@imPort "myfile.css" print ;
@chArset "{ut};f8"  ;

p h1 .
blub {
	color:red;
	font-weight: bold
}
@media print,screen, tty {
	p h1#blub {background-color:#123456}
	asd[a] {}
	/* bla */
	b__[a="b"] {}
	b__[a ~= "asd"] {}
	b__ [ a |= "asd" ] {}
	#bla {}
	.abc {}
}
@MEDIA print {
	[asd="bla"] {}
}
input,select,textarea {
	color:green;
}
input { border: 1px; }
input { margin: 2px; }
a:first-child + b > c d:hover {}
/*p {}*/
CSS;
		
		$this->css = new FWS_CSS_StyleSheet($css);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		$this->css = null;
		parent::tearDown();
	}
	
	/**
	 * Tests FWS_CSS_StyleSheet->__toString()
	 */
	public function test__toString()
	{
		$str = $this->css->__toString();
		$this->css = new FWS_CSS_StyleSheet($str);
		$this->testGet_blocks();
		$this->testGet_ruleset_by_name();
		$this->testGet_rulesets();
		$this->testGet_rulesets_by_name();
		$this->testGet_rulesets_for_media();
		$this->testGet_rulesets_with_class();
		$this->testGet_rulesets_with_id();
		$this->testAdd_block();
	}

	/**
	 * Tests FWS_CSS_StyleSheet->add_block()
	 */
	public function testAdd_block()
	{
		$block = new FWS_CSS_Block_Comment('/* huhu, das bin ich */');
		$this->css->add_block($block);
		self::assertEquals(19,count($this->css->get_blocks()));
		self::assertEquals($block,$this->css->get_block(18));
	}
	
	/**
	 * Tests FWS_CSS_StyleSheet->get_blocks()
	 */
	public function testGet_blocks()
	{
		self::assertEquals(18,count($this->css->get_blocks()));
	}

	/**
	 * Tests FWS_CSS_StyleSheet->get_ruleset_by_name()
	 */
	public function testGet_ruleset_by_name()
	{
		// id selector
		$sels = array(new FWS_CSS_Selector_ID('bla'));
		$ruleset = new FWS_CSS_Block_Ruleset($sels,array(),array('print','screen','tty'));
		$sel = $this->css->get_ruleset_by_name($ruleset->get_name())->get_selector(0);
		self::assertEquals('bla',$sel->get_id());
		
		$ruleset->set_media(null);
		self::assertEquals(null,$this->css->get_ruleset_by_name($ruleset->get_name()));
		
		// connector & class-selector
		$sels = array(new FWS_CSS_Selector_Connector(
			new FWS_CSS_Selector_Type('p'),
			FWS_CSS_Selector_Connector::CON_ANY_CHILD,
			new FWS_CSS_Selector_Class('blub','h1')
		));
		$ruleset = new FWS_CSS_Block_Ruleset($sels);
		$sel = $this->css->get_ruleset_by_name($ruleset->get_name())->get_selector(0);
		self::assertEquals('blub',$sel->get_right_selector()->get_class());
		self::assertEquals('h1',$sel->get_right_selector()->get_tagname());
		self::assertEquals('p',$sel->get_left_selector()->get_tagname());
		
		// attribute exists
		$sels = array(new FWS_CSS_Selector_Attribute('a',FWS_CSS_Selector_Attribute::OP_EXIST,null,'asd'));
		$ruleset = new FWS_CSS_Block_Ruleset($sels,array(),array('print','screen','tty'));
		$sel = $this->css->get_ruleset_by_name($ruleset->get_name())->get_selector(0);
		self::assertEquals('asd',$sel->get_tagname());
		self::assertEquals('a',$sel->get_attribute_name());
		self::assertEquals(FWS_CSS_Selector_Attribute::OP_EXIST,$sel->get_attribute_op());
		self::assertEquals(null,$sel->get_attribute_value());
		
		// attribute value exact
		$ruleset->get_selector(0)->set_tagname('b__');
		$ruleset->get_selector(0)->set_attribute_op(FWS_CSS_Selector_Attribute::OP_EQ);
		$ruleset->get_selector(0)->set_attribute_value('b');
		$sel = $this->css->get_ruleset_by_name($ruleset->get_name())->get_selector(0);
		self::assertEquals('b__',$sel->get_tagname());
		self::assertEquals('a',$sel->get_attribute_name());
		self::assertEquals(FWS_CSS_Selector_Attribute::OP_EQ,$sel->get_attribute_op());
		self::assertEquals('b',$sel->get_attribute_value());
		
		// attribute in set separated by space (b__[a ~= "asd"])
		$sels = array(new FWS_CSS_Selector_Attribute('a',FWS_CSS_Selector_Attribute::OP_IN_SET,'asd','b__'));
		$ruleset = new FWS_CSS_Block_Ruleset($sels,array(),array('print','screen','tty'));
		$sel = $this->css->get_ruleset_by_name($ruleset->get_name())->get_selector(0);
		self::assertEquals('b__',$sel->get_tagname());
		self::assertEquals('a',$sel->get_attribute_name());
		self::assertEquals(FWS_CSS_Selector_Attribute::OP_IN_SET,$sel->get_attribute_op());
		self::assertEquals('asd',$sel->get_attribute_value());
		
		// attribute in set separated by '-' (b__ [ a |= "asd" ])
		$sels = array(new FWS_CSS_Selector_Attribute('a',FWS_CSS_Selector_Attribute::OP_IN_HSET,'asd','b__'));
		$ruleset = new FWS_CSS_Block_Ruleset($sels,array(),array('print','screen','tty'));
		$sel = $this->css->get_ruleset_by_name($ruleset->get_name())->get_selector(0);
		self::assertEquals('b__',$sel->get_tagname());
		self::assertEquals('a',$sel->get_attribute_name());
		self::assertEquals(FWS_CSS_Selector_Attribute::OP_IN_HSET,$sel->get_attribute_op());
		self::assertEquals('asd',$sel->get_attribute_value());
		
		// pseudo & connectors (a:first-child + b > c d:hover)
		$sels = array(
			new FWS_CSS_Selector_Connector(
				new FWS_CSS_Selector_Pseudo(new FWS_CSS_Selector_Type('a'),'first-child'),
				FWS_CSS_Selector_Connector::CON_NEXT_SIB,
				new FWS_CSS_Selector_Connector(
					new FWS_CSS_Selector_Type('b'),
					FWS_CSS_Selector_Connector::CON_NEXT_CHILD,
					new FWS_CSS_Selector_Connector(
						new FWS_CSS_Selector_Type('c'),
						FWS_CSS_Selector_Connector::CON_ANY_CHILD,
						new FWS_CSS_Selector_Pseudo(new FWS_CSS_Selector_Type('d'),'hover')
					)
				)
			)
		);
		$ruleset = new FWS_CSS_Block_Ruleset($sels);
		self::assertNotEquals(null,$this->css->get_ruleset_by_name($ruleset->get_name()));
	}

	/**
	 * Tests FWS_CSS_StyleSheet->get_rulesets()
	 */
	public function testGet_rulesets()
	{
		$rulesets = $this->css->get_rulesets(array(0,1,2,3));

		self::assertEquals('my\\"file.css',$rulesets[0]->get_uri());
		
		self::assertEquals('myfile.css',$rulesets[1]->get_uri());
		self::assertEquals(array('print'),$rulesets[1]->get_media());
		
		self::assertEquals('{ut};f8',$rulesets[2]->get_charset());
		
		self::assertEquals('p',$rulesets[3]->get_selector(0)->get_left_selector()->get_tagname());
		self::assertEquals('h1',$rulesets[3]->get_selector(0)->get_right_selector()->get_tagname());
		self::assertEquals('blub',$rulesets[3]->get_selector(0)->get_right_selector()->get_class());
		self::assertEquals('red',$rulesets[3]->get_property('color'));
	}

	/**
	 * Tests FWS_CSS_StyleSheet->get_rulesets_by_name()
	 */
	public function testGet_rulesets_by_name()
	{
		$sel = new FWS_CSS_Selector_Class('abc');
		$ruleset = new FWS_CSS_Block_Ruleset(array($sel),array(),array('print','screen','tty'));
		
		$res = $this->css->get_rulesets_by_name($ruleset->get_name());
		self::assertEquals(1,count($res));
		self::assertEquals('abc',$res[0]->get_selector(0)->get_class());
	}

	/**
	 * Tests FWS_CSS_StyleSheet->get_rulesets_for_media()
	 */
	public function testGet_rulesets_for_media()
	{
		self::assertEquals(5,count($this->css->get_rulesets_for_media(null)));
		self::assertEquals(8,count($this->css->get_rulesets_for_media('print')));
		self::assertEquals(7,count($this->css->get_rulesets_for_media('tty')));
		self::assertEquals(0,count($this->css->get_rulesets_for_media('bla')));
	}

	/**
	 * Tests FWS_CSS_StyleSheet->get_rulesets_for_class()
	 */
	public function testGet_rulesets_with_class()
	{
		self::assertEquals(1,count($this->css->get_rulesets_for_class('blub')));
		self::assertEquals(0,count($this->css->get_rulesets_for_class('fantasy')));
	}
	
	/**
	 * Tests FWS_CSS_StyleSheet->get_rulesets_for_tagname()
	 */
	public function testGet_rulesets_for_tagname()
	{
		self::assertEquals(3,count($this->css->get_rulesets_for_tagname('input')));
		self::assertEquals(1,count($this->css->get_rulesets_for_tagname('select')));
		self::assertEquals(2,count($this->css->get_rulesets_for_tagname('p')));
	}

	/**
	 * Tests FWS_CSS_StyleSheet->get_rulesets_for_id()
	 */
	public function testGet_rulesets_with_id()
	{
		self::assertEquals(1,count($this->css->get_rulesets_for_id('blub')));
		self::assertEquals(0,count($this->css->get_rulesets_for_id('fantasy')));
	}

	/**
	 * Tests FWS_CSS_StyleSheet->remove_blocks()
	 */
	public function testRemove_blocks()
	{
		$this->css->remove_blocks(array());
		self::assertEquals(18,count($this->css->get_blocks()));
		
		$this->css->remove_blocks(array(0,1,2));
		self::assertEquals(15,count($this->css->get_blocks()));
		
		$this->css->remove_blocks(array(0,1,2));
		self::assertEquals(12,count($this->css->get_blocks()));
		
		$this->css->remove_blocks(array(0,1,2,3,4,5,6,7,8,9,10,11));
		self::assertEquals(0,count($this->css->get_blocks()));
	}
}
?>