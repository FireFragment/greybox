<?php

namespace App\Model;

use Nette;

abstract class BaseModel extends \Kdyby\Doctrine\Entities\BaseEntity
{
  use Nette\SmartObject;
  /**
    * @var Nette\Database\Context
    */
  private $database;

  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;
  }
}