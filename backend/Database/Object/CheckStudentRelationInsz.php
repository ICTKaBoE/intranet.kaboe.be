<?php

namespace Database\Object;

use Database\Interface\CustomObject;
use Helpers\Icon;
use Ouzo\Utilities\Strings;
use Security\Input;

class CheckStudentRelationInsz extends CustomObject
{
	protected $objectAttributes = [
		"id",
		"checkField",
		"informatStudentId",
		"informatInstituteNumber",
		"school",
		"class",
		"childName",
		"childInsz",
		"motherName",
		"motherInsz",
		"fatherName",
		"fatherInsz",
		"insertDateTime",
		"locked",
		"published",
		"deleted"
	];

	public function init()
	{
		$this->childInsz = Input::formatInsz($this->childInsz);
		$this->motherInsz = Input::formatInsz($this->motherInsz);
		$this->fatherInsz = Input::formatInsz($this->fatherInsz);
		$this->locked = Input::convertToBool($this->locked);
		$this->published = Input::convertToBool($this->published);

		$this->classOnly = explode(" van ", $this->class)[0];
		$this->classOnly = explode(" (", $this->classOnly)[0];

		$this->formLocked = $this->locked || $this->published;
		$this->foundInInformat = !Strings::equal($this->informatStudentId, 0);

		if (strlen($this->informatInstituteNumber) == 5) $this->informatInstituteNumber = "0{$this->informatInstituteNumber}";
	}

	public function check()
	{
		$this->childInszIsCorrect = Input::check($this->childInsz, Input::INPUT_TYPE_INSZ);
		$this->motherInszIsCorrect = Input::check($this->motherInsz, Input::INPUT_TYPE_INSZ);
		$this->fatherInszIsCorrect = Input::check($this->fatherInsz, Input::INPUT_TYPE_INSZ);

		$this->childInszIsCorrectIcon = Input::empty($this->childInsz) ? "" : Icon::load($this->childInszIsCorrect ? "check" : "x");
		$this->motherInszIsCorrectIcon = Input::empty($this->motherInsz) ? "" : Icon::load($this->motherInszIsCorrect ? "check" : "x");
		$this->fatherInszIsCorrectIcon = Input::empty($this->fatherInsz) ? "" : Icon::load($this->fatherInszIsCorrect ? "check" : "x");
		$this->lockedIcon = Icon::load("thumb-" . ($this->locked ? "up" : "down"));
		$this->foundInInformatIcon = Icon::load("thumb-" . ($this->foundInInformat ? "up" : "down"));

		if (!$this->foundInInformat) $this->foundInInformatError = "Geen leerling gevonden in Informat met INSZ '{$this->childInsz}'!";
		if (!$this->childInszIsCorrect) $this->childInszIsCorrectError = "Formaat/aantal cijfers INSZ '{$this->childInsz}' is verkeerd!";
		if (!$this->motherInszIsCorrect) $this->motherInszIsCorrectError = "Formaat/aantal cijfers INSZ '{$this->motherInsz}' is verkeerd!";
		if (!$this->fatherInszIsCorrect) $this->fatherInszIsCorrectError = "Formaat/aantal cijfers INSZ '{$this->fatherInsz}' is verkeerd!";

		if (!$this->foundInInformat || !$this->childInszIsCorrect || !$this->motherInszIsCorrect || !$this->fatherInszIsCorrect) $this->generalError = "Er bevinden zich 1 of meerdere fouten in deze rij!";
	}
}
