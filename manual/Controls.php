<?php

class Input
{
	private $name;
	private $label;

	public function __construct($label, $name)
	{
		$this->name = $name;
		$this->label = $label;
	}

	public function ToString()
	{
		$html = $this->label;
		$html .= '<input type="text" name="' . $this->name . '" />';

		return $html;
	}
}

class Hidden
{
	private $name;
	private $value;

	public function __construct($name, $value)
	{
		$this->name = $name;
		$this->value = $value;
	}

	public function ToString()
	{
		$html = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';

		return $html;
	}
}

class DropDown
{
	private $name;
	private $contents;

	function __construct($name, $contents)
	{
		$this->name = $name;
		$this->contents = $contents;
	}

	function Write()
	{
		print $this->ToString();
	}

	function ToString()
	{
		$result = '<select name="' . $this->name . '">';

		foreach ($this->contents as $item)
		{
			$result .= '<option value="' . $item->GetID() . '">';
			$result .= $item->ToString() . '</option>' . "\n";
		}

		$result .= '</select>';

		return $result;
	}
}

?>
