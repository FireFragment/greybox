<?php
//namespace ResourcesModule;
namespace App\Presenters;

use Nette;
use Drahak\Restful\IResource;
use Drahak\Restful\Application\UI\ResourcePresenter;
use Drahak\Restful\Validation\IValidator;

class BadgePresenter extends ResourcePresenter
{
  /** @var Nette\Database\Context */
  private $database;

  protected $typeMap = array(
    'json' => IResource::JSON,
    'xml' => IResource::XML
  );

  public function __construct(Nette\Database\Context $database)
  {
    $this->database = $database;
  }

  public function validateCreate()
  {
    $input = $this->getInput();
    return $this->validate($input);
  }

  public function actionCreate()
  {
    $input = $this->getInput();
    $badge = $this->database->table('badge')->insert([
      'name' => $input->name,
      'requiredIB' => $input->requiredIB
    ]);
    return $this->resource->badge = $badge;
  }

  public function actionRead($id)
  {
    $badge = $this->database->table('badge')->get($id);
    return $this->resource->badge = $badge;
  }

  public function validateUpdate($id)
  {
    $input = $this->getInput();
    return $this->validate($input);    
  }

  public function actionUpdate($id)
  {
    $input = $this->getInput();
    $badge = $this->database->table('badge')->get($id);
    $badge->update($input);
    return $this->resource->badge = $badge;
  }

  public function actionDelete($id)
  {
    $badge = $this->database->table('badge')->get($id);
    $badge->delete();
    return $this->resource->badge = $badge;
  }

  private function validate($input)
  {
    $input->field('name')
      ->addRule(IValidator::MAX_LENGTH, "Maximum name length is %d.", 30);
    $input->field('requiredIB')
      ->addRule(IValidator::RANGE, "Required IB has to be between %s and %s.", [0, 30000]);    
  }

}