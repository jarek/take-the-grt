<?php

include_once 'headers.php';

class Form
{
	private $contents;
	private $method = false;
	private $destinationPage = false;

	private $acceptedElements = array('Input', 'DropDown', 'SubmitButton',
		'Hidden');

	public function __construct($action=false,$method=false)
	{
		if ($action !== false)
		{
			$this->destinationPage = $action;
		}

		if ($method !== false)
		{
			$this->method = $method;
		}
	}

	public function Add($formElement)
	{
		if (in_array(get_class($formElement), $this->acceptedElements))
		{
			$this->contents[] = $formElement;
		}
		else
		{
			throw new Exception('Attempted to add invalid form element to form');
		}
	}

	public function ToString()
	{
		if ($this->destinationPage)
		{
			$html = '<form action="' . $this->destinationPage . '"';
		}
		else
		{
			$html = '<form';
		}

		if ($this->method)
		{
			$html .= ' method="' . $this->method . '" >' . "\n";
		}
		else
		{
			$html .= ' method="get">' . "\n";
		}

		foreach ($this->contents as $formItem)
		{
			$html .= $formItem->ToString() . "\n";
		}

		$html .= '</form>';

		return $html;
	}
}

?>
