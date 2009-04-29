<?php

class SubmitButton
{
	private $caption;

	public function __construct($caption = false)
	{
		if ($caption !== false)
		{
			$this->caption = $caption;
		}
		else
		{
			$this->caption = 'Submit';
		}
	}

	public function ToString()
	{
		return '<input type="submit" value="' . $this->caption . '">';
	}
}
